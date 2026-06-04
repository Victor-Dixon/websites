#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
import math
from dataclasses import dataclass
from datetime import datetime, timezone
from pathlib import Path
from typing import Any

import pandas as pd

try:
    import yfinance as yf
except Exception as exc:
    raise SystemExit(
        "Missing yfinance. Install with: python -m pip install yfinance pandas"
    ) from exc


@dataclass
class SignalResult:
    ticker: str
    close: float
    volume: int
    ema8: float
    ema21: float
    rsi14: float
    macd: float
    macd_signal: float
    macd_hist: float
    vwap_proxy: float
    above_vwap: bool
    ema_bullish: bool
    macd_bullish: bool
    rsi_ok: bool
    setup_score: int
    setup_label: str


def safe_float(value: Any) -> float:
    try:
        if pd.isna(value):
            return float("nan")
        return float(value)
    except Exception:
        return float("nan")


def safe_int(value: Any) -> int:
    try:
        if pd.isna(value):
            return 0
        return int(value)
    except Exception:
        return 0


def rsi(series: pd.Series, period: int = 14) -> pd.Series:
    delta = series.diff()
    gain = delta.clip(lower=0)
    loss = -delta.clip(upper=0)
    avg_gain = gain.ewm(alpha=1 / period, adjust=False).mean()
    avg_loss = loss.ewm(alpha=1 / period, adjust=False).mean()
    rs = avg_gain / avg_loss.replace(0, math.nan)
    return 100 - (100 / (1 + rs))


def normalize_download(df: pd.DataFrame, ticker: str) -> pd.DataFrame:
    if df.empty:
        return df

    if isinstance(df.columns, pd.MultiIndex):
        if ticker in df.columns.get_level_values(0):
            df = df[ticker].copy()
        elif ticker in df.columns.get_level_values(-1):
            df = df.xs(ticker, level=-1, axis=1).copy()
        else:
            df.columns = [c[-1] if isinstance(c, tuple) else c for c in df.columns]

    df = df.rename(columns={str(c): str(c).title() for c in df.columns})
    return df


def calculate_signal(ticker: str, df: pd.DataFrame) -> SignalResult:
    required = {"Close", "High", "Low", "Volume"}
    missing = required - set(df.columns)
    if missing:
        raise ValueError(f"{ticker}: missing columns {sorted(missing)}")

    close = df["Close"].astype(float)
    high = df["High"].astype(float)
    low = df["Low"].astype(float)
    volume = df["Volume"].fillna(0).astype(float)

    typical = (high + low + close) / 3
    vwap_proxy_series = (typical * volume).cumsum() / volume.replace(0, math.nan).cumsum()

    ema8 = close.ewm(span=8, adjust=False).mean()
    ema21 = close.ewm(span=21, adjust=False).mean()
    rsi14 = rsi(close, 14)

    ema12 = close.ewm(span=12, adjust=False).mean()
    ema26 = close.ewm(span=26, adjust=False).mean()
    macd = ema12 - ema26
    macd_signal = macd.ewm(span=9, adjust=False).mean()
    macd_hist = macd - macd_signal

    last = df.index[-1]
    last_close = safe_float(close.loc[last])
    last_volume = safe_int(volume.loc[last])
    last_ema8 = safe_float(ema8.loc[last])
    last_ema21 = safe_float(ema21.loc[last])
    last_rsi = safe_float(rsi14.loc[last])
    last_macd = safe_float(macd.loc[last])
    last_macd_signal = safe_float(macd_signal.loc[last])
    last_macd_hist = safe_float(macd_hist.loc[last])
    last_vwap = safe_float(vwap_proxy_series.loc[last])

    above_vwap = bool(last_close > last_vwap)
    ema_bullish = bool(last_ema8 > last_ema21)
    macd_bullish = bool(last_macd > last_macd_signal and last_macd_hist > 0)
    rsi_ok = bool(40 <= last_rsi <= 72)

    score = 0
    score += 25 if above_vwap else 0
    score += 25 if ema_bullish else 0
    score += 25 if macd_bullish else 0
    score += 15 if rsi_ok else 0
    score += 10 if last_volume > 0 else 0

    if score >= 80:
        label = "A_SETUP_CANDIDATE"
    elif score >= 60:
        label = "WATCHLIST_SETUP"
    else:
        label = "NO_TRADE_REVIEW"

    return SignalResult(
        ticker=ticker,
        close=last_close,
        volume=last_volume,
        ema8=last_ema8,
        ema21=last_ema21,
        rsi14=last_rsi,
        macd=last_macd,
        macd_signal=last_macd_signal,
        macd_hist=last_macd_hist,
        vwap_proxy=last_vwap,
        above_vwap=above_vwap,
        ema_bullish=ema_bullish,
        macd_bullish=macd_bullish,
        rsi_ok=rsi_ok,
        setup_score=score,
        setup_label=label,
    )


def result_to_dict(result: SignalResult) -> dict[str, Any]:
    return {
        "ticker": result.ticker,
        "close": round(result.close, 4),
        "volume": result.volume,
        "indicators": {
            "ema8": round(result.ema8, 4),
            "ema21": round(result.ema21, 4),
            "rsi14": round(result.rsi14, 4),
            "macd": round(result.macd, 6),
            "macd_signal": round(result.macd_signal, 6),
            "macd_hist": round(result.macd_hist, 6),
            "vwap_proxy": round(result.vwap_proxy, 4),
        },
        "conditions": {
            "above_vwap_proxy": result.above_vwap,
            "ema8_above_ema21": result.ema_bullish,
            "macd_bullish": result.macd_bullish,
            "rsi_in_review_zone": result.rsi_ok,
        },
        "setup_score": result.setup_score,
        "setup_label": result.setup_label,
    }


def write_markdown(out: Path, generated: str, period: str, interval: str, rows: list[dict[str, Any]]) -> None:
    lines = [
        "# TradingRobotPlug Daily Market Proof",
        "",
        f"Generated UTC: `{generated}`",
        f"Data window: `{period}`",
        f"Interval: `{interval}`",
        "",
        "## Purpose",
        "",
        "This is a market-derived proof artifact. It shows that TradingRobotPlug can pull market data, calculate indicators, score setup conditions, and publish reviewable robot-plugin evidence.",
        "",
        "This is not financial advice. This is not a profit claim. This is not live trade approval.",
        "",
        "## Daily Setup Scores",
        "",
        "| Ticker | Close | Score | Label | VWAP | EMA | MACD | RSI |",
        "|---|---:|---:|---|---|---|---|---|",
    ]

    for row in rows:
        c = row["conditions"]
        lines.append(
            f"| {row['ticker']} | {row['close']} | {row['setup_score']} | {row['setup_label']} | "
            f"{'PASS' if c['above_vwap_proxy'] else 'FAIL'} | "
            f"{'PASS' if c['ema8_above_ema21'] else 'FAIL'} | "
            f"{'PASS' if c['macd_bullish'] else 'FAIL'} | "
            f"{'PASS' if c['rsi_in_review_zone'] else 'FAIL'} |"
        )

    lines.extend([
        "",
        "## Indicator Details",
        "",
    ])

    for row in rows:
        ind = row["indicators"]
        cond = row["conditions"]
        lines.extend([
            f"### {row['ticker']}",
            "",
            f"- Close: `{row['close']}`",
            f"- Volume: `{row['volume']}`",
            f"- EMA 8: `{ind['ema8']}`",
            f"- EMA 21: `{ind['ema21']}`",
            f"- RSI 14: `{ind['rsi14']}`",
            f"- MACD: `{ind['macd']}`",
            f"- MACD Signal: `{ind['macd_signal']}`",
            f"- MACD Histogram: `{ind['macd_hist']}`",
            f"- VWAP Proxy: `{ind['vwap_proxy']}`",
            "",
            "Conditions:",
            f"- Above VWAP proxy: `{cond['above_vwap_proxy']}`",
            f"- EMA 8 above EMA 21: `{cond['ema8_above_ema21']}`",
            f"- MACD bullish: `{cond['macd_bullish']}`",
            f"- RSI review zone: `{cond['rsi_in_review_zone']}`",
            "",
        ])

    lines.extend([
        "## Risk Rules",
        "",
        "- Paper-first only.",
        "- No execution order is created by this report.",
        "- No live trading approval is implied.",
        "- Any strategy must pass separate forward testing and operator review.",
        "",
    ])

    out.write_text("\n".join(lines), encoding="utf-8")


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--out-dir", required=True)
    parser.add_argument("--tickers", default="TSLA,QQQ,SPY")
    parser.add_argument("--period", default="5d")
    parser.add_argument("--interval", default="15m")
    args = parser.parse_args()

    out_dir = Path(args.out_dir)
    out_dir.mkdir(parents=True, exist_ok=True)

    tickers = [t.strip().upper() for t in args.tickers.split(",") if t.strip()]
    generated = datetime.now(timezone.utc).isoformat()
    stamp = datetime.now(timezone.utc).strftime("%Y%m%d")

    rows: list[dict[str, Any]] = []
    errors: list[dict[str, str]] = []

    for ticker in tickers:
        try:
            raw = yf.download(
                ticker,
                period=args.period,
                interval=args.interval,
                auto_adjust=True,
                progress=False,
                threads=False,
            )
            df = normalize_download(raw, ticker)
            if df.empty:
                raise ValueError("download returned empty dataframe")
            result = calculate_signal(ticker, df.dropna())
            rows.append(result_to_dict(result))
        except Exception as exc:
            errors.append({"ticker": ticker, "error": str(exc)})

    payload = {
        "domain": "tradingrobotplug.com",
        "generated_utc": generated,
        "data_source": "yfinance via Yahoo Finance public market data access",
        "source_limit": "research/educational proof artifact; not production execution feed",
        "period": args.period,
        "interval": args.interval,
        "tickers": tickers,
        "results": rows,
        "errors": errors,
        "risk_disclaimer": "Not financial advice. Not a profit claim. Not live trade approval.",
    }

    json_path = out_dir / f"daily-market-proof-{stamp}.json"
    md_path = out_dir / f"daily-market-proof-{stamp}.md"
    latest_json = out_dir / "latest-daily-market-proof.json"
    latest_md = out_dir / "latest-daily-market-proof.md"
    index_md = out_dir / "index.md"

    json_text = json.dumps(payload, indent=2)
    json_path.write_text(json_text, encoding="utf-8")
    latest_json.write_text(json_text, encoding="utf-8")

    write_markdown(md_path, generated, args.period, args.interval, rows)
    latest_md.write_text(md_path.read_text(encoding="utf-8"), encoding="utf-8")

    index_lines = [
        "# TradingRobotPlug Daily Proof Index",
        "",
        f"Latest generated UTC: `{generated}`",
        "",
        "## Latest",
        "",
        "- [Latest Markdown](./latest-daily-market-proof.md)",
        "- [Latest JSON](./latest-daily-market-proof.json)",
        "",
        "## Dated Artifacts",
        "",
    ]

    for p in sorted(out_dir.glob("daily-market-proof-*.md"), reverse=True)[:30]:
        stamp_part = p.stem.replace("daily-market-proof-", "")
        index_lines.append(f"- [{stamp_part} Markdown](./{p.name}) · [JSON](./daily-market-proof-{stamp_part}.json)")

    index_md.write_text("\n".join(index_lines) + "\n", encoding="utf-8")

    print(f"DAILY_PROOF_JSON={json_path}")
    print(f"DAILY_PROOF_MD={md_path}")
    print(f"RESULT_COUNT={len(rows)}")
    print(f"ERROR_COUNT={len(errors)}")
    if not rows:
        raise SystemExit("NO_MARKET_ROWS_GENERATED")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
