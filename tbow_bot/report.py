"""
TBOW Bot Report Generator

Generates HTML reports for daily trading recaps.
"""

from __future__ import annotations

import logging
from datetime import date, datetime, timezone
from typing import Optional
from pathlib import Path

from .db import Trade, DailyStats, get_trades_for_date, get_cumulative_stats
from .config import Config, REPORT_TIMEZONE

logger = logging.getLogger("tbow_bot.report")


# ═══════════════════════════════════════════════════════════════════════════
# CSS STYLES (separate to avoid escaping issues)
# ═══════════════════════════════════════════════════════════════════════════

REPORT_CSS = """
.tbow-daily-report {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
.tbow-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 24px;
}
.tbow-header h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
}
.tbow-header .date {
    opacity: 0.8;
    font-size: 16px;
}
.tbow-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.tbow-stat-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}
.tbow-stat-card .value {
    font-size: 32px;
    font-weight: bold;
    color: #1a1a2e;
}
.tbow-stat-card .value.positive { color: #10b981; }
.tbow-stat-card .value.negative { color: #ef4444; }
.tbow-stat-card .label {
    font-size: 14px;
    color: #666;
    margin-top: 4px;
}
.tbow-trades-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
}
.tbow-trades-table th,
.tbow-trades-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}
.tbow-trades-table th {
    background: #f3f4f6;
    font-weight: 600;
}
.tbow-trades-table tr:hover {
    background: #f9fafb;
}
.tbow-pnl-positive { color: #10b981; font-weight: 600; }
.tbow-pnl-negative { color: #ef4444; font-weight: 600; }
.tbow-side-call {
    background: #dcfce7;
    color: #166534;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}
.tbow-side-put {
    background: #fee2e2;
    color: #991b1b;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}
.tbow-chart-placeholder {
    background: #f3f4f6;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 60px 20px;
    text-align: center;
    color: #6b7280;
    margin-bottom: 24px;
}
.tbow-disclaimer {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 16px;
    border-radius: 0 8px 8px 0;
    font-size: 14px;
    color: #92400e;
}
.tbow-cumulative {
    background: #eff6ff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
}
.tbow-cumulative h3 {
    margin: 0 0 12px 0;
    color: #1e40af;
}
"""

NO_TRADES_MESSAGE = """
<div style="text-align: center; padding: 40px; color: #6b7280;">
    <p>📭 No trades today. No signals triggered entry/exit conditions.</p>
</div>
"""

CHART_PLACEHOLDER = """
<div class="tbow-chart-placeholder">
    📈 Chart placeholder - Add TradingView chart screenshot here
</div>
"""


# ═══════════════════════════════════════════════════════════════════════════
# REPORT GENERATOR
# ═══════════════════════════════════════════════════════════════════════════

class ReportGenerator:
    """Generates HTML reports from trade data."""
    
    def __init__(
        self,
        include_chart_placeholder: bool = True,
        include_cumulative: bool = True,
    ):
        self.include_chart_placeholder = include_chart_placeholder
        self.include_cumulative = include_cumulative
    
    def generate_daily_report(
        self,
        target_date: date,
        trades: list[Trade],
        stats: DailyStats,
        symbol: str = "TSLA",
        db_path: Optional[Path] = None,
    ) -> tuple[str, str]:
        """
        Generate HTML report for a single day.
        
        Returns (title, html_content)
        """
        # Format date
        date_formatted = target_date.strftime("%B %d, %Y")
        date_short = target_date.strftime("%Y-%m-%d")
        
        # P&L formatting
        pnl = stats.total_pnl
        pnl_class = "positive" if pnl >= 0 else "negative"
        pnl_display = f"${pnl:+,.2f}"
        
        # Generate trades table
        if trades:
            trades_table = self._generate_trades_table(trades)
        else:
            trades_table = NO_TRADES_MESSAGE
        
        # Chart placeholder
        chart_placeholder = CHART_PLACEHOLDER if self.include_chart_placeholder else ""
        
        # Cumulative section
        cumulative_section = ""
        if self.include_cumulative:
            cumulative_section = self._generate_cumulative_section(db_path)
        
        # Build HTML using string concatenation to avoid format issues
        html_parts = [
            '<div class="tbow-daily-report">',
            f'<style>{REPORT_CSS}</style>',
            '',
            '<div class="tbow-header">',
            '<h1>🎯 TBOW Daily Recap</h1>',
            f'<div class="date">{date_formatted} | {symbol}</div>',
            '</div>',
            '',
            '<div class="tbow-stats-grid">',
            '<div class="tbow-stat-card">',
            f'<div class="value {pnl_class}">{pnl_display}</div>',
            '<div class="label">Daily P&L</div>',
            '</div>',
            '<div class="tbow-stat-card">',
            f'<div class="value">{stats.total_trades}</div>',
            '<div class="label">Trades</div>',
            '</div>',
            '<div class="tbow-stat-card">',
            f'<div class="value">{stats.win_rate:.0f}%</div>',
            '<div class="label">Win Rate</div>',
            '</div>',
            '<div class="tbow-stat-card">',
            f'<div class="value">{stats.wins}/{stats.losses}</div>',
            '<div class="label">Wins/Losses</div>',
            '</div>',
            '</div>',
            '',
            chart_placeholder,
            '',
            '<h3>📊 Trade Details</h3>',
            trades_table,
            '',
            cumulative_section,
            '',
            '<div class="tbow-disclaimer">',
            '<strong>⚠️ Disclaimer:</strong> This is a paper trading recap based on TBOW signal timing. ',
            'Actual results may vary due to slippage, execution delays, and market conditions. ',
            'This is NOT investment advice. Past performance does not guarantee future results.',
            '</div>',
            '',
            '</div>',
        ]
        
        html = '\n'.join(html_parts)
        
        # Generate title
        title = f"TBOW Daily Recap – {date_short} | {symbol} | {pnl_display}"
        
        return title, html
    
    def _generate_trades_table(self, trades: list[Trade]) -> str:
        """Generate HTML table of trades."""
        rows = []
        
        for i, trade in enumerate(trades, 1):
            pnl = trade.pnl_underlying or 0
            pnl_class = "tbow-pnl-positive" if pnl >= 0 else "tbow-pnl-negative"
            pnl_display = f"${pnl:+,.2f}"
            
            # Format times
            entry_time = self._format_time(trade.entry_ts)
            exit_time = self._format_time(trade.exit_ts) if trade.exit_ts else "–"
            exit_price = trade.exit_price if trade.exit_price else 0
            
            side_class = f"tbow-side-{trade.side.lower()}"
            
            row = f"""
            <tr>
                <td>{i}</td>
                <td><span class="{side_class}">{trade.side}</span></td>
                <td>{entry_time}</td>
                <td>${trade.entry_price:.2f}</td>
                <td>{exit_time}</td>
                <td>${exit_price:.2f}</td>
                <td class="{pnl_class}">{pnl_display}</td>
            </tr>
            """
            rows.append(row)
        
        table = f"""
        <table class="tbow-trades-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Side</th>
                    <th>Entry Time</th>
                    <th>Entry $</th>
                    <th>Exit Time</th>
                    <th>Exit $</th>
                    <th>P&L</th>
                </tr>
            </thead>
            <tbody>
                {''.join(rows)}
            </tbody>
        </table>
        """
        
        return table
    
    def _generate_cumulative_section(self, db_path: Optional[Path] = None) -> str:
        """Generate cumulative performance section."""
        stats = get_cumulative_stats(db_path)
        
        if stats["total_days"] == 0:
            return ""
        
        pnl = stats["total_pnl"]
        pnl_class = "tbow-pnl-positive" if pnl >= 0 else "tbow-pnl-negative"
        
        return f"""
        <div class="tbow-cumulative">
            <h3>📈 Cumulative Performance</h3>
            <p>
                <strong>Total Days:</strong> {stats["total_days"]} |
                <strong>Total Trades:</strong> {stats["total_trades"]} |
                <strong>Win Rate:</strong> {stats["avg_win_rate"]:.1f}% |
                <strong>Total P&L:</strong> <span class="{pnl_class}">${pnl:+,.2f}</span>
            </p>
        </div>
        """
    
    def _format_time(self, ts: str) -> str:
        """Format timestamp for display."""
        try:
            dt = datetime.fromisoformat(ts.replace("Z", "+00:00"))
            return dt.strftime("%H:%M:%S")
        except (ValueError, AttributeError):
            return ts[:8] if len(ts) >= 8 else ts


# ═══════════════════════════════════════════════════════════════════════════
# CONVENIENCE FUNCTIONS
# ═══════════════════════════════════════════════════════════════════════════

def generate_report_for_date(
    target_date: date,
    symbol: str = "TSLA",
    db_path: Optional[Path] = None,
) -> tuple[str, str]:
    """
    Generate a report for a specific date.
    
    Loads trades from database and generates HTML.
    
    Returns (title, html_content)
    """
    from .paper_engine import PaperTradeEngine
    
    # Process signals to get trades and stats
    engine = PaperTradeEngine()
    result = engine.process_date(target_date, symbol, reprocess=False, db_path=db_path)
    
    # Generate report
    generator = ReportGenerator()
    return generator.generate_daily_report(
        target_date=target_date,
        trades=result.trades,
        stats=result.stats,
        symbol=symbol,
        db_path=db_path,
    )


def generate_report_for_today(
    symbol: str = "TSLA",
) -> tuple[str, str]:
    """Generate a report for today."""
    return generate_report_for_date(date.today(), symbol)
