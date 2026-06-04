# Sitewide hash link cleanup

generated=2026-06-04T18:37:00-05:00
status=PASS

## Patch summary
```json
{
  "changed_files": [
    {
      "file": "sites/production/ariajet.site/index.html",
      "hash_links_replaced": 7,
      "site": "ariajet.site"
    },
    {
      "file": "sites/production/houstonsipqueen.com/index.html",
      "hash_links_replaced": 9,
      "site": "houstonsipqueen.com"
    },
    {
      "file": "sites/production/tradingrobotplug.com/index.html",
      "hash_links_replaced": 7,
      "site": "tradingrobotplug.com"
    },
    {
      "file": "sites/production/tradingrobotplug.com/proof/index.html",
      "hash_links_replaced": 4,
      "site": "tradingrobotplug.com"
    }
  ],
  "changed_file_count": 4,
  "changed_sites": [
    "ariajet.site",
    "houstonsipqueen.com",
    "tradingrobotplug.com"
  ]
}```

## Deploy plan
```text
DEPLOY|ariajet.site|/home/u996867598/domains/ariajet.site/public_html
DEPLOY|houstonsipqueen.com|/home/u996867598/domains/houstonsipqueen.com/public_html
DEPLOY|tradingrobotplug.com|/home/u996867598/domains/tradingrobotplug.com/public_html
```

## Live verify
```text
--- https://ariajet.site/ ---
  % Total    % Received % Xferd  Average Speed  Time    Time    Time   Current
                                 Dload  Upload  Total   Spent   Left   Speed
  0      0   0      0   0      0      0      0                              0  0  12900   0      0   0      0      0      0                              0  0  12900   0      0   0      0      0      0                              0  0  12900   0      0   0      0      0      0                              0
HTTP/2 200 
content-type: text/html
last-modified: Thu, 04 Jun 2026 23:35:58 GMT
etag: "3264-6a220bde-4aaf13d87d8e2feb;;;"
accept-ranges: bytes
content-length: 12900
date: Thu, 04 Jun 2026 23:36:59 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
--- hash scan ---
--- https://houstonsipqueen.com/ ---
  % Total    % Received % Xferd  Average Speed  Time    Time    Time   Current
                                 Dload  Upload  Total   Spent   Left   Speed
  0      0   0      0   0      0      0      0                              0  0  14825   0      0   0      0      0      0                              0  0  14825   0      0   0      0      0      0                              0  0  14825   0      0   0      0      0      0                              0
HTTP/2 200 
content-type: text/html
last-modified: Thu, 04 Jun 2026 23:35:58 GMT
etag: "39e9-6a220bde-5b1eeda3370d5c54;;;"
accept-ranges: bytes
content-length: 14825
date: Thu, 04 Jun 2026 23:37:00 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
--- hash scan ---
--- https://tradingrobotplug.com/ ---
  % Total    % Received % Xferd  Average Speed  Time    Time    Time   Current
                                 Dload  Upload  Total   Spent   Left   Speed
  0      0   0      0   0      0      0      0                              0  0  21364   0      0   0      0      0      0                              0  0  21364   0      0   0      0      0      0                              0  0  21364   0      0   0      0      0      0                              0
HTTP/2 200 
content-type: text/html
last-modified: Thu, 04 Jun 2026 23:35:58 GMT
etag: "5374-6a220bde-45e7d00df7cc8cba;;;"
accept-ranges: bytes
content-length: 21364
date: Thu, 04 Jun 2026 23:37:01 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
--- hash scan ---
```
