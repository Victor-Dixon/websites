#!/usr/bin/env python3
"""
TBOW Bot CLI

Command-line interface for the TBOW Bot system.

Usage:
    python -m tbow_bot.cli [command] [options]
"""

from __future__ import annotations

import argparse
import json
import logging
import sys
from datetime import date, datetime, timedelta
from pathlib import Path

from .db import (
    init_db, get_signals_for_date, get_all_signals,
    get_trades_for_date, get_cumulative_stats,
)
from .paper_engine import PaperTradeEngine, process_today
from .report import generate_report_for_date
from .post_wp import WordPressPublisher
from .config import Config

# ═══════════════════════════════════════════════════════════════════════════
# LOGGING
# ═══════════════════════════════════════════════════════════════════════════

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
)
logger = logging.getLogger("tbow_bot.cli")


# ═══════════════════════════════════════════════════════════════════════════
# COMMANDS
# ═══════════════════════════════════════════════════════════════════════════

def cmd_init(args: argparse.Namespace) -> int:
    """Initialize the database."""
    init_db()
    print("✓ Database initialized")
    return 0


def cmd_signals(args: argparse.Namespace) -> int:
    """List signals."""
    if args.date:
        target_date = datetime.strptime(args.date, "%Y-%m-%d").date()
        signals = get_signals_for_date(target_date, args.symbol)
    else:
        signals = get_all_signals(args.symbol)[:args.limit]
    
    if not signals:
        print("No signals found")
        return 0
    
    print(f"\nSignals ({len(signals)} total)")
    print("=" * 70)
    
    for s in signals:
        print(f"{s.ts} | {s.symbol:5} | {s.event:20} | ${s.price:.2f}")
    
    return 0


def cmd_trades(args: argparse.Namespace) -> int:
    """List trades."""
    if args.date:
        target_date = datetime.strptime(args.date, "%Y-%m-%d").date()
    else:
        target_date = date.today()
    
    trades = get_trades_for_date(target_date, args.symbol)
    
    if not trades:
        print(f"No trades for {target_date}")
        return 0
    
    print(f"\nTrades for {target_date} ({len(trades)} total)")
    print("=" * 80)
    
    total_pnl = 0
    for t in trades:
        pnl = t.pnl_underlying or 0
        total_pnl += pnl
        pnl_str = f"${pnl:+,.2f}"
        print(
            f"{t.side:4} | Entry: ${t.entry_price:.2f} → "
            f"Exit: ${t.exit_price or 0:.2f} | P&L: {pnl_str}"
        )
    
    print("-" * 80)
    print(f"Total P&L: ${total_pnl:+,.2f}")
    
    return 0


def cmd_process(args: argparse.Namespace) -> int:
    """Process signals for a date."""
    if args.date:
        target_date = datetime.strptime(args.date, "%Y-%m-%d").date()
    else:
        target_date = date.today()
    
    engine = PaperTradeEngine()
    result = engine.process_date(
        target_date=target_date,
        symbol=args.symbol,
        reprocess=args.reprocess,
    )
    
    print(f"\n✓ Processed {target_date}")
    print(f"  Signals: {result.signals_processed}")
    print(f"  Trades: {len(result.trades)}")
    print(f"  P&L: ${result.stats.total_pnl:+,.2f}")
    print(f"  Win Rate: {result.stats.win_rate:.0f}%")
    
    return 0


def cmd_report(args: argparse.Namespace) -> int:
    """Generate a report."""
    if args.date:
        target_date = datetime.strptime(args.date, "%Y-%m-%d").date()
    else:
        target_date = date.today()
    
    title, html = generate_report_for_date(target_date, args.symbol)
    
    print(f"\nReport: {title}")
    print("-" * 60)
    
    if args.output:
        output_path = Path(args.output)
        with open(output_path, "w") as f:
            f.write(f"<html><head><title>{title}</title></head><body>{html}</body></html>")
        print(f"Saved to {output_path}")
    else:
        # Print raw HTML (or truncate)
        if args.raw:
            print(html)
        else:
            print(f"HTML content: {len(html)} characters")
            print("Use --output to save or --raw to print")
    
    return 0


def cmd_publish(args: argparse.Namespace) -> int:
    """Publish a report to WordPress."""
    if args.date:
        target_date = datetime.strptime(args.date, "%Y-%m-%d").date()
    else:
        target_date = date.today()
    
    # Generate report
    title, html = generate_report_for_date(target_date, args.symbol)
    
    # Publish
    publisher = WordPressPublisher()
    
    if args.test:
        # Just test connection
        if publisher.test_connection():
            print("✓ WordPress connection successful")
            return 0
        else:
            print("✗ WordPress connection failed")
            return 1
    
    status = "publish" if args.live else "draft"
    
    result = publisher.publish_daily_report(
        title=title,
        html=html,
        report_date=target_date,
        status=status,
        skip_if_exists=not args.force,
    )
    
    if result.success:
        print(f"✓ Published as {status}")
        if result.post_url:
            print(f"  URL: {result.post_url}")
        if result.post_id:
            print(f"  ID: {result.post_id}")
    else:
        print(f"✗ Failed: {result.error}")
        return 1
    
    return 0


def cmd_stats(args: argparse.Namespace) -> int:
    """Show cumulative statistics."""
    stats = get_cumulative_stats()
    
    print("\nTBOW Bot Cumulative Statistics")
    print("=" * 40)
    print(f"Total Days: {stats['total_days']}")
    print(f"Total Trades: {stats['total_trades']}")
    print(f"Total Wins: {stats['total_wins']}")
    print(f"Total Losses: {stats['total_losses']}")
    print(f"Win Rate: {stats['avg_win_rate']:.1f}%")
    print(f"Total P&L: ${stats['total_pnl']:+,.2f}")
    print(f"Total Signals: {stats['total_signals']}")
    print(f"Signals Filtered: {stats['total_filtered']}")
    
    return 0


def cmd_status(args: argparse.Namespace) -> int:
    """Show system status."""
    config = Config.from_env()
    
    print("\nTBOW Bot Status")
    print("=" * 40)
    
    # Config
    print("\nConfiguration:")
    print(f"  Webhook Secret: {'✓ Set' if config.webhook_secret else '✗ Not set'}")
    print(f"  WordPress URL: {config.wp_base}")
    print(f"  WordPress User: {config.wp_user or 'Not set'}")
    print(f"  WP Password: {'✓ Set' if config.wp_app_password else '✗ Not set'}")
    print(f"  Default Symbol: {config.default_symbol}")
    print(f"  Post Status: {config.post_status}")
    
    # Database
    print("\nDatabase:")
    stats = get_cumulative_stats()
    print(f"  Total Signals: {stats['total_signals']}")
    print(f"  Total Trades: {stats['total_trades']}")
    print(f"  Total Days: {stats['total_days']}")
    
    # Today
    print("\nToday:")
    today_signals = get_signals_for_date(date.today())
    today_trades = get_trades_for_date(date.today())
    print(f"  Signals: {len(today_signals)}")
    print(f"  Trades: {len(today_trades)}")
    
    return 0


def cmd_serve(args: argparse.Namespace) -> int:
    """Start the webhook server."""
    try:
        import uvicorn
    except ImportError:
        print("uvicorn not installed. Run: pip install uvicorn")
        return 1
    
    config = Config.from_env()
    
    print(f"\nStarting TBOW Bot webhook server...")
    print(f"  Host: {config.webhook_host}")
    print(f"  Port: {config.webhook_port}")
    print(f"  Webhook: http://{config.webhook_host}:{config.webhook_port}/tv-webhook")
    print()
    
    uvicorn.run(
        "tbow_bot.app:app",
        host=config.webhook_host,
        port=config.webhook_port,
        reload=args.reload,
    )
    
    return 0


# ═══════════════════════════════════════════════════════════════════════════
# MAIN
# ═══════════════════════════════════════════════════════════════════════════

def main() -> int:
    """Main CLI entrypoint."""
    parser = argparse.ArgumentParser(
        description="TBOW Bot - TradingView to WordPress Pipeline",
    )
    
    subparsers = parser.add_subparsers(dest="command", help="Command")
    
    # init
    init_parser = subparsers.add_parser("init", help="Initialize database")
    
    # signals
    signals_parser = subparsers.add_parser("signals", help="List signals")
    signals_parser.add_argument("--date", "-d", help="Date (YYYY-MM-DD)")
    signals_parser.add_argument("--symbol", "-s", default="TSLA")
    signals_parser.add_argument("--limit", "-l", type=int, default=50)
    
    # trades
    trades_parser = subparsers.add_parser("trades", help="List trades")
    trades_parser.add_argument("--date", "-d", help="Date (YYYY-MM-DD)")
    trades_parser.add_argument("--symbol", "-s", default="TSLA")
    
    # process
    process_parser = subparsers.add_parser("process", help="Process signals")
    process_parser.add_argument("--date", "-d", help="Date (YYYY-MM-DD)")
    process_parser.add_argument("--symbol", "-s", default="TSLA")
    process_parser.add_argument("--reprocess", "-r", action="store_true")
    
    # report
    report_parser = subparsers.add_parser("report", help="Generate report")
    report_parser.add_argument("--date", "-d", help="Date (YYYY-MM-DD)")
    report_parser.add_argument("--symbol", "-s", default="TSLA")
    report_parser.add_argument("--output", "-o", help="Output file")
    report_parser.add_argument("--raw", action="store_true", help="Print raw HTML")
    
    # publish
    publish_parser = subparsers.add_parser("publish", help="Publish to WordPress")
    publish_parser.add_argument("--date", "-d", help="Date (YYYY-MM-DD)")
    publish_parser.add_argument("--symbol", "-s", default="TSLA")
    publish_parser.add_argument("--live", action="store_true", help="Publish (not draft)")
    publish_parser.add_argument("--force", action="store_true", help="Force even if exists")
    publish_parser.add_argument("--test", action="store_true", help="Test connection only")
    
    # stats
    stats_parser = subparsers.add_parser("stats", help="Show statistics")
    
    # status
    status_parser = subparsers.add_parser("status", help="Show system status")
    
    # serve
    serve_parser = subparsers.add_parser("serve", help="Start webhook server")
    serve_parser.add_argument("--reload", action="store_true", help="Auto-reload on changes")
    
    args = parser.parse_args()
    
    if not args.command:
        parser.print_help()
        return 0
    
    # Initialize database
    init_db()
    
    # Dispatch
    commands = {
        "init": cmd_init,
        "signals": cmd_signals,
        "trades": cmd_trades,
        "process": cmd_process,
        "report": cmd_report,
        "publish": cmd_publish,
        "stats": cmd_stats,
        "status": cmd_status,
        "serve": cmd_serve,
    }
    
    handler = commands.get(args.command)
    if handler:
        return handler(args)
    else:
        print(f"Unknown command: {args.command}")
        return 1


if __name__ == "__main__":
    sys.exit(main())
