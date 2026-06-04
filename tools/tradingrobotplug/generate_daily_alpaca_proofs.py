#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
import math
import os
from dataclasses import dataclass
from datetime import datetime, timedelta, timezone
from pathlib import Path
from typing import Any

import pandas as pd

try:
    import requests
except Exception as exc:
    raise SystemExit("Missing requests. Install with: python -m pip install requests pandas") from exc


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
    vwap: float
    above_vwap: bool
    ema_bullish: bool
    macd_bullish: bool
    rsi_ok: bool
    setup_score: int
    setup_label: str


def env(name: str) -> str:
    value = os.getenv(name, "").strip()
    if not value:
        raise SystemExit(f"MISSING_ENV={name}")
    return value


def rsi(series: pd.Series, period: int = 14) -> pd.Series:
    delta = series.diff()
    gain = delta.clip(lower=0)
    loss = -delta.clip(upper=0)
    avg_gain = gain.ewm(alpha=1 / period, adjust=False).mean()
    avg_loss = loss.ewm(alpha=1 / period, adjust=False).mean()
    rs = avg_gain / avg_loss.replace(0, math.nan)
    return 100 - (100 / (1 + rs))


def fetch_alpaca_bars(symbol: str, timeframe: str, days: int) -> pd.DataFrame:
    key = env("ALPACA_API_KEY")
    secret = env("ALPACA_SECRET_KEY")
    feed = os.getenv("ALPACA_DATA_FEED", "iex").strip() or "iex"

    end = datetime.now(timezone.utc)
    start = end - timedelta(days=days)

    url = f"https://data.alpaca.markets/v2/stocks/{symbol}/bars"
    params = {
        "timeframe": timeframe,
        "start": start.isoformat().replace("+00:00", "Z"),
        "end": end.isoformat().replace("+00:00", "Z"),
        "adjustment": "raw",
        "feed": feed,
        "limit": 10000,
    }
    headers = {
        "APCA-API-KEY-ID": key,
        "APCA-API-SECRET-KEY": secret,
    }

    resp = requests.get(url, params=params, headers=headers, timeout=30)
    if resp.status_code >= 400:
        raise RuntimeError(f"{symbol}: Alpaca HTTP {resp.status_code}: {resp.text[:500]}")

    payload = resp.json()
    bars = payload.get("bars", [])
    if not bars:
        raise RuntimeError(f"{symbol}: no bars returned")

    df = pd.DataFrame(bars)
    df = df.rename(columns={
        "t": "timestamp",
        "o": "open",
        "h": "high",
        "l": "low",
        "c": "close",
        "v": "volume",
        "n": "trade_count",
        "vw": "alpaca_vwap",
    })
    df["timestamp"] = pd.to_datetime(df["timestamp"], utc=True)
    df = df.set_index("timestamp").sort_index()
    return df


def calculate_signal(ticker: str, df: pd.DataFrame) -> SignalResult:
    close = df["close"].astype(float)
    high = df["high"].astype(float)
    low = df["low"].astype(float)
    volume = df["volume"].fillna(0).astype(float)

    if "alpaca_vwap" in df.columns and df["alpaca_vwap"].notna().any():
        vwap_series = df["alpaca_vwap"].astype(float)
    else:
        typical = (high + low + close) / 3
        vwap_series = (typical * volume).cumsum() / volume.replace(0, math.nan).cumsum()

    ema8 = close.ewm(span=8, adjust=False).mean()
    ema21 = close.ewm(span=21, adjust=False).mean()
    rsi14 = rsi(close, 14)

    ema12 = close.ewm(span=12, adjust=False).mean()
    ema26 = close.ewm(span=26, adjust=False).mean()
    macd = ema12 - ema26
    macd_signal = macd.ewm(span=9, adjust=False).mean()
    macd_hist = macd - macd_signal

    last = df.index[-1]

    last_close = float(close.loc[last])
    last_volume = int(volume.loc[last])
    last_ema8 = float(ema8.loc[last])
    last_ema21 = float(ema21.loc[last])
    last_rsi = float(rsi14.loc[last])
    last_macd = float(macd.loc[last])
    last_macd_signal = float(macd_signal.loc[last])
    last_macd_hist = float(macd_hist.loc[last])
    last_vwap = float(vwap_series.loc[last])

    above_vwap = last_close > last_vwap
    ema_bullish = last_ema8 > last_ema21
    macd_bullish = last_macd > last_macd_signal and last_macd_hist > 0
    rsi_ok = 40 <= last_rsi <= 72

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
        vwap=last_vwap,
        above_vwap=above_vwap,
        ema_bullish=ema_bullish,
        macd_bullish=macd_bullish,
        rsi_ok=rsi_ok,
        setup_score=score,
        setup_label=label,
    )


def row(result: SignalResult) -> dict[str, Any]:
    return {
        "ticker": result.ticker,
        "close": round(result.close, 4),
        "volume": result.volume,
        "indicators": {
            "vwap": round(result.vwap, 4),
            "ema8": round(result.ema8, 4),
            "ema21": round(result.ema21, 4),
            "rsi14": round(result.rsi14, 4),
            "macd": round(result.macd, 6),
            "macd_signal": round(result.macd_signal, 6),
            "macd_hist": round(result.macd_hist, 6),
        },
        "conditions": {
            "above_vwap": result.above_vwap,
            "ema8_above_ema21": result.ema_bullish,
            "macd_bullish": result.macd_bullish,
            "rsi_in_review_zone": result.rsi_ok,
        },
        "setup_score": result.setup_score,
        "setup_label": result.setup_label,
    }


def write_md(path: Path, payload: dict[str, Any]) -> None:
    lines = [
        "# TradingRobotPlug Alpaca Daily Proof",
        "",
        f"Generated UTC: `{payload['generated_utc']}`",
        f"Data source: `{payload['data_source']}`",
        f"Timeframe: `{payload['timeframe']}`",
        f"Lookback days: `{payload['lookback_days']}`",
        "",
        "## Purpose",
        "",
        "This artifact proves TradingRobotPlug can pull broker-grade market data through Alpaca, calculate strategy indicators, score robot-plugin conditions, and publish reviewable evidence.",
        "",
        "This is not financial advice. This is not a profit claim. This is not live trade approval.",
        "",
        "## Setup Scores",
        "",
        "| Ticker | Close | Score | Label | VWAP | EMA | MACD | RSI |",
        "|---|---:|---:|---|---|---|---|---|",
    ]

    for item in payload["results"]:
        c = item["conditions"]
        lines.append(
            f"| {item['ticker']} | {item['close']} | {item['setup_score']} | {item['setup_label']} | "
            f"{'PASS' if c['above_vwap'] else 'FAIL'} | "
            f"{'PASS' if c['ema8_above_ema21'] else 'FAIL'} | "
            f"{'PASS' if c['macd_bullish'] else 'FAIL'} | "
            f"{'PASS' if c['rsi_in_review_zone'] else 'FAIL'} |"
        )

    lines += ["", "## Indicator Details", ""]

    for item in payload["results"]:
        ind = item["indicators"]
        cond = item["conditions"]
        lines += [
            f"### {item['ticker']}",
            "",
            f"- Close: `{item['close']}`",
            f"- Volume: `{item['volume']}`",
            f"- VWAP: `{ind['vwap']}`",
            f"- EMA 8: `{ind['ema8']}`",
            f"- EMA 21: `{ind['ema21']}`",
            f"- RSI 14: `{ind['rsi14']}`",
            f"- MACD: `{ind['macd']}`",
            f"- MACD Signal: `{ind['macd_signal']}`",
            f"- MACD Histogram: `{ind['macd_hist']}`",
            "",
            "Conditions:",
            f"- Above VWAP: `{cond['above_vwap']}`",
            f"- EMA 8 above EMA 21: `{cond['ema8_above_ema21']}`",
            f"- MACD bullish: `{cond['macd_bullish']}`",
            f"- RSI review zone: `{cond['rsi_in_review_zone']}`",
            "",
        ]

    if payload["errors"]:
        lines += ["## Errors", ""]
        for err in payload["errors"]:
            lines.append(f"- `{err['ticker']}`: {err['error']}")
        lines.append("")

    lines += [
        "## Risk Rules",
        "",
        "- Paper-first only.",
        "- No execution order is created by this report.",
        "- No live trading approval is implied.",
        "- Any strategy must pass separate forward testing and operator review.",
        "",
    ]

    path.write_text("\n".join(lines), encoding="utf-8")


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--out-dir", required=True)
    parser.add_argument("--symbols", default="TSLA,QQQ,SPY")
    parser.add_argument("--timeframe", default="15Min")
    parser.add_argument("--days", type=int, default=7)
    args = parser.parse_args()

    out_dir = Path(args.out_dir)
    out_dir.mkdir(parents=True, exist_ok=True)

    generated = datetime.now(timezone.utc).isoformat()
    stamp = datetime.now(timezone.utc).strftime("%Y%m%d")
    symbols = [s.strip().upper() for s in args.symbols.split(",") if s.strip()]

    results = []
    errors = []

    for symbol in symbols:
        try:
            df = fetch_alpaca_bars(symbol, args.timeframe, args.days)
            results.append(row(calculate_signal(symbol, df)))
        except Exception as exc:
            errors.append({"ticker": symbol, "error": str(exc)})

    payload = {
        "domain": "tradingrobotplug.com",
        "generated_utc": generated,
        "data_source": "Alpaca Market Data API",
        "timeframe": args.timeframe,
        "lookback_days": args.days,
        "symbols": symbols,
        "results": results,
        "errors": errors,
        "risk_disclaimer": "Not financial advice. Not a profit claim. Not live trade approval.",
    }

    dated_json = out_dir / f"alpaca-daily-proof-{stamp}.json"
    dated_md = out_dir / f"alpaca-daily-proof-{stamp}.md"
    latest_json = out_dir / "latest-alpaca-daily-proof.json"
    latest_md = out_dir / "latest-alpaca-daily-proof.md"
    index_md = out_dir / "index.md"

    json_text = json.dumps(payload, indent=2)
    dated_json.write_text(json_text, encoding="utf-8")
    latest_json.write_text(json_text, encoding="utf-8")
    write_md(dated_md, payload)
    latest_md.write_text(dated_md.read_text(encoding="utf-8"), encoding="utf-8")

    index_lines = [
        "# TradingRobotPlug Alpaca Proof Index",
        "",
        f"Latest generated UTC: `{generated}`",
        "",
        "## Latest",
        "",
        "- [Latest Alpaca Markdown](./latest-alpaca-daily-proof.md)",
        "- [Latest Alpaca JSON](./latest-alpaca-daily-proof.json)",
        "",
        "## Dated Artifacts",
        "",
    ]

    for p in sorted(out_dir.glob("alpaca-daily-proof-*.md"), reverse=True)[:40]:
        d = p.stem.replace("alpaca-daily-proof-", "")
        index_lines.append(f"- [{d} Markdown](./{p.name}) · [JSON](./alpaca-daily-proof-{d}.json)")

    index_md.write_text("\n".join(index_lines) + "\n", encoding="utf-8")

    print(f"ALPACA_PROOF_MD={dated_md}")
    print(f"ALPACA_PROOF_JSON={dated_json}")
    print(f"RESULT_COUNT={len(results)}")
    print(f"ERROR_COUNT={len(errors)}")

    if not results:
        raise SystemExit("NO_ALPACA_MARKET_ROWS_GENERATED")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
