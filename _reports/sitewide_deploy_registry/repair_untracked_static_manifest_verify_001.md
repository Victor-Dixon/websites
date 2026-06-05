# Repair untracked static manifest verification

generated=2026-06-04T18:46:36-05:00
status=FAIL

## Manifest domains
```text
  - domain: ariajet.site
  - domain: houstonsipqueen.com
  - domain: tradingrobotplug.com
  - domain: freerideinvestor.com
  - domain: weareswarm.site
  - domain: xthunder.site
```

## Verify output
```text
SITE_COUNT=6
--- ariajet.site ---
URL=https://ariajet.site/
HTTP_STATUS=200
CONTENT_TYPE=text/html
MARKER PASS Aria Jet
MARKER PASS Phone Cases
MARKER PASS Phones we fixed
HASH_LINKS=PASS
--- houstonsipqueen.com ---
URL=https://houstonsipqueen.com/
HTTP_STATUS=200
CONTENT_TYPE=text/html
MARKER PASS Houston Sip Queen
MARKER PASS Mobile Bartending
MARKER PASS Host provides alcohol
HASH_LINKS=PASS
--- tradingrobotplug.com ---
URL=https://tradingrobotplug.com/
HTTP_STATUS=200
CONTENT_TYPE=text/html
MARKER PASS TradingRobotPlug
MARKER PASS Trading Robot Plugins
MARKER PASS Proof Dashboard
HASH_LINKS=PASS
--- freerideinvestor.com ---
URL=https://freerideinvestor.com/
HTTP_STATUS=200
CONTENT_TYPE=text/html
MARKER PASS FreeRideInvestor
HASH_LINKS=FAIL
--- weareswarm.site ---
URL=https://weareswarm.site/
HTTP_STATUS=200
CONTENT_TYPE=text/html
MARKER PASS WeAreSwarm
HASH_LINKS=PASS
--- xthunder.site ---
URL=https://xthunder.site/
HTTP_STATUS=200
CONTENT_TYPE=text/html
MARKER PASS xThunder
HASH_LINKS=FAIL
VERIFY=FAIL
FAILURE=freerideinvestor.com: href hash links remain
FAILURE=xthunder.site: href hash links remain
```
