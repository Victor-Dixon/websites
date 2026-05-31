# Spark Protocol Character Generation Port 076

## Result

- Replaced Spark-lite domain scoring with Spark Protocol v8.5 Q1-Q28 domain table.
- Implemented score_to_tier.
- Implemented 25% manifested-domain gate.
- Verified all-H Duality and all-G Mind fixtures against expected uploaded-engine behavior.

## Public Demo

- https://dadudekc.site/character-generator/

## Raw Output

```text
Success: Plugin already activated.
PLUGIN_ACTIVE=PASS
7PAGE_EXISTS=PASS id=7
Success: Updated post 7.
PAGE_UPDATE=PASS id=7
Success: Purged All!
LITESPEED_PURGE=PASS
PUBLIC_RENDER=PASS
```

## all-H REST Fixture
```json
{"protocol_version":"Spark Protocol v8.5 domain generation","answers_expected":28,"scores":{"Titan":0,"Velocity":0,"Energy":0,"Specter":0,"Duality":31,"Omni":0,"Primal":0,"Mind":0},"tiers":{"Titan":1,"Velocity":1,"Energy":1,"Specter":1,"Duality":5,"Omni":1,"Primal":1,"Mind":1},"manifest_threshold":7.75,"manifested":["Duality"],"powers":[{"domain":"Duality","power":"Laser Light","tier":5,"lead":true},{"domain":"Duality","power":"Hard Light","tier":4,"lead":false}],"spark_signature":85,"combat_capability":56,"threat_class":"Delta","cast":"Solo Spark"}```

## all-G REST Fixture
```json
{"protocol_version":"Spark Protocol v8.5 domain generation","answers_expected":28,"scores":{"Titan":0,"Velocity":0,"Energy":0,"Specter":0,"Duality":0,"Omni":0,"Primal":0,"Mind":31},"tiers":{"Titan":1,"Velocity":1,"Energy":1,"Specter":1,"Duality":1,"Omni":1,"Primal":1,"Mind":5},"manifest_threshold":7.75,"manifested":["Mind"],"powers":[{"domain":"Mind","power":"Psychic Defense","tier":5,"lead":true}],"spark_signature":84,"combat_capability":53,"threat_class":"Delta","cast":"Solo Spark"}```

STATUS=PASS
