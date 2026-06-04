# TradingRobotPlug Daily Market Proof

Generated UTC: `2026-06-04T23:10:50.214491+00:00`
Data window: `5d`
Interval: `15m`

## Purpose

This is a market-derived proof artifact. It shows that TradingRobotPlug can pull market data, calculate indicators, score setup conditions, and publish reviewable robot-plugin evidence.

This is not financial advice. This is not a profit claim. This is not live trade approval.

## Daily Setup Scores

| Ticker | Close | Score | Label | VWAP | EMA | MACD | RSI |
|---|---:|---:|---|---|---|---|---|
| TSLA | 418.46 | 50 | NO_TRADE_REVIEW | FAIL | FAIL | PASS | PASS |
| QQQ | 740.5 | 75 | WATCHLIST_SETUP | FAIL | PASS | PASS | PASS |
| SPY | 757.05 | 100 | A_SETUP_CANDIDATE | PASS | PASS | PASS | PASS |

## Indicator Details

### TSLA

- Close: `418.46`
- Volume: `2062601`
- EMA 8: `419.4755`
- EMA 21: `419.9614`
- RSI 14: `42.2269`
- MACD: `-0.602725`
- MACD Signal: `-0.694666`
- MACD Histogram: `0.091941`
- VWAP Proxy: `424.8243`

Conditions:
- Above VWAP proxy: `False`
- EMA 8 above EMA 21: `False`
- MACD bullish: `True`
- RSI review zone: `True`

### QQQ

- Close: `740.5`
- Volume: `4715845`
- EMA 8: `741.9727`
- EMA 21: `741.6196`
- RSI 14: `44.7437`
- MACD: `0.248113`
- MACD Signal: `0.141582`
- MACD Histogram: `0.106531`
- VWAP Proxy: `741.2935`

Conditions:
- Above VWAP proxy: `False`
- EMA 8 above EMA 21: `True`
- MACD bullish: `True`
- RSI review zone: `True`

### SPY

- Close: `757.05`
- Volume: `6839943`
- EMA 8: `757.5482`
- EMA 21: `756.9236`
- RSI 14: `53.179`
- MACD: `0.606495`
- MACD Signal: `0.572878`
- MACD Histogram: `0.033617`
- VWAP Proxy: `756.6794`

Conditions:
- Above VWAP proxy: `True`
- EMA 8 above EMA 21: `True`
- MACD bullish: `True`
- RSI review zone: `True`

## Risk Rules

- Paper-first only.
- No execution order is created by this report.
- No live trading approval is implied.
- Any strategy must pass separate forward testing and operator review.
