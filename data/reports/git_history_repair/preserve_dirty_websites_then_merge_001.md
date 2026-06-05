# Preserve dirty websites work before phone-canonical xThunder merge

Generated: 2026-06-05T01:06:17-05:00

## Backup branch

`phone/pre-xthunder-dirty-save-20260605_010617`

## Reason

The phone-canonical xThunder merge requires a clean worktree before reset to `origin/master`.
Current Termux tree contained uncommitted phone-side website work.

## Preserved status

```text
 M runtime/deploy/hostinger_sites_manifest.yaml
?? _reports/sitewide_deploy_registry/repair_untracked_static_manifest_verify_001.md
?? runtime/tasks/websites/promote_untracked_static_sites_to_manifest_001.yaml
?? runtime/tasks/websites/repair_untracked_static_manifest_verify_001.yaml
?? sites/production/freerideinvestor.com/
?? sites/production/weareswarm.site/
?? sites/production/xthunder.site/
```

## Next action

After this preservation commit, return to `master`, reset to `origin/master`, checkout the xThunder patch/script from `origin/desktop/xthunder-live`, and run the merge script.
