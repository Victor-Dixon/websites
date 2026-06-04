# TradingRobotPlug Alpaca Daily Proof

Generated UTC: `2026-06-04T23:18:50.096674+00:00`
Data source: `Alpaca Market Data API`
Timeframe: `15Min`
Lookback days: `7`

## Purpose

This artifact proves TradingRobotPlug can pull broker-grade market data through Alpaca, calculate strategy indicators, score robot-plugin conditions, and publish reviewable evidence.

This is not financial advice. This is not a profit claim. This is not live trade approval.

## Setup Scores

| Ticker | Close | Score | Label | VWAP | EMA | MACD | RSI |
|---|---:|---:|---|---|---|---|---|
| TSLA | 417.36 | 10 | NO_TRADE_REVIEW | FAIL | FAIL | FAIL | FAIL |
| QQQ | 738.6 | 10 | NO_TRADE_REVIEW | FAIL | FAIL | FAIL | FAIL |
| SPY | 756.16 | 50 | NO_TRADE_REVIEW | FAIL | PASS | FAIL | PASS |

## Indicator Details

### TSLA

- Close: `417.36`
- Volume: `107`
- VWAP: `417.36`
- EMA 8: `418.691`
- EMA 21: `419.5148`
- RSI 14: `38.8084`
- MACD: `-0.779539`
- MACD Signal: `-0.671671`
- MACD Histogram: `-0.107867`

Conditions:
- Above VWAP: `False`
- EMA 8 above EMA 21: `False`
- MACD bullish: `False`
- RSI review zone: `False`

### QQQ

- Close: `738.6`
- Volume: `60`
- VWAP: `738.6`
- EMA 8: `740.0559`
- EMA 21: `740.6204`
- RSI 14: `38.25`
- MACD: `-0.243829`
- MACD Signal: `0.176361`
- MACD Histogram: `-0.42019`

Conditions:
- Above VWAP: `False`
- EMA 8 above EMA 21: `False`
- MACD bullish: `False`
- RSI review zone: `False`

### SPY

- Close: `756.16`
- Volume: `45`
- VWAP: `756.16`
- EMA 8: `756.8569`
- EMA 21: `756.6621`
- RSI 14: `46.498`
- MACD: `0.402856`
- MACD Signal: `0.636847`
- MACD Histogram: `-0.233991`

Conditions:
- Above VWAP: `False`
- EMA 8 above EMA 21: `True`
- MACD bullish: `False`
- RSI review zone: `True`

## Risk Rules

- Paper-first only.
- No execution order is created by this report.
- No live trading approval is implied.
- Any strategy must pass separate forward testing and operator review.
