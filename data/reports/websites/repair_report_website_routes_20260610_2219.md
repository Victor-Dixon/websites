# Website route repair proof

generated=2026-06-10T22:19:00Z
branch=cursor/website-repair-7e11

## Scope

- dadudekc.site missions/buttons
- dadudekc.site/spark-dashboard routing
- weareswarm.online/focus missing Trading Plan
- project/tasks board pages

## Source patches

- `runtime/themes/dreamos-emergence/functions.php`
  - Added a small WordPress route alias for `/missions` and `/mission`.
  - Target route: `/meridian-dispatch/`.
- `_deploy/weareswarm/focus/index.html`
  - Added checked-in `/focus/` fallback with the missing `Trading Plan` card restored.
- `_deploy/weareswarm/project/tasks/index.html`
  - Added task-board alias to `/tasks/`.
- `_deploy/weareswarm/projects/tasks/index.html`
  - Added task-board alias to `/tasks/`.

## Local verification

```text
HTML_PARSE=PASS _deploy/weareswarm/focus/index.html bytes=8029
HTML_PARSE=PASS _deploy/weareswarm/project/tasks/index.html bytes=373
HTML_PARSE=PASS _deploy/weareswarm/projects/tasks/index.html bytes=373
MARKER=PASS _deploy/weareswarm/focus/index.html Trading Plan
MARKER=PASS _deploy/weareswarm/focus/index.html STATUS=VISIBLE
MARKER=PASS _deploy/weareswarm/focus/index.html Revenue Operator
MARKER=PASS _deploy/weareswarm/project/tasks/index.html url=/tasks/
MARKER=PASS _deploy/weareswarm/projects/tasks/index.html url=/tasks/
STATIC_VERIFY=PASS
```

Note: local PHP CLI is not installed in the cloud image, so local `php -l` could not run.

## Remote deploy proof

Targeted Hostinger repair only; no full-site static overwrite.

```text
BACKUP=PASS focus/index.html
UPLOAD=PASS focus/index.html
BACKUP=SKIP missing project/tasks/index.html
UPLOAD=PASS project/tasks/index.html
BACKUP=SKIP missing projects/tasks/index.html
UPLOAD=PASS projects/tasks/index.html
BACKUP=PASS dadudekc.site/functions.php
PATCH=PASS dadudekc route alias
REMOTE_PHP_LINT_OUT=No syntax errors detected in domains/dadudekc.site/public_html/wp-content/themes/dreamos-emergence/functions.php
REMOTE_DEPLOY=PASS
```

## Live verification after repair

```text
LIVE_AFTER https://dadudekc.site/missions status=200 final=https://dadudekc.site/spark-login/?redirect_to=%2Fmeridian-dispatch%2F markers=['Command Post', 'Log In']
LIVE_AFTER https://dadudekc.site/mission status=200 final=https://dadudekc.site/spark-login/?redirect_to=%2Fmeridian-dispatch%2F markers=['Command Post', 'Log In']
LIVE_AFTER https://dadudekc.site/spark-dashboard status=200 final=https://dadudekc.site/spark-dashboard/ markers=['Command Post', 'Meridian Dispatch', 'Log In']
LIVE_AFTER https://weareswarm.online/focus status=200 final=https://www.weareswarm.online/focus/ markers=['Trading Plan', 'STATUS=VISIBLE']
LIVE_AFTER https://weareswarm.online/project/tasks status=200 final=https://www.weareswarm.online/project/tasks/ markers=['WeAreSwarm Tasks']
LIVE_AFTER https://weareswarm.online/projects/tasks/ status=200 final=https://www.weareswarm.online/projects/tasks/ markers=['WeAreSwarm Tasks']
LIVE_AFTER https://weareswarm.online/tasks/ status=200 final=https://www.weareswarm.online/tasks/ markers=['Active queue']
```
