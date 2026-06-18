# FreeRideInvestor Clean Site Root

Canonical site root for `freerideinvestor.com`.

## Direction

Clean rebuild. Do not continue the old WordPress theme as canonical.

## Core Product

Agent-powered trading journal:

1. Import fills.
2. Match fills to market candles.
3. Reconstruct behavior timeline.
4. Generate behavior tags.
5. Score discipline.
6. Recommend the next operator rule.

## Pages

- `index.html` — sales funnel homepage
- `blog.html` — personal trading journal blog index
- `blog/day-7-the-question-getting-louder.html` — Day 7 journal entry
- `early-access.html` — intake/lead capture placeholder
- `replay-proof.html` — product proof page
- `day-trade-planner.html` — day planner rebuild placeholder

## Theme Assets

- `assets/css/freeride-theme.css` — shared glass/gradient theme layer
- `assets/js/freeride-market-field.js` — Three.js market-field background animation

The static pages use Tailwind via the CDN for utility classes and Three.js r184 via an ES module CDN import for progressive visual enhancement.

## Salvage Policy

Preserve only:

- custom plugins
- day trade planner logic
- trading workflow utilities
- useful shortcodes/components

Archive the old theme/pages unless promoted by manifest.
