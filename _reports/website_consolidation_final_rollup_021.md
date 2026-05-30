# Website Consolidation Final Rollup 021

## Status

PASS.

## SSOT

`~/projects/websites`

## Closed Lanes

- Website repo consolidation
- Custom plugin classification
- Shared plugin staging
- Hostinger domain model manifest
- FreeRideInvestor plugin package
- DreamOS Trading Tools plugin package
- Reusable Hostinger plan generator
- Hostinger per-domain install checklists
- Legacy repo deprecation packets
- Legacy repo deprecation commits
- Legacy repo deprecation pushes
- Legacy repo archive

## Archived Legacy Repos

- `Victor-Dixon/TradingRobotPlugWeb`
- `Victor-Dixon/FreerideinvestorWebsite`
- `Victor-Dixon/DaDudeKC-Website`

## Upload Candidates

- `_hostinger_build/dist/freerideinvestor-content-engine-0.1.0.zip`
- `_hostinger_build/dist/dreamos-trading-tools-0.1.0.zip`

## Hostinger Install Order

### freerideinvestor

1. Upload `freerideinvestor-content-engine-0.1.0.zip`
2. Activate plugin
3. Confirm CPTs:
   - `cheat_sheet`
   - `free_investor`
   - `tbow_tactics`
4. Upload `dreamos-trading-tools-0.1.0.zip`
5. Activate plugin
6. Create shortcode smoke page:
   - `[current_year]`
   - `[custom_message]`
   - `[cheat_sheet]`
   - `[tbow_tactics]`

### tradingrobotplug

1. Upload `dreamos-trading-tools-0.1.0.zip`
2. Activate plugin
3. Confirm no fatal error

### dadudekc

Hold until domain value confirmed.

## Theme Policy

Rebuild all themes from scratch. Old themes are reference only.

## Rule

No old website repos remain active as canonical sources.
