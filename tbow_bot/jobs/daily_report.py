#!/usr/bin/env python3
"""
TBOW Bot Daily Report Job

Processes signals, generates report, and publishes to WordPress.

Run daily after market close (4:10pm CT / 5:10pm ET).

Usage:
    python -m tbow_bot.jobs.daily_report
    python -m tbow_bot.jobs.daily_report --date 2025-12-29
    python -m tbow_bot.jobs.daily_report --dry-run
"""

from __future__ import annotations

import argparse
import logging
import sys
from datetime import date, datetime, timedelta
from pathlib import Path

# Add parent to path for imports
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

from tbow_bot.paper_engine import PaperTradeEngine
from tbow_bot.report import ReportGenerator
from tbow_bot.post_wp import WordPressPublisher, PostResult
from tbow_bot.db import init_db, get_signals_for_date
from tbow_bot.config import Config

# ═══════════════════════════════════════════════════════════════════════════
# LOGGING
# ═══════════════════════════════════════════════════════════════════════════

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
)
logger = logging.getLogger("tbow_bot.daily_report")


# ═══════════════════════════════════════════════════════════════════════════
# DAILY REPORT JOB
# ═══════════════════════════════════════════════════════════════════════════

def run_daily_report(
    target_date: date,
    symbol: str = "TSLA",
    dry_run: bool = False,
    reprocess: bool = False,
    post_status: str = "draft",
) -> dict:
    """
    Run the complete daily report pipeline.
    
    1. Process signals → trades
    2. Generate HTML report
    3. Publish to WordPress
    
    Args:
        target_date: Date to process
        symbol: Stock symbol
        dry_run: If True, generate report but don't publish
        reprocess: If True, reprocess signals even if already done
        post_status: "draft" or "publish"
    
    Returns:
        Dict with pipeline results
    """
    logger.info(f"Starting daily report for {target_date} ({symbol})")
    
    # Initialize database
    init_db()
    
    # Check if we have signals
    signals = get_signals_for_date(target_date, symbol)
    if not signals:
        logger.warning(f"No signals found for {symbol} on {target_date}")
        # Still generate an empty report
    
    logger.info(f"Found {len(signals)} signals")
    
    # 1. Process signals to trades
    engine = PaperTradeEngine()
    engine_result = engine.process_date(
        target_date=target_date,
        symbol=symbol,
        reprocess=reprocess,
    )
    
    logger.info(
        f"Processed: {engine_result.signals_processed} signals → "
        f"{len(engine_result.trades)} trades"
    )
    
    # 2. Generate report
    generator = ReportGenerator()
    title, html = generator.generate_daily_report(
        target_date=target_date,
        trades=engine_result.trades,
        stats=engine_result.stats,
        symbol=symbol,
    )
    
    logger.info(f"Generated report: {title}")
    
    # 3. Publish (or dry run)
    result: PostResult
    
    if dry_run:
        logger.info("DRY RUN - not publishing to WordPress")
        
        # Save to file for review
        output_dir = Path(__file__).parent.parent / "output"
        output_dir.mkdir(exist_ok=True)
        
        output_file = output_dir / f"report_{target_date}.html"
        with open(output_file, "w") as f:
            f.write(f"<html><head><title>{title}</title></head><body>{html}</body></html>")
        
        logger.info(f"Saved to {output_file}")
        
        result = PostResult(success=True, error="Dry run - not published")
    else:
        # Publish to WordPress
        publisher = WordPressPublisher()
        
        result = publisher.publish_daily_report(
            title=title,
            html=html,
            report_date=target_date,
            status=post_status,
            skip_if_exists=True,
        )
        
        if result.success:
            logger.info(f"Published: {result.post_url or 'Created as draft'}")
        else:
            logger.error(f"Failed to publish: {result.error}")
    
    return {
        "date": str(target_date),
        "symbol": symbol,
        "signals_processed": engine_result.signals_processed,
        "trades": len(engine_result.trades),
        "total_pnl": engine_result.stats.total_pnl,
        "win_rate": engine_result.stats.win_rate,
        "post_success": result.success,
        "post_id": result.post_id,
        "post_url": result.post_url,
        "post_error": result.error,
    }


def run_for_today(
    symbol: str = "TSLA",
    dry_run: bool = False,
    post_status: str = "draft",
) -> dict:
    """Run daily report for today."""
    return run_daily_report(
        target_date=date.today(),
        symbol=symbol,
        dry_run=dry_run,
        post_status=post_status,
    )


def run_for_yesterday(
    symbol: str = "TSLA",
    dry_run: bool = False,
    post_status: str = "draft",
) -> dict:
    """Run daily report for yesterday."""
    yesterday = date.today() - timedelta(days=1)
    return run_daily_report(
        target_date=yesterday,
        symbol=symbol,
        dry_run=dry_run,
        post_status=post_status,
    )


# ═══════════════════════════════════════════════════════════════════════════
# CLI
# ═══════════════════════════════════════════════════════════════════════════

def main():
    """CLI entrypoint."""
    parser = argparse.ArgumentParser(
        description="TBOW Bot Daily Report Generator",
    )
    
    parser.add_argument(
        "--date", "-d",
        help="Date to process (YYYY-MM-DD). Default: today",
        default=None,
    )
    
    parser.add_argument(
        "--symbol", "-s",
        help="Stock symbol. Default: TSLA",
        default="TSLA",
    )
    
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Generate report but don't publish",
    )
    
    parser.add_argument(
        "--reprocess",
        action="store_true",
        help="Reprocess signals even if already done",
    )
    
    parser.add_argument(
        "--publish",
        action="store_true",
        help="Publish immediately (not as draft)",
    )
    
    parser.add_argument(
        "--yesterday",
        action="store_true",
        help="Process yesterday instead of today",
    )
    
    args = parser.parse_args()
    
    # Determine date
    if args.date:
        target_date = datetime.strptime(args.date, "%Y-%m-%d").date()
    elif args.yesterday:
        target_date = date.today() - timedelta(days=1)
    else:
        target_date = date.today()
    
    # Determine status
    post_status = "publish" if args.publish else "draft"
    
    # Run
    try:
        result = run_daily_report(
            target_date=target_date,
            symbol=args.symbol.upper(),
            dry_run=args.dry_run,
            reprocess=args.reprocess,
            post_status=post_status,
        )
        
        # Print summary
        print("\n" + "=" * 50)
        print("TBOW Daily Report Summary")
        print("=" * 50)
        print(f"Date: {result['date']}")
        print(f"Symbol: {result['symbol']}")
        print(f"Signals: {result['signals_processed']}")
        print(f"Trades: {result['trades']}")
        print(f"P&L: ${result['total_pnl']:+,.2f}")
        print(f"Win Rate: {result['win_rate']:.0f}%")
        print("-" * 50)
        
        if result['post_success']:
            if result['post_url']:
                print(f"Published: {result['post_url']}")
            elif result['post_id']:
                print(f"Draft created: Post #{result['post_id']}")
            else:
                print(f"Status: {result['post_error']}")
        else:
            print(f"Publish failed: {result['post_error']}")
        
        print("=" * 50)
        
        return 0 if result['post_success'] else 1
        
    except Exception as e:
        logger.exception(f"Daily report failed: {e}")
        return 1


if __name__ == "__main__":
    sys.exit(main())
