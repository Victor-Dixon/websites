# Phone-canonical merge plan

## Decision

**Canonical history:** `origin/master` (phone / Termux push — Dream.OS worklog spine)  
**Salvage from desktop:** `sites/production/websites/xthunder.site` + deploy config + tasks/reports

## Windows blocker

Phone `master` contains paths with backslashes (invalid on Windows):

```text
collected/hostinger/.../assets\css\stats.css
```

Git on Windows cannot `checkout`, `read-tree`, or merge `origin/master` locally.  
**Merge must finish on phone (Termux/Linux) or GitHub Linux runners.**

## Desktop already on GitHub

Branch: `desktop/xthunder-live` (full desktop line, backup)

## Phone finish lane (Termux)

```bash
cd "$HOME/projects/websites"
git fetch origin
git pull origin master   # phone canonical
bash runtime/scripts/merge_phone_canonical_xthunder_001.sh
```

Patch applied: `data/reports/git_history_repair/xthunder_phone_canonical_salvage.patch`

## After phone push

On Windows desktop (working copy stays on desktop line):

```bash
cd /d/websites
git fetch origin
# Do NOT reset to master on Windows until invalid paths are removed from phone repo
git checkout desktop/xthunder-live
```

Long-term: delete or rename invalid `collected/hostinger/...` paths on phone/Linux so Windows can track `master` again.
