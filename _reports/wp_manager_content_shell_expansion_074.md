# WP Manager Content Shell Expansion 074

## Result

- Fixed manager content update shell expansion.
- Character Generator page contains shortcode.
- Public page renders generator.
- REST POST returns generator payload.

## URL

- https://dadudekc.site/character-generator/

## Raw Output

```text
7PAGE_EXISTS=PASS id=7
Success: Updated post 7.
PAGE_UPDATE=PASS id=7
PAGE_ID=7
<h1>Character Generator</h1>

<p>
Create your first Spark. This public demo is the first playable surface of The Emergence:
a deterministic fantasy-superhero system that began with “Who would win?” and expanded
into a world where the answer can include you.
</p>

[emergence_character_generator]
[emergence_character_generator]
REMOTE_CONTENT_SHORTCODE=PASS
REMOTE_CONTENT_LITERAL_CAT=PASS
SHORTCODE_RENDER=PASS
Success: Purged All!
LITESPEED_PURGE=PASS
PUBLIC_RENDER=PASS
REST_POST_SIGNAL=PASS
```

## REST Output

```json
{"scores":{"Titan":2,"Velocity":1,"Energy":1,"Specter":1,"Omni":1,"Primal":1,"Mind":1,"Duality":0},"manifested":["Titan","Velocity","Energy","Specter","Omni","Primal","Mind"],"powers":[{"domain":"Titan","power":"Elasticity","tier":1,"lead":true},{"domain":"Velocity","power":"Danger Sense","tier":1,"lead":false},{"domain":"Energy","power":"Concussive Blasts","tier":1,"lead":false},{"domain":"Specter","power":"Invisibility","tier":1,"lead":false},{"domain":"Omni","power":"Magnetism","tier":1,"lead":false},{"domain":"Primal","power":"Shapeshifting","tier":1,"lead":false},{"domain":"Mind","power":"Telekinesis","tier":1,"lead":false}],"spark_signature":81,"combat_capability":39,"threat_class":"Gamma","cast":"Wild-Cast"}```

STATUS=PASS
