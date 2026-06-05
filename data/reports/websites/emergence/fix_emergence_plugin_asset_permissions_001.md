# Fix Emergence plugin asset permissions

Generated: 2026-06-05T02:09:15-05:00

## Remote chmod

```text
PWD=/home/u996867598
HOME=/home/u996867598
PLUGIN=/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator
== AFTER PERMS ==
drwxr-xr-x 3 u996867598 o1008028115 4096 May 31 12:31 /home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator
drwxr-xr-x 2 u996867598 o1008028115 4096 May 31 17:56 /home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets
total 236
drwxr-xr-x 2 u996867598 o1008028115  4096 May 31 17:56 .
drwxr-xr-x 3 u996867598 o1008028115  4096 May 31 12:31 ..
-rw-r--r-- 1 u996867598 o1008028115 16417 Jun  5 07:08 emergence-cg.css
-rw-r--r-- 1 u996867598 o1008028115 55697 Jun  5 07:08 emergence-cg.js
-rw-r--r-- 1 u996867598 o1008028115 16417 Jun  5 07:09 emergence-character-generator.css
-rw-r--r-- 1 u996867598 o1008028115 55697 Jun  5 07:08 emergence-character-generator.js
-rw-r--r-- 1 u996867598 o1008028115 40426 May 31 14:29 protocol-v85-question-bank.json
-rw-r--r-- 1 u996867598 o1008028115 12150 May 31 12:42 spark-protocol-v85-domain-key.json
== REMOTE GUARD VERIFY ==
/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js:827:/* DreamOS Spark Generator Fail-Open Guard
/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets/emergence-character-generator.js:827:/* DreamOS Spark Generator Fail-Open Guard
/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js:918:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',
/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets/emergence-character-generator.js:918:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',
/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css:477:/* DreamOS Spark Generator Fail-Open Visibility Guard */
/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets/emergence-character-generator.css:477:/* DreamOS Spark Generator Fail-Open Visibility Guard */
CACHE_PURGE_BASE=/home/u996867598/domains/dadudekc.site/public_html
CACHE_PURGE_BASE=/home/u996867598/public_html
```

## Live asset verification

```text
--- https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js?cb=1780643352275682712 ---
HEADERS:
HTTP/2 200 
cache-control: public, max-age=604800
expires: Fri, 12 Jun 2026 07:09:13 GMT
content-type: application/x-javascript
last-modified: Fri, 05 Jun 2026 07:08:56 GMT
etag: "d991-6a227608-6db6938775880076;;;"
accept-ranges: bytes
content-length: 55697
date: Fri, 05 Jun 2026 07:09:13 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

SIZE:
55697 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_emergence_plugin_asset_permissions_001/live-emergence-cg.js
SIGNALS:
827:/* DreamOS Spark Generator Fail-Open Guard
918:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',

--- https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-character-generator.js?cb=1780643352856246096 ---
HEADERS:
HTTP/2 200 
cache-control: public, max-age=604800
expires: Fri, 12 Jun 2026 07:09:14 GMT
content-type: application/x-javascript
last-modified: Fri, 05 Jun 2026 07:08:57 GMT
etag: "d991-6a227609-5d795a42b3d3c94f;;;"
accept-ranges: bytes
content-length: 55697
date: Fri, 05 Jun 2026 07:09:14 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

SIZE:
55697 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_emergence_plugin_asset_permissions_001/live-emergence-character-generator.js
SIGNALS:
827:/* DreamOS Spark Generator Fail-Open Guard
918:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',

--- https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css?cb=1780643353449460866 ---
HEADERS:
HTTP/2 200 
cache-control: public, max-age=604800
expires: Fri, 12 Jun 2026 07:09:15 GMT
content-type: text/css
last-modified: Fri, 05 Jun 2026 07:08:58 GMT
etag: "4021-6a22760a-3cfa46704ac3f90d;;;"
accept-ranges: bytes
content-length: 16417
date: Fri, 05 Jun 2026 07:09:15 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

SIZE:
16417 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_emergence_plugin_asset_permissions_001/live-emergence-cg.css
SIGNALS:
477:/* DreamOS Spark Generator Fail-Open Visibility Guard */

--- https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-character-generator.css?cb=1780643354101137866 ---
HEADERS:
HTTP/2 200 
cache-control: public, max-age=604800
expires: Fri, 12 Jun 2026 07:09:15 GMT
content-type: text/css
last-modified: Fri, 05 Jun 2026 07:09:00 GMT
etag: "4021-6a22760c-2e5bd6bb4e00ebbf;;;"
accept-ranges: bytes
content-length: 16417
date: Fri, 05 Jun 2026 07:09:15 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

SIZE:
16417 /data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/tmp/fix_emergence_plugin_asset_permissions_001/live-emergence-character-generator.css
SIGNALS:
477:/* DreamOS Spark Generator Fail-Open Visibility Guard */
```

## Generator page verification

```text
emergence-cg.css?ver=0.7.3
emergence-character-generator.css?ver=0.7.3
Character Record
character record
EmergenceCG
EmergenceCG
EmergenceCG
Character Record
EmergenceCG
emergence-cg.js?ver=0.7.3
emergence-character-generator.js?ver=0.7.3
```

## Result

Plugin asset files were present but not web-readable. Directory/file permissions were repaired so static JS/CSS can be served.
