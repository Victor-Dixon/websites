# Raw Legacy Site Salvage Policy 016

status: `raw_salvage_ignored_from_git`
root: `/data/data/com.termux/files/home/projects/websites`

## Raw Salvage Dirs

### TradingRobotPlugWeb

- path: `/data/data/com.termux/files/home/projects/websites/TradingRobotPlugWeb`
- exists: `True`
- file_count: 116
- bytes: 386239
- policy: `local_raw_salvage_not_git_canonical`

### FreerideinvestorWebsite

- path: `/data/data/com.termux/files/home/projects/websites/FreerideinvestorWebsite`
- exists: `True`
- file_count: 99
- bytes: 552840
- policy: `local_raw_salvage_not_git_canonical`

### DaDudeKC-Website

- path: `/data/data/com.termux/files/home/projects/websites/DaDudeKC-Website`
- exists: `True`
- file_count: 26
- bytes: 108069
- policy: `local_raw_salvage_not_git_canonical`

## Canonical Git Paths

- `_configs/`
- `_github_deprecation_packets/`
- `_hostinger_build/`
- `_hostinger_plan/`
- `_reports/`
- `_shared_plugins/`
- `tools/`
- `README.md`

## Rule

Do not track raw top-level legacy site folders unless a later promotion manifest explicitly selects files.
