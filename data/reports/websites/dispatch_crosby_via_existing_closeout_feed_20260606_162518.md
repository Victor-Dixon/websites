# Dispatch Crosby via Existing Closeout Feed

generated=2026-06-06T16:25:18-05:00
root=/data/data/com.termux/files/home/projects/websites
feed=/data/data/com.termux/files/home/projects/websites/runtime/feeds/closeouts/crosby_weareswarm_proof_20260606_162518.json

== PRECHECK ==
RENDERER=PASS
DISPATCHER=PASS
WEBHOOK_ENV=PASS:/data/data/com.termux/files/home/projects/websites/.cache/secure_runtime/discord_closeout_webhook.env
== WRITE FEED JSON ==
FEED_JSON=PASS:/data/data/com.termux/files/home/projects/websites/runtime/feeds/closeouts/crosby_weareswarm_proof_20260606_162518.json
== RENDER CLOSEOUT CARDS ==
STATUS=DRY_RUN_RENDERED
FEED_COUNT=2
RENDERED_COUNT=2
MANIFEST=/data/data/com.termux/files/home/projects/websites/data/reports/closeout_feed_rendered/closeout_feed_render_manifest_001.json
== LOAD WEBHOOK ENV WITHOUT PRINTING SECRET ==
DISCORD_CLOSEOUT_WEBHOOK_URL=FOUND
== DISPATCH CLOSEOUT CARDS ==
Traceback (most recent call last):
  File "/data/data/com.termux/files/home/projects/websites/runtime/scripts/dispatch_closeout_feed_cards_001.py", line 129, in <module>
    raise SystemExit(main())
                     ~~~~^^
  File "/data/data/com.termux/files/home/projects/websites/runtime/scripts/dispatch_closeout_feed_cards_001.py", line 115, in main
    manifest = build_manifest(render_dir, out_dir, args.send)
  File "/data/data/com.termux/files/home/projects/websites/runtime/scripts/dispatch_closeout_feed_cards_001.py", line 68, in build_manifest
    status_code, body = post_discord(webhook, content)
                        ~~~~~~~~~~~~^^^^^^^^^^^^^^^^^^
  File "/data/data/com.termux/files/home/projects/websites/runtime/scripts/dispatch_closeout_feed_cards_001.py", line 19, in post_discord
    req = request.Request(
        webhook_url,
    ...<2 lines>...
        method="POST",
    )
  File "/data/data/com.termux/files/usr/lib/python3.13/urllib/request.py", line 292, in __init__
    self.full_url = url
    ^^^^^^^^^^^^^
  File "/data/data/com.termux/files/usr/lib/python3.13/urllib/request.py", line 318, in full_url
    self._parse()
    ~~~~~~~~~~~^^
  File "/data/data/com.termux/files/usr/lib/python3.13/urllib/request.py", line 347, in _parse
    raise ValueError("unknown url type: %r" % self.full_url)
ValueError: unknown url type: 'PASTE_FRESH_WEBHOOK_HERE'
