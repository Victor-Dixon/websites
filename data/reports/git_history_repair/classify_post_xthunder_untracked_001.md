# Classify post-xThunder untracked files

Generated: 2026-06-05T01:11:31-05:00

## Current branch

```text
master
```

## Untracked files

```text
?? data/reports/git_history_repair/classify_post_xthunder_untracked_001.md
?? data/reports/git_history_repair/desktop_files_20260604_163432.txt
?? data/reports/git_history_repair/desktop_only_files_20260604_163432.txt
?? data/reports/git_history_repair/dirty_index_20260604_163432.patch
?? data/reports/git_history_repair/dirty_worktree_20260604_163432.patch
?? data/reports/git_history_repair/finalize_applied_xthunder_patch_001.md
?? data/reports/git_history_repair/remote_files_20260604_163432.txt
?? data/reports/git_history_repair/remote_only_files_20260604_163432.txt
?? data/reports/git_history_repair/salvage_remote_websites_artifacts_20260604_164053.md
?? data/reports/git_history_repair/stage_xthunder_allowlist_and_promote_001.md
?? data/reports/git_history_repair/websites_history_split_20260604_163432.md
?? ops/deployment/simple_wordpress_deployer.py
?? ops/deployment/unified_deployer.py
```

## Initial classification

### Keep as repair evidence candidates

- `data/reports/git_history_repair/*.txt`
- `data/reports/git_history_repair/*.patch`
- `data/reports/git_history_repair/*.md`

### Review as possible deployment tooling

- `ops/deployment/simple_wordpress_deployer.py`
- `ops/deployment/unified_deployer.py`

## Rule

Do not delete. Next lane should inspect deployer scripts, then either:
1. commit them as valid ops tooling,
2. move them to reports/quarantine, or
3. leave them local if they contain secrets or machine-specific assumptions.
