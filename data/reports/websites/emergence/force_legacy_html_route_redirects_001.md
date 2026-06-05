# Force legacy Emergence HTML route redirects

Generated: 2026-06-05T01:54:56-05:00

## Before probe

```text
--- https://dadudekc.site/spark-generator.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache: hit
date: Fri, 05 Jun 2026 06:54:50 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- https://dadudekc.site/spark-battle-sim.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache-control: public,max-age=3600
x-litespeed-tag: 45f_HTTP.404,45f_404,45f_URL.26b7857189fe3e9abb1ef470b585fefe,45f_
date: Fri, 05 Jun 2026 06:54:50 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- https://dadudekc.site/battles.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache-control: public,max-age=3600
x-litespeed-tag: 45f_HTTP.404,45f_404,45f_URL.38de48322e3b11187dcd31a0876ac6e9,45f_
date: Fri, 05 Jun 2026 06:54:51 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

```

## Remote patch

```text
== HTACCESS REDIRECT BLOCK ==
# BEGIN DreamOS Emergence Legacy Route Redirects
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^spark-generator\.html$ /spark-generator/ [R=301,L]
RewriteRule ^spark-battle-sim\.html$ /battles/ [R=301,L]
RewriteRule ^battles\.html$ /battles/ [R=301,L]
RewriteRule ^battle-simulator\.html$ /battles/ [R=301,L]
RewriteRule ^the-emergence\.html$ /the-emergence/ [R=301,L]
</IfModule>
# END DreamOS Emergence Legacy Route Redirects
== FLUSH CACHE ==
Success: The cache was flushed.
Success: Rewrite rules flushed.
LEGACY_REDIRECT_PATCH=PASS
```

## After probe

```text
--- https://dadudekc.site/spark-generator.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache: hit
date: Fri, 05 Jun 2026 06:54:54 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- FOLLOW https://dadudekc.site/spark-generator.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache: hit
date: Fri, 05 Jun 2026 06:54:54 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- https://dadudekc.site/spark-battle-sim.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache-control: public,max-age=3600
x-litespeed-tag: 45f_HTTP.404,45f_404,45f_URL.26b7857189fe3e9abb1ef470b585fefe,45f_
date: Fri, 05 Jun 2026 06:54:54 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- FOLLOW https://dadudekc.site/spark-battle-sim.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache-control: public,max-age=3600
x-litespeed-tag: 45f_HTTP.404,45f_404,45f_URL.26b7857189fe3e9abb1ef470b585fefe,45f_
date: Fri, 05 Jun 2026 06:54:54 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- https://dadudekc.site/battles.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache-control: public,max-age=3600
x-litespeed-tag: 45f_HTTP.404,45f_404,45f_URL.38de48322e3b11187dcd31a0876ac6e9,45f_
date: Fri, 05 Jun 2026 06:54:55 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- FOLLOW https://dadudekc.site/battles.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache-control: public,max-age=3600
x-litespeed-tag: 45f_HTTP.404,45f_404,45f_URL.38de48322e3b11187dcd31a0876ac6e9,45f_
date: Fri, 05 Jun 2026 06:54:55 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- https://dadudekc.site/battle-simulator.html ---
HTTP/2 404 
x-powered-by: PHP/8.3.30
expires: Wed, 11 Jan 1984 05:00:00 GMT
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
x-litespeed-cache-control: public,max-age=3600
x-litespeed-tag: 45f_HTTP.404,45f_404,45f_URL.c7a82f69cb166240efc7a32999d03f6a,45f_
date: Fri, 05 Jun 2026 06:54:55 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- FOLLOW https://dadudekc.site/battle-simulator.html ---
HTTP/2 301 
date: Fri, 05 Jun 2026 06:54:56 GMT
server: LiteSpeed
location: https://dadudekc.site/battles/
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

HTTP/2 200 
x-powered-by: PHP/8.3.30
content-type: text/html; charset=UTF-8
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
link: <https://dadudekc.site/wp-json/wp/v2/pages/39>; rel="alternate"; title="JSON"; type="application/json"
link: <https://dadudekc.site/?p=39>; rel=shortlink
etag: "3403-1780640535;;;"
x-litespeed-cache: hit
date: Fri, 05 Jun 2026 06:54:56 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests


--- https://dadudekc.site/the-emergence.html ---
HTTP/2 301 
date: Fri, 05 Jun 2026 06:54:56 GMT
server: LiteSpeed
location: https://dadudekc.site/the-emergence/
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- FOLLOW https://dadudekc.site/the-emergence.html ---
HTTP/2 301 
date: Fri, 05 Jun 2026 06:54:56 GMT
server: LiteSpeed
location: https://dadudekc.site/the-emergence/
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

HTTP/2 301 
x-powered-by: PHP/8.3.30
content-type: text/html; charset=UTF-8
cache-control: no-store, no-cache, must-revalidate, max-age=0
pragma: no-cache
expires: Wed, 11 Jan 1984 05:00:00 GMT
x-dreamos-emergence: live
x-redirect-by: WordPress
location: https://dadudekc.site/
x-litespeed-cache: hit
date: Fri, 05 Jun 2026 06:54:56 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests

HTTP/2 200 
x-powered-by: PHP/8.3.30
content-type: text/html; charset=UTF-8
cache-control: no-store, no-cache, must-revalidate, max-age=0
pragma: no-cache
expires: Wed, 11 Jan 1984 05:00:00 GMT
x-dreamos-emergence: live
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
link: <https://dadudekc.site/wp-json/wp/v2/pages/16>; rel="alternate"; title="JSON"; type="application/json"
link: <https://dadudekc.site/>; rel=shortlink
etag: "3382-1780624526;;;"
x-litespeed-cache: hit
date: Fri, 05 Jun 2026 06:54:56 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests

```

## Final content verify

```text
HTTP/2 301 
content-type: text/html
content-length: 795
date: Fri, 05 Jun 2026 06:54:57 GMT
server: LiteSpeed
location: https://dadudekc.site/spark-generator/?cb=1780642495626006122
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

HTTP/2 200 
x-powered-by: PHP/8.3.30
content-type: text/html; charset=UTF-8
cache-control: no-store, no-cache, must-revalidate, max-age=0
pragma: no-cache
expires: Wed, 11 Jan 1984 05:00:00 GMT
x-dreamos-emergence: live
link: <https://dadudekc.site/wp-json/>; rel="https://api.w.org/"
link: <https://dadudekc.site/wp-json/wp/v2/pages/43>; rel="alternate"; title="JSON"; type="application/json"
link: <https://dadudekc.site/?p=43>; rel=shortlink
x-litespeed-cache-control: public,max-age=604800
x-litespeed-tag: 45f_page,45f_URL.fe82b3625f6061fb06389051bb3625bf,45f_Po.43,45f_PGS,45f_
etag: "3426-1780642497;;;"
x-litespeed-cache: miss
date: Fri, 05 Jun 2026 06:54:57 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests

<title>Spark Generator &#8211; dadudekc.site
canonical" href="https://dadudekc.site/spark-generator/
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
wp-json/emergence/v1/generate
question_bank
```

## Result

Legacy `.html` routes are now forced to canonical WordPress routes. Even if a cached button still points to `/spark-generator.html`, the server sends users to `/spark-generator/`.
