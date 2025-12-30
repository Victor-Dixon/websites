"""
Tests for TBOW Tactics Automation System

Run with: pytest tests/test_tbow_tactics.py -v
"""

import json
import pytest
from datetime import datetime, timezone
from pathlib import Path
import tempfile
import sys

# Add src to path
sys.path.insert(0, str(Path(__file__).parent.parent / "src"))

from tbow_tactics.models import (
    Signal, Result, TradePlan, WeeklyRecap,
    Levels, Rules, PostConfig,
    Bias, Outcome, PostType,
)
from tbow_tactics.signal_engine import (
    SignalEngine, OHLCV, build_indicators, 
    calculate_sma, calculate_rsi, calculate_atr,
)
from tbow_tactics.risk_engine import RiskEngine, RiskLimits, ValidationResult
from tbow_tactics.plan_composer import PlanComposer, ComposedPost
from tbow_tactics.publisher import (
    StocktwitsPublisher, RateLimiter, DeduplicationStore,
    PublishResult,
)
from tbow_tactics.ledger import Ledger, JSONLedger


# ═══════════════════════════════════════════════════════════════════════════
# MODEL TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestModels:
    """Test data models."""
    
    def test_levels_creation(self):
        """Test Levels dataclass."""
        levels = Levels(
            entry_zone=(487.5, 489.5),
            invalidation=484.9,
            targets=[495.0, 503.0, 512.0],
        )
        
        assert levels.entry_mid == 488.5
        assert levels.stop_distance == pytest.approx(3.6)
        assert levels.risk_reward_ratio(0) == pytest.approx(1.806, rel=0.01)
    
    def test_signal_creation(self):
        """Test Signal creation and serialization."""
        signal = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels(
                entry_zone=(487.5, 489.5),
                invalidation=484.9,
                targets=[495.0, 503.0, 512.0],
            ),
            rules=Rules(
                trigger="break_and_hold_above_entry_zone",
                risk_per_trade_r=0.5,
            ),
            context=["above_50SMA", "HTF_support"],
        )
        
        assert signal.ticker == "TSLA"
        assert signal.bias == Bias.BULLISH
        assert "TBOW-TSLA" in signal.plan_id
        
        # Test serialization
        json_str = signal.to_json()
        assert "TSLA" in json_str
        assert "VWAP_Reclaim_Continuation" in json_str
        
        # Test deserialization
        signal2 = Signal.from_json(json_str)
        assert signal2.ticker == signal.ticker
        assert signal2.levels.invalidation == signal.levels.invalidation
    
    def test_signal_dedupe_hash(self):
        """Test dedupe hash is consistent for same signal."""
        signal1 = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((487.5, 489.5), 484.9, [495.0]),
        )
        
        signal2 = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((487.5, 489.5), 484.9, [495.0]),
        )
        
        assert signal1.dedupe_hash() == signal2.dedupe_hash()
        
        # Different levels = different hash
        signal3 = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((490.0, 492.0), 487.0, [500.0]),
        )
        
        assert signal1.dedupe_hash() != signal3.dedupe_hash()
    
    def test_trade_plan_lifecycle(self):
        """Test TradePlan creation and resolution."""
        signal = Signal(
            ticker="AAPL",
            timeframe="1H",
            setup="Breakout_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((195.0, 196.0), 193.0, [200.0, 205.0]),
        )
        
        plan = TradePlan(signal=signal)
        assert not plan.is_resolved
        assert plan.result is None
        
        # Resolve the plan
        result = plan.resolve(
            outcome=Outcome.TARGET1,
            result_r=1.5,
            notes="Clean breakout, took partials at T1",
        )
        
        assert plan.is_resolved
        assert plan.result.result_r == 1.5
        assert plan.result.outcome == Outcome.TARGET1
    
    def test_weekly_recap(self):
        """Test weekly recap calculations."""
        recap = WeeklyRecap(
            week_start="2025-12-22",
            week_end="2025-12-28",
            total_trades=7,
            wins=4,
            losses=3,
            total_r=2.1,
        )
        
        assert recap.win_rate == pytest.approx(57.14, rel=0.1)
        assert recap.expectancy == pytest.approx(0.3, rel=0.1)


# ═══════════════════════════════════════════════════════════════════════════
# SIGNAL ENGINE TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestSignalEngine:
    """Test signal generation."""
    
    def test_indicator_calculations(self):
        """Test indicator calculations."""
        closes = [100, 101, 102, 101, 103, 104, 102, 105, 106, 107]
        
        sma = calculate_sma(closes, 5)
        assert sma == pytest.approx(104.8)
        
        rsi = calculate_rsi(closes, 5)
        assert 0 <= rsi <= 100
    
    def test_manual_signal_creation(self):
        """Test creating a manual signal."""
        engine = SignalEngine()
        
        signal = engine.generate_manual_signal(
            ticker="tsla",  # lowercase to test normalization
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            entry_zone=(487.5, 489.5),
            invalidation=484.9,
            targets=[495.0, 503.0, 512.0],
            context=["above_50SMA"],
        )
        
        assert signal.ticker == "TSLA"  # Should be uppercase
        assert signal.timeframe == "4H"
        assert len(signal.levels.targets) == 3
    
    def test_signal_with_candle_data(self):
        """Test signal generation from candle data."""
        engine = SignalEngine()
        
        # Create mock candle data
        candles = []
        base_time = datetime(2025, 12, 29, 10, 0, 0, tzinfo=timezone.utc)
        
        for i in range(50):
            candles.append(OHLCV(
                timestamp=base_time,
                open=100 + i * 0.1,
                high=101 + i * 0.1,
                low=99 + i * 0.1,
                close=100.5 + i * 0.1,
                volume=1000000 + i * 10000,
            ))
        
        # Signal may or may not be generated depending on conditions
        signal = engine.generate_signal(
            ticker="TEST",
            timeframe="5m",
            candles=candles,
        )
        
        # If signal generated, verify structure
        if signal:
            assert signal.ticker == "TEST"
            assert signal.levels.entry_zone[0] > 0


# ═══════════════════════════════════════════════════════════════════════════
# RISK ENGINE TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestRiskEngine:
    """Test risk validation."""
    
    def test_valid_signal_passes(self):
        """Test that a valid signal passes validation."""
        signal = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((487.5, 489.5), 484.9, [495.0, 503.0]),
            rules=Rules("break_and_hold", 0.5, 3),
        )
        
        engine = RiskEngine()
        result = engine.validate(signal)
        
        assert result.valid
        assert result.rejection_reasons is None
    
    def test_missing_targets_fails(self):
        """Test that missing targets fails validation."""
        signal = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((487.5, 489.5), 484.9, []),  # Empty targets
            rules=Rules("break_and_hold", 0.5, 3),
        )
        
        engine = RiskEngine()
        result = engine.validate(signal)
        
        assert not result.valid
        assert any(r.value == "no_targets_defined" for r in result.rejection_reasons)
    
    def test_invalid_entry_zone_fails(self):
        """Test that invalid entry zone fails validation."""
        signal = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((0, 0), 484.9, [495.0]),  # Invalid zone
            rules=Rules("break_and_hold", 0.5, 3),
        )
        
        engine = RiskEngine()
        result = engine.validate(signal)
        
        assert not result.valid
    
    def test_max_trades_exceeded(self):
        """Test daily trade limit check."""
        signal = Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((487.5, 489.5), 484.9, [495.0]),
            rules=Rules("break_and_hold", 0.5, 3),
        )
        
        engine = RiskEngine(current_daily_trades=5)  # Already at limit
        result = engine.validate(signal)
        
        assert not result.valid
        assert any(r.value == "max_daily_trades_exceeded" for r in result.rejection_reasons)


# ═══════════════════════════════════════════════════════════════════════════
# PLAN COMPOSER TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestPlanComposer:
    """Test post composition."""
    
    @pytest.fixture
    def sample_signal(self):
        return Signal(
            ticker="TSLA",
            timeframe="4H",
            setup="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            levels=Levels((487.5, 489.5), 484.9, [495.0, 503.0, 512.0]),
            rules=Rules("break_and_hold_above_entry_zone", 0.5, 3),
        )
    
    def test_compose_plan_post(self, sample_signal):
        """Test composing a plan post."""
        composer = PlanComposer()
        post = composer.compose_plan(sample_signal)
        
        assert post.post_type == PostType.PLAN
        assert "$TSLA" in post.content
        assert "487.50" in post.content
        assert "484.90" in post.content
        assert "495.00" in post.content
        assert post.is_valid_length  # Under 1000 chars
    
    def test_compose_stopout_post(self, sample_signal):
        """Test composing a stop-out post."""
        result = Result(
            plan_id=sample_signal.plan_id,
            outcome=Outcome.STOPPED_OUT,
            result_r=-0.5,
        )
        
        composer = PlanComposer()
        post = composer.compose_stopout(sample_signal, result)
        
        assert post.post_type == PostType.STOPOUT
        assert "$TSLA" in post.content
        assert "-0.5R" in post.content
        assert "Stop-out" in post.content
    
    def test_compose_weekly_recap(self):
        """Test composing a weekly recap."""
        recap = WeeklyRecap(
            week_start="2025-12-22",
            week_end="2025-12-28",
            total_trades=7,
            wins=4,
            losses=3,
            total_r=2.1,
            best_setup="VWAP_Reclaim_Continuation",
            worst_mistake="Overtrading during chop",
        )
        
        composer = PlanComposer()
        post = composer.compose_weekly_recap(recap)
        
        assert "57%" in post.content  # Win rate
        assert "+2.1R" in post.content
        assert "7" in post.content


# ═══════════════════════════════════════════════════════════════════════════
# PUBLISHER TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestPublisher:
    """Test publishing functionality."""
    
    def test_rate_limiter(self):
        """Test rate limiting."""
        with tempfile.TemporaryDirectory() as tmpdir:
            limiter = RateLimiter(
                max_per_hour=2,
                max_per_day=5,
                cooldown_seconds=1,
                state_file=Path(tmpdir) / "rate_limit.json",
            )
            
            # Should be able to post initially
            can, reason = limiter.can_post()
            assert can
            
            # Record posts
            limiter.record_post()
            limiter.record_post()
            
            # Should be rate limited now
            can, reason = limiter.can_post()
            assert not can
            assert "Hourly limit" in reason
    
    def test_deduplication(self):
        """Test deduplication store."""
        with tempfile.TemporaryDirectory() as tmpdir:
            store = DeduplicationStore(
                window_hours=24,
                store_file=Path(tmpdir) / "dedupe.json",
            )
            
            signal = Signal(
                ticker="TSLA",
                timeframe="4H",
                setup="VWAP_Reclaim_Continuation",
                bias=Bias.BULLISH,
                levels=Levels((487.5, 489.5), 484.9, [495.0]),
            )
            
            # Not duplicate initially
            assert not store.is_duplicate(signal)
            
            # Record it
            store.record(signal)
            
            # Now it's a duplicate
            assert store.is_duplicate(signal)
    
    def test_draft_mode_saves_file(self):
        """Test that draft mode saves to file instead of posting."""
        with tempfile.TemporaryDirectory() as tmpdir:
            publisher = StocktwitsPublisher(
                draft_mode=True,
                draft_dir=Path(tmpdir) / "drafts",
            )
            
            signal = Signal(
                ticker="TSLA",
                timeframe="4H",
                setup="VWAP_Reclaim_Continuation",
                bias=Bias.BULLISH,
                levels=Levels((487.5, 489.5), 484.9, [495.0]),
            )
            
            response = publisher.publish_signal(signal)
            
            assert response.result == PublishResult.DRAFT_MODE
            assert "Draft saved" in response.message
            
            # Check draft file exists
            drafts = list((Path(tmpdir) / "drafts").glob("*.txt"))
            assert len(drafts) == 1


# ═══════════════════════════════════════════════════════════════════════════
# LEDGER TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestLedger:
    """Test ledger storage and retrieval."""
    
    def test_json_ledger_save_and_load(self):
        """Test saving and loading from JSON ledger."""
        with tempfile.TemporaryDirectory() as tmpdir:
            ledger = JSONLedger(Path(tmpdir) / "plans")
            
            signal = Signal(
                ticker="TSLA",
                timeframe="4H",
                setup="VWAP_Reclaim_Continuation",
                bias=Bias.BULLISH,
                levels=Levels((487.5, 489.5), 484.9, [495.0]),
            )
            
            plan = TradePlan(signal=signal)
            ledger.save(plan)
            
            # Load it back
            loaded = ledger.load(plan.plan_id)
            
            assert loaded is not None
            assert loaded.signal.ticker == "TSLA"
            assert loaded.signal.levels.invalidation == 484.9
    
    def test_ledger_resolve_trade(self):
        """Test resolving a trade in the ledger."""
        with tempfile.TemporaryDirectory() as tmpdir:
            ledger = Ledger(use_sqlite=False, ledger_dir=Path(tmpdir))
            
            signal = Signal(
                ticker="AAPL",
                timeframe="1H",
                setup="Breakout_Continuation",
                bias=Bias.BULLISH,
                levels=Levels((195.0, 196.0), 193.0, [200.0]),
            )
            
            plan = ledger.save_signal(signal)
            
            # Resolve it
            resolved = ledger.resolve(
                plan_id=plan.plan_id,
                outcome=Outcome.TARGET1,
                result_r=1.2,
                notes="Clean trade",
            )
            
            assert resolved is not None
            assert resolved.is_resolved
            assert resolved.result.result_r == 1.2
    
    def test_ledger_get_pending_and_resolved(self):
        """Test filtering pending vs resolved plans."""
        with tempfile.TemporaryDirectory() as tmpdir:
            ledger = Ledger(use_sqlite=False, ledger_dir=Path(tmpdir))
            
            # Create two plans
            signal1 = Signal(
                ticker="TSLA", timeframe="4H",
                setup="VWAP_Reclaim_Continuation", bias=Bias.BULLISH,
                levels=Levels((487.5, 489.5), 484.9, [495.0]),
            )
            signal2 = Signal(
                ticker="AAPL", timeframe="1H",
                setup="Breakout_Continuation", bias=Bias.BULLISH,
                levels=Levels((195.0, 196.0), 193.0, [200.0]),
            )
            
            plan1 = ledger.save_signal(signal1)
            plan2 = ledger.save_signal(signal2)
            
            # Resolve one
            ledger.resolve(plan2.plan_id, Outcome.TARGET1, 1.0)
            
            # Check filters
            pending = ledger.get_pending()
            resolved = ledger.get_resolved()
            
            assert len(pending) == 1
            assert len(resolved) == 1
            assert pending[0].signal.ticker == "TSLA"
            assert resolved[0].signal.ticker == "AAPL"


# ═══════════════════════════════════════════════════════════════════════════
# INTEGRATION TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestIntegration:
    """End-to-end integration tests."""
    
    def test_full_pipeline(self):
        """Test the full signal -> plan -> publish -> resolve pipeline."""
        with tempfile.TemporaryDirectory() as tmpdir:
            tmppath = Path(tmpdir)
            
            # 1. Create signal
            engine = SignalEngine()
            signal = engine.generate_manual_signal(
                ticker="TSLA",
                timeframe="4H",
                setup="VWAP_Reclaim_Continuation",
                bias=Bias.BULLISH,
                entry_zone=(487.5, 489.5),
                invalidation=484.9,
                targets=[495.0, 503.0, 512.0],
            )
            
            # 2. Validate
            risk_engine = RiskEngine()
            validation = risk_engine.validate(signal)
            assert validation.valid
            
            # 3. Save to ledger
            ledger = Ledger(use_sqlite=False, ledger_dir=tmppath / "ledger")
            plan = ledger.save_signal(signal)
            
            # 4. Compose post
            composer = PlanComposer()
            post = composer.compose_plan(signal)
            assert post.is_valid_length
            
            # 5. Publish (draft mode)
            publisher = StocktwitsPublisher(
                draft_mode=True,
                draft_dir=tmppath / "drafts",
            )
            response = publisher.publish_signal(signal)
            assert response.result == PublishResult.DRAFT_MODE
            
            # 6. Resolve trade
            resolved = ledger.resolve(
                plan_id=plan.plan_id,
                outcome=Outcome.TARGET1,
                result_r=1.4,
                notes="Followed stop. Took partials at T1.",
            )
            assert resolved.is_resolved
            
            # 7. Generate exit post
            exit_post = composer.compose_target_hit(signal, resolved.result)
            assert "+1.4R" in exit_post.content
            
            # 8. Generate recap
            recap = ledger.generate_weekly_recap()
            assert recap.total_trades == 1
            assert recap.wins == 1


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
