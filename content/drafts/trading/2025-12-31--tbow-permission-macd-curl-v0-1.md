---
title: 'TBOW Permission + MACD Curl (v0.1) — TSLA call scalps, tight stops'
date: 2025-12-31
pillar: execution
audience: active_traders
cta: Get the Plug
site_id: trading
---

# DRAFT NOT GENERATED (dry-run)

Below is the exact prompt that would be sent.

```
Write a blog post in the exact voice defined in VOICE_PROFILE. Do not drift.

VOICE_PROFILE:
<<<# Victor Voice Profile (SSOT)

Write like Victor:
- Direct, confident, builder energy.
- Short punchy lines. Minimal fluff.
- Concrete examples. Simple frameworks.
- Ends with a clear CTA.

Hard rules:
- No corporate jargon.
- No overexplaining.
- Use headings + bullets.
- Always include: Problem → Fix → Steps → CTA.

Signature patterns to use sometimes:
- "Here’s the move:"
- "If you’re doing ___ at night, you need a system."
- "Stop buying tools. Fix one workflow end-to-end."

Formatting rules:
- Output **markdown only**.
- Include a wrapper div + hero section (WordPress-friendly) when possible.
- Use these exact H2 headings (validator depends on them):
  - "## Problem"
  - "## Fix"
  - "## Steps"
  - "## Example"
  - "## CTA"
>>>

BRAND_PROFILE:
<<<audiences:
  - active_traders
  - algo_traders
  - prop_firm_traders

offer:
  name: "Trading Robot Plug"
  promise: "Automate your strategy without coding"
  cta_primary: "Get the Plug"

content_rules:
  word_count: [800, 1200]

seo:
  include_keywords: true
  include_internal_links: true
>>>

OPTIONAL_EXAMPLES (for style imitation):
<<<>>>

POST_BRIEF:
- title: TBOW Permission + MACD Curl (v0.1) — TSLA call scalps, tight stops
- audience: active_traders
- pillar: execution
- angle: Daily TSLA scalp rules + indicator release (v0.1)
- keywords: ['TSLA scalps', 'VWAP', 'MACD curl', 'Bollinger Bands', 'tight stops', 'TradingView indicator']
- CTA: Get the Plug
- include_disclaimer: true
- include_version_notes: true
- include_alerts_section: true
- include_how_to_use: true

REQUIRED CONTENT (must include verbatim sections/blocks):
1) "Permission" rules (30m EMA + MACD) and why calls are reversion-only when below EMA.
2) Entry trigger checklist (VWAP reclaim + MACD curl or cross + RSI > 50).
3) Tight stop rules with explicit invalidation.
4) Take-profit logic (BB mid, upper band, runner conditions).
5) Hold decision rules (VWAP holds + no lower-lows, exit on VWAP break + MACD curl down).
6) A short TSLA example trade walkthrough (entry, stop, exit) using today’s rules.
7) Disclaimers: educational only, not financial advice, tight-stop risk notes.
8) Publish Pine Script v0.1 in a fenced code block with a short "How to Use" list + alert notes.
9) Version notes: v0.1 initial release, note future improvements.

PINE SCRIPT (v0.1):
<<<//@version=5
indicator("TBOW Permission + MACD Curl (VWAP/BB/RSI)", overlay=true, max_labels_count=500)

//────────────────────────────────────────────────────────────────────
// Inputs
//────────────────────────────────────────────────────────────────────
htfTf      = input.timeframe("30", "HTF Permission TF")
htfEmaLen  = input.int(50, "HTF EMA Length", minval=1)

bbLen      = input.int(20, "BB Length", minval=1)
bbMult     = input.float(2.0, "BB Mult", step=0.1)

emaFastLen = input.int(9, "LTF EMA Fast", minval=1)
emaSlowLen = input.int(21, "LTF EMA Slow", minval=1)

rsiLen     = input.int(14, "RSI Length", minval=1)

showBG     = input.bool(true, "Background Permission")
showLabels = input.bool(true, "Show Labels")

//────────────────────────────────────────────────────────────────────
// LTF Indicators
//────────────────────────────────────────────────────────────────────
vwap  = ta.vwap(hlc3)

emaFast = ta.ema(close, emaFastLen)
emaSlow = ta.ema(close, emaSlowLen)

basis = ta.sma(close, bbLen)
dev   = bbMult * ta.stdev(close, bbLen)
upper = basis + dev
lower = basis - dev

rsi = ta.rsi(close, rsiLen)

[macdLine, signalLine, histLine] = ta.macd(close, 12, 26, 9)
macdDelta = macdLine - macdLine[1]

// “Curl” = momentum slope flips direction
macdCurlUp   = ta.crossover(macdDelta, 0)
macdCurlDown = ta.crossunder(macdDelta, 0)

macdXUp   = ta.crossover(macdLine, signalLine)
macdXDown = ta.crossunder(macdLine, signalLine)

//────────────────────────────────────────────────────────────────────
// HTF “Permission” (30m by default)
//────────────────────────────────────────────────────────────────────
htfClose = request.security(syminfo.tickerid, htfTf, close)
htfEma   = request.security(syminfo.tickerid, htfTf, ta.ema(close, htfEmaLen))
htfSlope = request.security(syminfo.tickerid, htfTf, ta.ema(close, htfEmaLen) - ta.ema(close, htfEmaLen)[1])

htfBull = (htfClose > htfEma) and (htfSlope > 0)
htfBear = (htfClose < htfEma) and (htfSlope < 0)

//────────────────────────────────────────────────────────────────────
// Setups (simple + readable)
//────────────────────────────────────────────────────────────────────
reclaimVWAP = close > vwap and close[1] <= vwap
loseVWAP    = close < vwap and close[1] >= vwap

callSetup =
     (htfBull or not htfBear) and           // allow scalps when HTF not screaming bear
     close > vwap and
     close > basis and
     rsi > 50 and
     (macdCurlUp or macdXUp)

putSetup =
     (htfBear or not htfBull) and
     close < vwap and
     close < basis and
     rsi < 50 and
     (macdCurlDown or macdXDown)

// Exits (tight-stop logic helpers)
callExit = loseVWAP or macdCurlDown or macdXDown
putExit  = reclaimVWAP or macdCurlUp or macdXUp

//────────────────────────────────────────────────────────────────────
// Plots
//────────────────────────────────────────────────────────────────────
plot(vwap, "VWAP", linewidth=2)
plot(emaFast, "EMA Fast", linewidth=1)
plot(emaSlow, "EMA Slow", linewidth=1)
plot(basis, "BB Mid", linewidth=1)
plot(upper, "BB Upper", linewidth=1)
plot(lower, "BB Lower", linewidth=1)

// Permission background
bgCol =
    htfBull ? color.new(color.green, 88) :
    htfBear ? color.new(color.red, 88) :
              color.new(color.gray, 92)

bgcolor(showBG ? bgCol : na)

// Signals (X marks like you wanted)
plotshape(callSetup, title="CALL", style=shape.xcross, location=location.belowbar, size=size.small, text="CALL")
plotshape(putSetup,  title="PUT",  style=shape.xcross, location=location.abovebar, size=size.small, text="PUT")

// MACD Curl markers (info-only)
plotshape(macdCurlUp,   title="MACD Curl Up",   style=shape.triangleup,   location=location.belowbar, size=size.tiny, text="curl↑")
plotshape(macdCurlDown, title="MACD Curl Down", style=shape.triangledown, location=location.abovebar, size=size.tiny, text="curl↓")

// Optional labels
if showLabels and callSetup
    label.new(bar_index, low, "CALL\nVWAP hold", style=label.style_label_up, textcolor=color.white)
if showLabels and putSetup
    label.new(bar_index, high, "PUT\nVWAP reject", style=label.style_label_down, textcolor=color.white)

if showLabels and callExit
    label.new(bar_index, high, "CALL EXIT\n(VWAP/MACD)", style=label.style_label_down, textcolor=color.white)
if showLabels and putExit
    label.new(bar_index, low, "PUT EXIT\n(VWAP/MACD)", style=label.style_label_up, textcolor=color.white)

// Alerts
alertcondition(callSetup, "CALL Setup", "TBOW CALL setup: HTF ok + VWAP + BB mid + RSI + MACD curl/cross")
alertcondition(putSetup,  "PUT Setup",  "TBOW PUT setup: HTF ok + VWAP + BB mid + RSI + MACD curl/cross")

alertcondition(callExit, "CALL Exit", "CALL exit trigger: VWAP loss or MACD curl/cross down")
alertcondition(putExit,  "PUT Exit",  "PUT exit trigger: VWAP reclaim or MACD curl/cross up")
>>>

OUTPUT:
- Markdown only
- 800–1200 words
- Use H2/H3 headings
- Include the required sections and disclaimers
- End with the CTA

WRITE:

```
