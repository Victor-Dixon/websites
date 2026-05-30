# Hostinger Site Install Plan 013

- **root:** `/data/data/com.termux/files/home/projects/websites`
- **plan_dir:** `/data/data/com.termux/files/home/projects/websites/_hostinger_plan`
- **dist_dir:** `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist`

## Domains

### freerideinvestor

- **checklist:** `/data/data/com.termux/files/home/projects/websites/_hostinger_plan/freerideinvestor/install_checklist.md`
- **theme_policy:** `rebuild_from_scratch`
- **status:** `ready_for_hostinger_staging_install`
- **model:** Media brand / trading discipline / content engine.
- **plugins:**
  - `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/freerideinvestor-content-engine-0.1.0.zip`
  - `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/dreamos-trading-tools-0.1.0.zip`
- **cpts:**
  - `cheat_sheet`
  - `free_investor`
  - `tbow_tactics`
- **shortcodes:**
  - `[current_year]`
  - `[custom_message]`
  - `[cheat_sheet]`
  - `[tbow_tactics]`
- **pages to rebuild:**
  - Home
  - Trading Journal
  - Cheat Sheets
  - Tbow Tactics
  - Archive Tools
  - Education
  - About
  - Contact / Email Capture

### tradingrobotplug

- **checklist:** `/data/data/com.termux/files/home/projects/websites/_hostinger_plan/tradingrobotplug/install_checklist.md`
- **theme_policy:** `rebuild_from_scratch`
- **status:** `ready_for_hostinger_staging_install`
- **model:** Trading tools / product demo / analytics lead capture.
- **plugins:**
  - `/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/dreamos-trading-tools-0.1.0.zip`
- **pages to rebuild:**
  - Home
  - Tools Demo
  - Trading Data
  - Product Roadmap
  - Contact / Lead Capture

### dadudekc

- **checklist:** `/data/data/com.termux/files/home/projects/websites/_hostinger_plan/dadudekc/install_checklist.md`
- **theme_policy:** `rebuild_from_scratch`
- **status:** `hold_until_domain_value_confirmed`
- **model:** Personal/local/community legacy brand.
- **plugins:**
  - none
- **pages to rebuild:**
  - Home
  - Community
  - Portfolio
  - Contact
- **hold_reason:** No deployable package yet. dadudekc-community-features is staged but not promoted/package-gated.

## Global Rules

- Install on Hostinger staging first.
- Do not archive old GitHub repos until GitHub Architect produces deprecation manifest proof.
- Do not preserve old themes as canonical.
- Keep websites/ as SSOT.

