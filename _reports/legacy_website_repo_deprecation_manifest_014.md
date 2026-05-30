# Legacy Website Repo Deprecation Manifest 014

status: `prepared_not_archived`
websites_ssot: `/data/data/com.termux/files/home/projects/websites`

## Repos

### TradingRobotPlugWeb

- local_path: `/data/data/com.termux/files/home/projects/TradingRobotPlugWeb`
- exists: `True`
- domain_model: `tradingrobotplug`
- deprecation_packet: `/data/data/com.termux/files/home/projects/websites/_github_deprecation_packets/TradingRobotPlugWeb/README.deprecated.md`
- archive_ready_after_review: `True`
- delete_approved: `False`
- plugin_artifacts:
  - `dreamos-trading-tools-0.1.0.zip`

### FreerideinvestorWebsite

- local_path: `/data/data/com.termux/files/home/projects/FreerideinvestorWebsite`
- exists: `True`
- domain_model: `freerideinvestor`
- deprecation_packet: `/data/data/com.termux/files/home/projects/websites/_github_deprecation_packets/FreerideinvestorWebsite/README.deprecated.md`
- archive_ready_after_review: `True`
- delete_approved: `False`
- plugin_artifacts:
  - `freerideinvestor-content-engine-0.1.0.zip`
  - `dreamos-trading-tools-0.1.0.zip`

### DaDudeKC-Website

- local_path: `/data/data/com.termux/files/home/projects/DaDudeKC-Website`
- exists: `True`
- domain_model: `dadudekc`
- deprecation_packet: `/data/data/com.termux/files/home/projects/websites/_github_deprecation_packets/DaDudeKC-Website/README.deprecated.md`
- archive_ready_after_review: `True`
- delete_approved: `False`
- plugin_artifacts:
  - none

## Rules

- No destructive deletion.
- Archive only after review.
- Keep websites/ as SSOT.
- Themes rebuild from scratch.
- Plugin artifacts under _hostinger_build/dist are canonical upload candidates.
