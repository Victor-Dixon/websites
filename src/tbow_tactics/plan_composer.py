"""
TBOW Tactics Plan Composer

Transforms signals into human-readable Stocktwits posts.
Templates maintain consistency and credibility.
"""

from __future__ import annotations

from dataclasses import dataclass
from datetime import datetime, timezone
from typing import Optional
from enum import Enum
import textwrap

from .models import Signal, Result, TradePlan, WeeklyRecap, Bias, Outcome, PostType
from .config import DISCLOSURE_SHORT, DISCLOSURE_FULL


# ═══════════════════════════════════════════════════════════════════════════
# POST TEMPLATES
# ═══════════════════════════════════════════════════════════════════════════

TEMPLATE_PLAN = """\
${ticker} TBOW Tactics — {setup} (TF: {timeframe})
Bias: {bias_text}

Entry Zone: {entry_zone}
Invalidation: {invalidation_text}
Targets: {targets}

If/Then:
{if_then}

Risk: {risk_r}R max. {disclosure}"""

TEMPLATE_UPDATE = """\
${ticker} TBOW Update
{status_text}
Stop unchanged: {invalidation}
{progress_text}
Result so far: {current_r}R {booked_text}"""

TEMPLATE_STOPOUT = """\
${ticker} TBOW Stop-out logged
Invalidation hit ({invalidation}) → exited per rules.
Result: {result_r}R
No re-entry unless a fresh setup triggers. On to next."""

TEMPLATE_TARGET_HIT = """\
${ticker} TBOW Target Hit ✅
{target_text}
Result: +{result_r}R
{notes}"""

TEMPLATE_WEEKLY_RECAP = """\
TBOW Weekly Recap ({date_range})
Trades: {total_trades} | Win%: {win_rate:.0f}% | Net: {net_r:+.1f}R
Best setup: {best_setup} | Worst mistake: {worst_mistake}
{focus_text}
Full ledger stays public."""


# ═══════════════════════════════════════════════════════════════════════════
# IF/THEN SCENARIOS
# ═══════════════════════════════════════════════════════════════════════════

IF_THEN_BULLISH = """\
- Break + hold above zone → ride to T1 then trail
- Reject at zone → no trade / wait"""

IF_THEN_BEARISH = """\
- Break + hold below zone → ride to T1 then trail
- Bounce at zone → no trade / wait"""

IF_THEN_NEUTRAL = """\
- Direction confirmed with volume → follow
- Chop / no confirmation → sit out"""


# ═══════════════════════════════════════════════════════════════════════════
# PLAN COMPOSER
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class ComposedPost:
    """A composed post ready for publishing."""
    content: str
    post_type: PostType
    ticker: str
    plan_id: str
    character_count: int
    includes_disclosure: bool
    
    @property
    def is_valid_length(self) -> bool:
        """Stocktwits has 1000 character limit."""
        return self.character_count <= 1000
    
    def truncate_if_needed(self, max_chars: int = 1000) -> str:
        """Truncate content if over limit."""
        if len(self.content) <= max_chars:
            return self.content
        
        # Truncate and add ellipsis
        return self.content[:max_chars - 3] + "..."


class PlanComposer:
    """
    Composes Stocktwits posts from signals and results.
    
    Maintains consistent formatting for credibility.
    """
    
    def __init__(
        self,
        use_short_disclosure: bool = True,
        include_chart_note: bool = True,
    ):
        self.disclosure = DISCLOSURE_SHORT if use_short_disclosure else DISCLOSURE_FULL
        self.include_chart_note = include_chart_note
    
    def compose_plan(self, signal: Signal) -> ComposedPost:
        """
        Compose a trade plan post from a signal.
        
        Template A — Forward call with levels and invalidation.
        """
        # Format entry zone
        entry_low, entry_high = signal.levels.entry_zone
        if entry_low == entry_high:
            entry_zone = f"{entry_low:.2f}"
        else:
            entry_zone = f"{entry_low:.2f}–{entry_high:.2f}"
        
        # Format invalidation based on bias
        if signal.bias == Bias.BULLISH:
            invalidation_text = f"< {signal.levels.invalidation:.2f} (close/hold below = plan dead)"
        elif signal.bias == Bias.BEARISH:
            invalidation_text = f"> {signal.levels.invalidation:.2f} (close/hold above = plan dead)"
        else:
            invalidation_text = f"± {signal.levels.invalidation:.2f} (break level = plan dead)"
        
        # Format targets
        targets = " / ".join(f"{t:.2f}" for t in signal.levels.targets[:3])
        
        # Format bias text
        if signal.bias == Bias.BULLISH:
            bias_text = "Bullish if reclaim holds."
        elif signal.bias == Bias.BEARISH:
            bias_text = "Bearish if breakdown holds."
        else:
            bias_text = "Neutral — waiting for direction."
        
        # Select if/then template
        if signal.bias == Bias.BULLISH:
            if_then = IF_THEN_BULLISH
        elif signal.bias == Bias.BEARISH:
            if_then = IF_THEN_BEARISH
        else:
            if_then = IF_THEN_NEUTRAL
        
        # Format the setup name for display
        setup_display = signal.setup.replace("_", " ").title()
        
        content = TEMPLATE_PLAN.format(
            ticker=signal.ticker,
            setup=setup_display,
            timeframe=signal.timeframe,
            bias_text=bias_text,
            entry_zone=entry_zone,
            invalidation_text=invalidation_text,
            targets=targets,
            if_then=if_then,
            risk_r=signal.rules.risk_per_trade_r,
            disclosure=self.disclosure,
        )
        
        return ComposedPost(
            content=content,
            post_type=PostType.PLAN,
            ticker=signal.ticker,
            plan_id=signal.plan_id,
            character_count=len(content),
            includes_disclosure=True,
        )
    
    def compose_update(
        self,
        signal: Signal,
        status: str,
        current_r: float = 0.0,
        progress: str = "",
        booked: bool = False,
    ) -> ComposedPost:
        """
        Compose an update post for an active trade.
        
        Template B — Keep it honest.
        """
        if current_r >= 0:
            status_text = f"{status} ✅" if status else "Position active ✅"
        else:
            status_text = f"{status} ⚠️" if status else "Position under pressure ⚠️"
        
        progress_text = progress if progress else ""
        booked_text = "booked." if booked else "unrealized."
        
        content = TEMPLATE_UPDATE.format(
            ticker=signal.ticker,
            status_text=status_text,
            invalidation=f"{signal.levels.invalidation:.2f}",
            progress_text=progress_text,
            current_r=f"{current_r:+.1f}",
            booked_text=booked_text,
        )
        
        return ComposedPost(
            content=content,
            post_type=PostType.UPDATE,
            ticker=signal.ticker,
            plan_id=signal.plan_id,
            character_count=len(content),
            includes_disclosure=False,
        )
    
    def compose_stopout(self, signal: Signal, result: Result) -> ComposedPost:
        """
        Compose a stop-out post.
        
        Template C — Credibility builder. Never hide losses.
        """
        content = TEMPLATE_STOPOUT.format(
            ticker=signal.ticker,
            invalidation=f"<{signal.levels.invalidation:.2f}" if signal.bias == Bias.BULLISH else f">{signal.levels.invalidation:.2f}",
            result_r=f"{result.result_r:.1f}",
        )
        
        return ComposedPost(
            content=content,
            post_type=PostType.STOPOUT,
            ticker=signal.ticker,
            plan_id=signal.plan_id,
            character_count=len(content),
            includes_disclosure=False,
        )
    
    def compose_target_hit(
        self,
        signal: Signal,
        result: Result,
        target_number: int = 1,
        notes: str = "",
    ) -> ComposedPost:
        """
        Compose a target hit post.
        """
        target_text = f"T{target_number} reached at {signal.levels.targets[target_number - 1]:.2f}"
        
        content = TEMPLATE_TARGET_HIT.format(
            ticker=signal.ticker,
            target_text=target_text,
            result_r=result.result_r,
            notes=notes if notes else "Following the plan.",
        )
        
        return ComposedPost(
            content=content,
            post_type=PostType.TARGET_HIT,
            ticker=signal.ticker,
            plan_id=signal.plan_id,
            character_count=len(content),
            includes_disclosure=False,
        )
    
    def compose_weekly_recap(self, recap: WeeklyRecap) -> ComposedPost:
        """
        Compose a weekly recap post.
        
        Template D — Community glue.
        """
        focus_text = f"Next week focus: {recap.notes}" if recap.notes else ""
        
        content = TEMPLATE_WEEKLY_RECAP.format(
            date_range=f"{recap.week_start} to {recap.week_end}",
            total_trades=recap.total_trades,
            win_rate=recap.win_rate,
            net_r=recap.total_r,
            best_setup=recap.best_setup or "N/A",
            worst_mistake=recap.worst_mistake or "N/A",
            focus_text=focus_text,
        )
        
        return ComposedPost(
            content=content,
            post_type=PostType.RECAP,
            ticker="",  # Recap is not ticker-specific
            plan_id="",
            character_count=len(content),
            includes_disclosure=False,
        )
    
    def compose_from_plan(
        self,
        plan: TradePlan,
        post_type: Optional[PostType] = None,
    ) -> ComposedPost:
        """
        Compose appropriate post from a TradePlan.
        
        Automatically determines post type based on plan state.
        """
        if post_type:
            # Explicit type requested
            pass
        elif plan.result is None:
            # No result yet = initial plan
            post_type = PostType.PLAN
        elif plan.result.outcome == Outcome.STOPPED_OUT:
            post_type = PostType.STOPOUT
        elif plan.result.outcome in (Outcome.TARGET1, Outcome.TARGET2, Outcome.TARGET3):
            post_type = PostType.TARGET_HIT
        else:
            post_type = PostType.UPDATE
        
        if post_type == PostType.PLAN:
            return self.compose_plan(plan.signal)
        elif post_type == PostType.STOPOUT:
            return self.compose_stopout(plan.signal, plan.result)
        elif post_type == PostType.TARGET_HIT:
            target_num = 1
            if plan.result.outcome == Outcome.TARGET2:
                target_num = 2
            elif plan.result.outcome == Outcome.TARGET3:
                target_num = 3
            return self.compose_target_hit(
                plan.signal, 
                plan.result, 
                target_num,
                plan.result.notes,
            )
        else:
            return self.compose_update(
                plan.signal,
                status="Trade in progress",
                current_r=plan.result.result_r if plan.result else 0.0,
            )


# ═══════════════════════════════════════════════════════════════════════════
# BATCH COMPOSER
# ═══════════════════════════════════════════════════════════════════════════

class BatchComposer:
    """
    Compose multiple posts for batch processing.
    
    Useful for generating drafts for review.
    """
    
    def __init__(self, composer: Optional[PlanComposer] = None):
        self.composer = composer or PlanComposer()
    
    def compose_all_from_plans(
        self,
        plans: list[TradePlan],
    ) -> list[ComposedPost]:
        """Compose posts for all plans."""
        return [self.composer.compose_from_plan(plan) for plan in plans]
    
    def compose_drafts(
        self,
        signals: list[Signal],
    ) -> list[ComposedPost]:
        """Compose draft plan posts for signals."""
        return [self.composer.compose_plan(signal) for signal in signals]
    
    def format_drafts_for_review(
        self,
        posts: list[ComposedPost],
    ) -> str:
        """Format posts for human review before publishing."""
        output = []
        output.append("=" * 60)
        output.append("TBOW TACTICS DRAFT POSTS FOR REVIEW")
        output.append("=" * 60)
        output.append("")
        
        for i, post in enumerate(posts, 1):
            output.append(f"--- Post {i}/{len(posts)} ---")
            output.append(f"Type: {post.post_type.value.upper()}")
            output.append(f"Ticker: ${post.ticker}")
            output.append(f"Plan ID: {post.plan_id}")
            output.append(f"Characters: {post.character_count}/1000")
            output.append(f"Valid length: {'✓' if post.is_valid_length else '✗'}")
            output.append("")
            output.append(post.content)
            output.append("")
            output.append("-" * 40)
            output.append("")
        
        return "\n".join(output)
