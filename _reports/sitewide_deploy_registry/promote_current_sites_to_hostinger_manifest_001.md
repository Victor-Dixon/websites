# Promote current sites to Hostinger manifest

generated=2026-06-04T18:42:15-05:00
status=PASS

## Manifest
```yaml
version: 1
purpose: Canonical Hostinger static site deployment manifest.
provider: hostinger
ssh_alias: hostinger-weareswarm
sites:
  - domain: ariajet.site
    source_dir: sites/production/ariajet.site
    remote_root: /home/u996867598/domains/ariajet.site/public_html
    url: https://ariajet.site/
    type: static_mvp
    status: live
    verify_markers:
      - "Aria Jet"
      - "Phone Cases"
      - "Phones we fixed"
  - domain: houstonsipqueen.com
    source_dir: sites/production/houstonsipqueen.com
    remote_root: /home/u996867598/domains/houstonsipqueen.com/public_html
    url: https://houstonsipqueen.com/
    type: static_mobile_bartending_landing
    status: live
    verify_markers:
      - "Houston Sip Queen"
      - "Mobile Bartending"
      - "Host provides alcohol"
  - domain: tradingrobotplug.com
    source_dir: sites/production/tradingrobotplug.com
    remote_root: /home/u996867598/domains/tradingrobotplug.com/public_html
    url: https://tradingrobotplug.com/
    type: static_trading_robot_plugin_lab
    status: live
    verify_markers:
      - "TradingRobotPlug"
      - "Trading Robot Plugins"
      - "Proof Dashboard"
```

## Verify output
```text
SITE_COUNT=3
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
VERIFY=PASS
```
