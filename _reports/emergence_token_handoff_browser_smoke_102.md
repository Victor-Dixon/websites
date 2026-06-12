# Emergence Token Handoff Browser Smoke 102

## Task
Automate token create → /battles/?spark_token=... → imported Spark → custom battle.

## Actions
- Added browser-lite token handoff smoke.
- Created signed Spark token through REST.
- Loaded valid token from REST.
- Verified invalid token rejection.
- Opened Battle page with token.
- Verified token import bridge semantics.
- Ran custom Spark battle using token payload.
- Verified winner, arena, story, and no raw score leaks.

## Verification
```text
== SAFE PAYLOAD ASSERT ==
SAFE_SPARK_PAYLOAD=PASS
== CREATE TOKEN ==
HTTP_JSON=200 method=POST url=https://maskzero.site/wp-json/emergence/v1/spark-token?dreamos_smoke=102
TOKEN_CREATE=PASS
TOKEN_LENGTH=32
== LOAD VALID TOKEN ==
HTTP_JSON=200 method=GET url=https://maskzero.site/wp-json/emergence/v1/spark-token/C9eAtGLuPXEm28AdLiPJJLCNiXYC_MZA?dreamos_smoke=102
TOKEN_LOAD_VALID=PASS
TOKEN_NO_RAW_SCORE_LEAK=PASS
== INVALID TOKEN REJECT ==
HTTP_JSON=404 method=GET url=https://maskzero.site/wp-json/emergence/v1/spark-token/invalid-token-000000?dreamos_smoke=102
TOKEN_INVALID_REJECTED=PASS
== OPEN BATTLE PAGE WITH TOKEN ==
HTTP_FETCH=200 url=https://maskzero.site/battles/?dreamos_smoke=102&spark_token=C9eAtGLuPXEm28AdLiPJJLCNiXYC_MZA
BATTLE_TOKEN_PAGE_LOADS=PASS
BATTLE_TOKEN_IMPORT_SEMANTICS=PASS
== RUN CUSTOM BATTLE USING TOKEN PAYLOAD ==
HTTP_JSON=200 method=POST url=https://maskzero.site/wp-json/spark-battle/v1/custom-battle?dreamos_smoke=102
TOKEN_CUSTOM_BATTLE_REST=PASS
TOKEN_CUSTOM_BATTLE_WINNER_VISIBLE=PASS
TOKEN_CUSTOM_BATTLE_ARENA_VISIBLE=PASS
TOKEN_CUSTOM_BATTLE_STORY_VISIBLE=PASS
TOKEN_CUSTOM_BATTLE_NO_RAW_SCORE_LEAK=PASS
== FINAL ASSERT ==
VALID_TOKEN_FRESH_SESSION_ASSERT=PASS
INVALID_TOKEN_REJECT_ASSERT=PASS
WINNER_ARENA_STORY_VISIBLE_ASSERT=PASS
EMERGENCE_TOKEN_HANDOFF_BROWSER_SMOKE=PASS
```

## Commit
Add Emergence token handoff browser smoke

## Status
PASS
