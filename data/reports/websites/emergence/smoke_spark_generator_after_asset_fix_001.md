# Smoke Spark generator after asset permission fix

Generated: 2026-06-05T01:42:16-05:00

## Classification

```text
EMERGENCECG_PRESENT=YES
QUESTION_BANK_PRESENT=YES
JS_REFERENCE_PRESENT=YES
CSS_REFERENCE_PRESENT=YES
GENERATE_POST_200=YES
SPARK_RESULT_PRESENT=YES
```

## Page signals

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
wp-json/emergence/v1/generate
question_bank
emergence-cg.js?ver=0.7.3
emergence-character-generator.js?ver=0.7.3
```

## Backend POST smoke

```text
HTTP/2 200 
x-powered-by: PHP/8.3.30
content-type: application/json; charset=UTF-8
x-robots-tag: noindex
link: <https://maskzero.site/wp-json/>; rel="https://api.w.org/"
x-content-type-options: nosniff
access-control-expose-headers: X-WP-Total, X-WP-TotalPages, Link
access-control-allow-headers: Authorization, X-WP-Nonce, Content-Disposition, Content-MD5, Content-Type
expires: Wed, 11 Jan 1984 05:00:00 GMT
pragma: no-cache
allow: POST
x-litespeed-cache-control: no-cache
cache-control: no-cache, must-revalidate, max-age=0, no-store, private
date: Fri, 05 Jun 2026 06:42:17 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

{"protocol_version":"Spark Protocol v8.5 two-pass generation","answers_expected":28,"phase":"domain_typing","scores":{"Titan":8,"Velocity":2,"Energy":5,"Specter":1,"Duality":4,"Omni":4,"Primal":4,"Mind":3},"tiers":{"Titan":2,"Velocity":1,"Energy":1,"Specter":1,"Duality":1,"Omni":1,"Primal":1,"Mind":1},"manifest_threshold":2,"manifested":["Titan","Energy","Duality","Omni","Primal","Mind","Velocity"],"lead_domain":"Titan","profile_shape":"Wide multi-domain Spark: broad manifestation, lower specialization pressure.","provisional_spark_signature":76,"provisional_combat_capability":28,"power_selection_status":"locked_until_flavor_pass","powers":[],"next_phase":{"name":"flavor_power_selection","questions":"Q29-Q68","description":"Flavor questions select actual sub-affinities\/powers inside manifested domains."},"cast":"Wild-Cast"}
```

## Result

Assets are readable and backend generation is healthy. If the page still appears blank in browser, remaining issue is browser-side runtime JS/CSS execution, not missing assets or backend failure.
