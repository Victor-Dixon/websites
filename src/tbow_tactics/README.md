# TBOW Tactics Automation System

**FreeRideInvestor** | Signal → Risk → Plan → Publish → Audit

Automates TA trade plans with deterministic rules, R-based outcome tracking, and consistent Stocktwits publishing.

---

## 🎯 Core Principles (Non-Negotiables)

1. **Every call includes**: Entry zone, Stop/Invalidation, Targets, Timeframe, Setup
2. **Every outcome logged in R** (not dollars)
3. **Post exits + stop-outs** with same visibility as winners
4. **No "signals service" language** — frame as education + playbook
5. **No deletes, no hindsight edits**
6. **Rate-limited posting** to avoid Stocktwits bans

---

## 📦 Installation

```bash
# From workspace root
cd /workspace

# Install dependencies (if not already)
pip install pytest

# Run tests to verify
pytest tests/test_tbow_tactics.py -v
```

---

## 🚀 Quick Start

### 1. Generate a Signal (CLI)

```bash
python -m src.tbow_tactics.cli signal TSLA \
    --timeframe 4H \
    --setup VWAP_Reclaim_Continuation \
    --bias bullish \
    --entry 487.5-489.5 \
    --invalidation 484.9 \
    --targets 495,503,512 \
    --compose
```

### 2. Generate a Signal (Python)

```python
from src.tbow_tactics import SignalEngine, Bias

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

print(signal.to_json())
```

### 3. Validate Through Risk Engine

```python
from src.tbow_tactics import RiskEngine

risk = RiskEngine()
result = risk.validate(signal)

if result.valid:
    print("✓ Signal passed validation")
else:
    print(f"✗ Rejected: {[r.value for r in result.rejection_reasons]}")
```

### 4. Compose Stocktwits Post

```python
from src.tbow_tactics import PlanComposer

composer = PlanComposer()
post = composer.compose_plan(signal)

print(post.content)
# $TSLA TBOW Tactics — Vwap Reclaim Continuation (TF: 4H)
# Bias: Bullish if reclaim holds.
# ...
```

### 5. Publish (Draft Mode)

```python
from src.tbow_tactics import StocktwitsPublisher

publisher = StocktwitsPublisher(draft_mode=True)
response = publisher.publish_signal(signal)

print(f"Result: {response.result.value}")
# Result: draft_mode
```

### 6. Log to Ledger

```python
from src.tbow_tactics import Ledger, Outcome

ledger = Ledger()

# Save signal
plan = ledger.save_signal(signal)

# Later: Resolve with outcome
ledger.resolve(
    plan_id=plan.plan_id,
    outcome=Outcome.TARGET1,
    result_r=1.4,
    notes="Followed stop. Took partials at T1.",
)
```

### 7. Generate Weekly Recap

```python
recap = ledger.generate_weekly_recap()
print(f"Win Rate: {recap.win_rate:.0f}%")
print(f"Net R: {recap.total_r:+.1f}")
```

---

## 📂 Architecture

```
src/tbow_tactics/
├── __init__.py          # Package exports
├── config.py            # Configuration & constants
├── models.py            # Data models (Signal, Result, TradePlan)
├── signal_engine.py     # TA logic & signal generation
├── risk_engine.py       # Validation & no-trade filters
├── plan_composer.py     # Stocktwits post templates
├── publisher.py         # Rate-limited publishing
├── ledger.py            # Audit trail & R-tracking
├── cli.py               # Command-line interface
├── schemas/             # JSON schemas
│   ├── signal_schema.json
│   └── result_schema.json
├── templates/           # Post templates (future)
└── ledger_data/         # Runtime data storage
    ├── plans/           # JSON plan files
    ├── drafts/          # Draft posts
    └── config.json      # Runtime config
```

---

## 📋 CLI Commands

| Command | Description |
|---------|-------------|
| `signal` | Generate a manual signal |
| `validate` | Validate a signal through risk engine |
| `compose` | Compose a Stocktwits post |
| `publish` | Publish (draft mode by default) |
| `resolve` | Resolve a trade with outcome |
| `recap` | Generate weekly recap |
| `status` | Show system status |
| `list` | List plans in ledger |
| `drafts` | List pending drafts |

### Examples

```bash
# Check system status
python -m src.tbow_tactics.cli status --verbose

# List pending trades
python -m src.tbow_tactics.cli list --pending

# Resolve a trade
python -m src.tbow_tactics.cli resolve TBOW-TSLA-20251229-001 \
    --outcome target1 \
    --result-r 1.4 \
    --notes "Clean breakout" \
    --post

# Generate weekly recap
python -m src.tbow_tactics.cli recap --post
```

---

## 📊 Data Models

### Signal JSON (SSOT)

```json
{
  "strategy": "TBOW_Tactics",
  "version": "1.0.0",
  "ticker": "TSLA",
  "timeframe": "4H",
  "timestamp_utc": "2025-12-29T13:05:00Z",
  "setup": "VWAP_Reclaim_Continuation",
  "bias": "bullish",
  "levels": {
    "entry_zone": [487.5, 489.5],
    "invalidation": 484.9,
    "targets": [495.0, 503.0, 512.0]
  },
  "rules": {
    "trigger": "break_and_hold_above_entry_zone",
    "risk_per_trade_r": 0.5,
    "max_trades_today": 2
  },
  "context": ["above_50SMA", "HTF_support"],
  "post": {
    "include_chart": true,
    "post_type": "plan"
  }
}
```

### Result JSON

```json
{
  "plan_id": "TBOW-TSLA-20251229-1305-4H-001",
  "resolved_timestamp_utc": "2025-12-29T18:40:00Z",
  "outcome": "target2",
  "result_r": 1.4,
  "notes": "Followed stop. Took partials at T1.",
  "followed_rules": true
}
```

---

## 📝 Post Templates

### Template A — Trade Plan

```
$TSLA TBOW Tactics — Vwap Reclaim Continuation (TF: 4H)
Bias: Bullish if reclaim holds.

Entry Zone: 487.50–489.50
Invalidation: < 484.90 (close/hold below = plan dead)
Targets: 495.00 / 503.00 / 512.00

If/Then:
- Break + hold above zone → ride to T1 then trail
- Reject at zone → no trade / wait

Risk: 0.5R max. Not financial advice — playbook + journaling in public.
```

### Template C — Stop-out

```
$TSLA TBOW Stop-out logged
Invalidation hit (<484.90) → exited per rules.
Result: -0.5R
No re-entry unless a fresh setup triggers. On to next.
```

### Template D — Weekly Recap

```
TBOW Weekly Recap (2025-12-22 to 2025-12-28)
Trades: 7 | Win%: 57% | Net: +2.1R
Best setup: VWAP_Reclaim_Continuation | Worst mistake: Overtrading
Next week focus: cleaner entries + no scale-ins against stop.
Full ledger stays public.
```

---

## 🔧 Configuration

Edit `ledger_data/config.json`:

```json
{
  "default_risk_r": 0.5,
  "max_risk_r": 2.0,
  "max_trades_per_day": 5,
  "draft_mode": true,
  "auto_post": false,
  "max_posts_per_hour": 4,
  "max_posts_per_day": 20,
  "stocktwits_access_token": ""
}
```

---

## 🚦 Rollout Phases

### Phase 0: Draft Only (Today)
- Bot generates plans → saves to `drafts/`
- You manually post the best 1–3/day
- `draft_mode: true`

### Phase 1: Auto-post Paper Mode
- Bot posts only "Plan" + "Update"
- Strict rate limit + de-dupe
- No "Big position" talk

### Phase 2: Verified Ledger
- Publish weekly R-recap
- Link to public ledger page on FreeRideInvestor

### Phase 3: Community Engine
- Reply bot (limited): explains setup rules
- Monthly "TBOW Office Hours" post

---

## 🎓 The Narrative That Builds Trust

> "I'm building TBOW in public."
> "Every plan has invalidation."
> "I track R, not ego."
> "Losses stay up."
> "This is a playbook, not a signal service."

**That narrative + receipts = community.**

---

## 📚 Available Setups

| Setup | Description | Default Bias |
|-------|-------------|--------------|
| `VWAP_Reclaim_Continuation` | Price reclaims VWAP with volume | Bullish |
| `VWAP_Rejection_Short` | Price rejects at VWAP | Bearish |
| `HTF_Support_Bounce` | Bounce from higher TF support | Bullish |
| `HTF_Resistance_Rejection` | Rejection from higher TF resistance | Bearish |
| `Breakout_Continuation` | Breakout above key level | Bullish |
| `Breakdown_Continuation` | Breakdown below key level | Bearish |
| `Gap_Fill_Play` | Trade toward unfilled gap | Neutral |
| `Range_Mean_Reversion` | Reversion in choppy conditions | Neutral |
| `Trend_Pullback_Entry` | Entry on pullback in trend | Bullish |
| `Opening_Range_Breakout` | Breakout from first 15-30min range | Neutral |

---

## 🧪 Testing

```bash
# Run all tests
pytest tests/test_tbow_tactics.py -v

# Run specific test class
pytest tests/test_tbow_tactics.py::TestModels -v

# Run with coverage
pytest tests/test_tbow_tactics.py --cov=src/tbow_tactics
```

---

## 📜 License

Part of the FreeRideInvestor project. For educational purposes only.

---

## 🔗 Links

- [FreeRideInvestor.com](https://freerideinvestor.com)
- [Stocktwits Profile](https://stocktwits.com)
