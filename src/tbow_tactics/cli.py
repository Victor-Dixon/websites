#!/usr/bin/env python3
"""
TBOW Tactics CLI

Command-line interface for the TBOW Tactics automation system.

Usage:
    python -m tbow_tactics.cli [command] [options]

Commands:
    signal      Generate a signal manually
    validate    Validate a signal through risk engine
    compose     Compose a Stocktwits post from a signal
    publish     Publish a post (draft mode by default)
    resolve     Resolve a pending trade with outcome
    recap       Generate weekly recap
    status      Show system status
    list        List plans in ledger
"""

from __future__ import annotations

import argparse
import json
import logging
import sys
from datetime import datetime, timezone
from pathlib import Path
from typing import Optional

from .models import Signal, Bias, Outcome
from .signal_engine import SignalEngine
from .risk_engine import RiskEngine
from .plan_composer import PlanComposer, BatchComposer
from .publisher import StocktwitsPublisher
from .ledger import Ledger
from .config import SETUP_CATALOG, VALID_TIMEFRAMES, TBOWConfig, LEDGER_DIR

# Set up logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S",
)
logger = logging.getLogger("tbow_tactics.cli")


def cmd_signal(args: argparse.Namespace) -> int:
    """Generate a manual signal."""
    engine = SignalEngine()
    
    # Parse entry zone
    entry_zone = tuple(float(x) for x in args.entry.split("-"))
    if len(entry_zone) == 1:
        entry_zone = (entry_zone[0], entry_zone[0])
    
    # Parse targets
    targets = [float(x) for x in args.targets.split(",")]
    
    # Create signal
    signal = engine.generate_manual_signal(
        ticker=args.ticker.upper(),
        timeframe=args.timeframe,
        setup=args.setup,
        bias=Bias(args.bias),
        entry_zone=entry_zone,
        invalidation=float(args.invalidation),
        targets=targets,
        trigger=args.trigger or "break_and_hold_above_entry_zone",
        context=args.context.split(",") if args.context else None,
    )
    
    # Validate through risk engine
    risk_engine = RiskEngine()
    validation = risk_engine.validate(signal)
    
    if not validation.valid:
        logger.warning(f"Signal validation failed: {validation.rejection_reasons}")
        if not args.force:
            print("Signal failed validation. Use --force to save anyway.")
            print(f"Reasons: {[r.value for r in validation.rejection_reasons or []]}")
            return 1
    
    # Save to ledger
    ledger = Ledger()
    plan = ledger.save_signal(signal)
    
    print(f"\n✓ Signal created: {plan.plan_id}")
    print(f"\nSignal JSON:")
    print(signal.to_json())
    
    if args.compose:
        composer = PlanComposer()
        post = composer.compose_plan(signal)
        print(f"\nComposed Post ({post.character_count} chars):")
        print("-" * 40)
        print(post.content)
        print("-" * 40)
    
    return 0


def cmd_validate(args: argparse.Namespace) -> int:
    """Validate a signal from JSON file or stdin."""
    if args.file:
        with open(args.file, "r") as f:
            signal_data = json.load(f)
    elif args.json:
        signal_data = json.loads(args.json)
    else:
        # Read from stdin
        signal_data = json.load(sys.stdin)
    
    signal = Signal.from_dict(signal_data)
    
    risk_engine = RiskEngine(atr_value=float(args.atr) if args.atr else None)
    validation = risk_engine.validate(signal)
    
    if validation.valid:
        print("✓ Signal PASSED validation")
        if validation.warnings:
            print(f"  Warnings: {validation.warnings}")
        return 0
    else:
        print("✗ Signal FAILED validation")
        print(f"  Reasons: {[r.value for r in validation.rejection_reasons or []]}")
        if validation.warnings:
            print(f"  Warnings: {validation.warnings}")
        return 1


def cmd_compose(args: argparse.Namespace) -> int:
    """Compose a Stocktwits post."""
    # Load signal or plan
    if args.plan_id:
        ledger = Ledger()
        plan = ledger.load(args.plan_id)
        if not plan:
            print(f"Plan {args.plan_id} not found")
            return 1
        signal = plan.signal
    elif args.file:
        with open(args.file, "r") as f:
            signal = Signal.from_dict(json.load(f))
    else:
        print("Provide --plan-id or --file")
        return 1
    
    composer = PlanComposer()
    post = composer.compose_plan(signal)
    
    print(f"Post Type: {post.post_type.value}")
    print(f"Characters: {post.character_count}/1000")
    print(f"Valid: {'✓' if post.is_valid_length else '✗'}")
    print()
    print(post.content)
    
    if args.output:
        with open(args.output, "w") as f:
            f.write(post.content)
        print(f"\nSaved to {args.output}")
    
    return 0


def cmd_publish(args: argparse.Namespace) -> int:
    """Publish a post to Stocktwits."""
    config = TBOWConfig.load()
    
    publisher = StocktwitsPublisher(
        access_token=config.stocktwits_access_token,
        draft_mode=not args.live,
    )
    
    if args.plan_id:
        ledger = Ledger()
        plan = ledger.load(args.plan_id)
        if not plan:
            print(f"Plan {args.plan_id} not found")
            return 1
        
        response = publisher.publish_plan(plan, force=args.force)
    elif args.content:
        response = publisher.publish_raw(args.content, force=args.force)
    else:
        print("Provide --plan-id or --content")
        return 1
    
    print(f"Result: {response.result.value}")
    print(f"Message: {response.message}")
    
    if response.post_id:
        print(f"Post ID: {response.post_id}")
    
    return 0 if response.result.value in ("success", "draft_mode") else 1


def cmd_resolve(args: argparse.Namespace) -> int:
    """Resolve a trade with its outcome."""
    ledger = Ledger()
    
    plan = ledger.resolve(
        plan_id=args.plan_id,
        outcome=Outcome(args.outcome),
        result_r=float(args.result_r),
        notes=args.notes or "",
        followed_rules=not args.broke_rules,
    )
    
    if not plan:
        print(f"Plan {args.plan_id} not found")
        return 1
    
    print(f"✓ Resolved {args.plan_id}")
    print(f"  Outcome: {args.outcome}")
    print(f"  Result: {float(args.result_r):+.1f}R")
    print(f"  Followed rules: {'Yes' if not args.broke_rules else 'No'}")
    
    # Compose exit post
    if args.post:
        composer = PlanComposer()
        
        if Outcome(args.outcome) == Outcome.STOPPED_OUT:
            post = composer.compose_stopout(plan.signal, plan.result)
        else:
            post = composer.compose_target_hit(plan.signal, plan.result)
        
        print(f"\nExit Post:")
        print("-" * 40)
        print(post.content)
        print("-" * 40)
    
    return 0


def cmd_recap(args: argparse.Namespace) -> int:
    """Generate weekly recap."""
    ledger = Ledger()
    
    if args.start:
        start = datetime.fromisoformat(args.start).replace(tzinfo=timezone.utc)
    else:
        start = None
    
    if args.end:
        end = datetime.fromisoformat(args.end).replace(tzinfo=timezone.utc)
    else:
        end = None
    
    recap = ledger.generate_weekly_recap(start, end)
    
    print(f"\nTBOW Weekly Recap")
    print(f"Period: {recap.week_start} to {recap.week_end}")
    print(f"=" * 40)
    print(f"Total Trades: {recap.total_trades}")
    print(f"Wins: {recap.wins} | Losses: {recap.losses} | BE: {recap.breakevens}")
    print(f"Win Rate: {recap.win_rate:.1f}%")
    print(f"Net R: {recap.total_r:+.1f}")
    print(f"Expectancy: {recap.expectancy:+.2f}R per trade")
    print(f"Best Trade: {recap.best_trade_r:+.1f}R")
    print(f"Worst Trade: {recap.worst_trade_r:+.1f}R")
    print(f"Best Setup: {recap.best_setup or 'N/A'}")
    
    if args.post:
        composer = PlanComposer()
        post = composer.compose_weekly_recap(recap)
        print(f"\nRecap Post:")
        print("-" * 40)
        print(post.content)
        print("-" * 40)
    
    if args.json:
        print(f"\nJSON:")
        print(recap.to_json())
    
    return 0


def cmd_status(args: argparse.Namespace) -> int:
    """Show system status."""
    config = TBOWConfig.load()
    ledger = Ledger()
    publisher = StocktwitsPublisher(draft_mode=True)
    
    print("\nTBOW Tactics System Status")
    print("=" * 40)
    
    # Config
    print(f"\nConfiguration:")
    print(f"  Draft Mode: {config.draft_mode}")
    print(f"  Default Risk: {config.default_risk_r}R")
    print(f"  Max Trades/Day: {config.max_trades_per_day}")
    print(f"  Active Setups: {len(config.active_setups)}")
    
    # Ledger
    pending = ledger.get_pending()
    resolved = ledger.get_resolved()
    
    print(f"\nLedger:")
    print(f"  Pending Plans: {len(pending)}")
    print(f"  Resolved Plans: {len(resolved)}")
    
    # Publisher
    status = publisher.get_status()
    print(f"\nPublisher:")
    print(f"  Draft Mode: {status['draft_mode']}")
    print(f"  Posts Today: {status['rate_limit']['posts_today']}/{status['rate_limit']['max_per_day']}")
    print(f"  Posts This Hour: {status['rate_limit']['posts_this_hour']}/{status['rate_limit']['max_per_hour']}")
    print(f"  Pending Drafts: {status['pending_drafts']}")
    
    # Pending plans
    if pending and args.verbose:
        print(f"\nPending Plans:")
        for plan in pending[:5]:
            print(f"  - {plan.plan_id}: ${plan.signal.ticker} {plan.signal.setup}")
    
    return 0


def cmd_list(args: argparse.Namespace) -> int:
    """List plans in ledger."""
    ledger = Ledger()
    
    if args.pending:
        plans = ledger.get_pending()
        title = "Pending Plans"
    elif args.resolved:
        plans = ledger.get_resolved()
        title = "Resolved Plans"
    else:
        # All plans
        plans = ledger.get_pending() + ledger.get_resolved()
        title = "All Plans"
    
    if args.ticker:
        plans = [p for p in plans if p.signal.ticker.upper() == args.ticker.upper()]
    
    print(f"\n{title} ({len(plans)} total)")
    print("=" * 60)
    
    for plan in plans[:args.limit]:
        status = "PENDING"
        result_str = ""
        
        if plan.result and plan.result.outcome != Outcome.PENDING:
            status = plan.result.outcome.value.upper()
            result_str = f" ({plan.result.result_r:+.1f}R)"
        
        print(f"{plan.plan_id}")
        print(f"  ${plan.signal.ticker} | {plan.signal.setup} | {plan.signal.timeframe}")
        print(f"  Status: {status}{result_str}")
        print()
    
    if len(plans) > args.limit:
        print(f"... and {len(plans) - args.limit} more")
    
    return 0


def cmd_drafts(args: argparse.Namespace) -> int:
    """List and manage draft posts."""
    publisher = StocktwitsPublisher(draft_mode=True)
    drafts = publisher.get_pending_drafts()
    
    if not drafts:
        print("No pending drafts")
        return 0
    
    print(f"\nPending Drafts ({len(drafts)} total)")
    print("=" * 60)
    
    for i, draft in enumerate(drafts[:args.limit], 1):
        print(f"{i}. {draft.name}")
        
        if args.verbose:
            with open(draft, "r") as f:
                content = f.read()
            # Extract the actual post content
            if "---" in content:
                post = content.split("---")[1].strip()
                print(f"   Preview: {post[:80]}...")
            print()
    
    return 0


def main() -> int:
    """Main CLI entrypoint."""
    parser = argparse.ArgumentParser(
        description="TBOW Tactics Automation System",
        formatter_class=argparse.RawDescriptionHelpFormatter,
    )
    
    subparsers = parser.add_subparsers(dest="command", help="Command to run")
    
    # signal command
    signal_parser = subparsers.add_parser("signal", help="Generate a manual signal")
    signal_parser.add_argument("ticker", help="Stock ticker")
    signal_parser.add_argument("--timeframe", "-tf", required=True, choices=VALID_TIMEFRAMES)
    signal_parser.add_argument("--setup", "-s", required=True, choices=list(SETUP_CATALOG.keys()))
    signal_parser.add_argument("--bias", "-b", required=True, choices=["bullish", "bearish", "neutral"])
    signal_parser.add_argument("--entry", "-e", required=True, help="Entry zone (e.g., 487.5-489.5)")
    signal_parser.add_argument("--invalidation", "-i", required=True, help="Stop/invalidation price")
    signal_parser.add_argument("--targets", "-t", required=True, help="Targets (comma-separated)")
    signal_parser.add_argument("--trigger", help="Trigger description")
    signal_parser.add_argument("--context", "-c", help="Context tags (comma-separated)")
    signal_parser.add_argument("--force", "-f", action="store_true", help="Save even if validation fails")
    signal_parser.add_argument("--compose", action="store_true", help="Show composed post")
    
    # validate command
    validate_parser = subparsers.add_parser("validate", help="Validate a signal")
    validate_parser.add_argument("--file", "-f", help="JSON file with signal")
    validate_parser.add_argument("--json", "-j", help="JSON string with signal")
    validate_parser.add_argument("--atr", help="ATR value for stop distance validation")
    
    # compose command
    compose_parser = subparsers.add_parser("compose", help="Compose a Stocktwits post")
    compose_parser.add_argument("--plan-id", "-p", help="Plan ID from ledger")
    compose_parser.add_argument("--file", "-f", help="JSON file with signal")
    compose_parser.add_argument("--output", "-o", help="Output file")
    
    # publish command
    publish_parser = subparsers.add_parser("publish", help="Publish to Stocktwits")
    publish_parser.add_argument("--plan-id", "-p", help="Plan ID to publish")
    publish_parser.add_argument("--content", "-c", help="Raw content to publish")
    publish_parser.add_argument("--live", action="store_true", help="Disable draft mode")
    publish_parser.add_argument("--force", action="store_true", help="Bypass rate limits")
    
    # resolve command
    resolve_parser = subparsers.add_parser("resolve", help="Resolve a trade")
    resolve_parser.add_argument("plan_id", help="Plan ID to resolve")
    resolve_parser.add_argument("--outcome", "-o", required=True, 
                                choices=[o.value for o in Outcome])
    resolve_parser.add_argument("--result-r", "-r", required=True, help="Result in R")
    resolve_parser.add_argument("--notes", "-n", help="Notes about the trade")
    resolve_parser.add_argument("--broke-rules", action="store_true", help="Didn't follow rules")
    resolve_parser.add_argument("--post", action="store_true", help="Show exit post")
    
    # recap command
    recap_parser = subparsers.add_parser("recap", help="Generate weekly recap")
    recap_parser.add_argument("--start", help="Start date (YYYY-MM-DD)")
    recap_parser.add_argument("--end", help="End date (YYYY-MM-DD)")
    recap_parser.add_argument("--post", action="store_true", help="Show recap post")
    recap_parser.add_argument("--json", action="store_true", help="Output as JSON")
    
    # status command
    status_parser = subparsers.add_parser("status", help="Show system status")
    status_parser.add_argument("--verbose", "-v", action="store_true")
    
    # list command
    list_parser = subparsers.add_parser("list", help="List plans")
    list_parser.add_argument("--pending", action="store_true", help="Only pending")
    list_parser.add_argument("--resolved", action="store_true", help="Only resolved")
    list_parser.add_argument("--ticker", "-t", help="Filter by ticker")
    list_parser.add_argument("--limit", "-l", type=int, default=20, help="Max items")
    
    # drafts command
    drafts_parser = subparsers.add_parser("drafts", help="List draft posts")
    drafts_parser.add_argument("--verbose", "-v", action="store_true")
    drafts_parser.add_argument("--limit", "-l", type=int, default=10)
    
    args = parser.parse_args()
    
    if not args.command:
        parser.print_help()
        return 0
    
    # Dispatch to command handler
    commands = {
        "signal": cmd_signal,
        "validate": cmd_validate,
        "compose": cmd_compose,
        "publish": cmd_publish,
        "resolve": cmd_resolve,
        "recap": cmd_recap,
        "status": cmd_status,
        "list": cmd_list,
        "drafts": cmd_drafts,
    }
    
    handler = commands.get(args.command)
    if handler:
        return handler(args)
    else:
        print(f"Unknown command: {args.command}")
        return 1


if __name__ == "__main__":
    sys.exit(main())
