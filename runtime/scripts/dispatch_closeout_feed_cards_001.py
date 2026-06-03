#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
import os
from datetime import datetime
from pathlib import Path
from typing import Any
from urllib import request


def read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8")


def post_discord(webhook_url: str, content: str) -> tuple[int, str]:
    data = json.dumps({"content": content[:1900]}).encode("utf-8")
    req = request.Request(
        webhook_url,
        data=data,
        headers={"Content-Type": "application/json"},
        method="POST",
    )
    with request.urlopen(req, timeout=20) as resp:
        return resp.status, resp.read().decode("utf-8", errors="replace")


def build_manifest(render_dir: Path, out_dir: Path, send: bool) -> dict[str, Any]:
    discord_cards = sorted(render_dir.glob("*.discord.md"))
    github_cards = sorted(render_dir.glob("*.github_architect.md"))

    manifest: dict[str, Any] = {
        "generated_at": datetime.now().isoformat(timespec="seconds"),
        "mode": "SEND" if send else "DRY_RUN",
        "status": "UNKNOWN",
        "dispatch_count": 0,
        "blocked_count": 0,
        "discord_cards": [str(p) for p in discord_cards],
        "github_architect_cards": [str(p) for p in github_cards],
        "events": [],
        "guardrail": "Dry-run by default. Live Discord dispatch requires --send and DISCORD_CLOSEOUT_WEBHOOK_URL.",
    }

    webhook = os.environ.get("DISCORD_CLOSEOUT_WEBHOOK_URL", "")

    for card_path in discord_cards:
        content = read_text(card_path)
        event: dict[str, Any] = {
            "target": "discord",
            "card": str(card_path),
            "send_requested": send,
            "sent": False,
            "status": "DRY_RUN",
        }

        if send:
            if not webhook:
                event["status"] = "BLOCKED_MISSING_DISCORD_CLOSEOUT_WEBHOOK_URL"
                manifest["blocked_count"] += 1
            else:
                status_code, body = post_discord(webhook, content)
                event["status"] = "SENT" if 200 <= status_code < 300 else f"HTTP_{status_code}"
                event["http_status"] = status_code
                event["response_preview"] = body[:300]
                event["sent"] = 200 <= status_code < 300
                manifest["dispatch_count"] += 1 if event["sent"] else 0
        else:
            preview_path = out_dir / (card_path.stem + ".dispatch_preview.md")
            preview_path.write_text(content, encoding="utf-8")
            event["preview"] = str(preview_path)

        manifest["events"].append(event)

    for card_path in github_cards:
        content = read_text(card_path)
        preview_path = out_dir / (card_path.stem + ".dispatch_preview.md")
        preview_path.write_text(content, encoding="utf-8")
        manifest["events"].append({
            "target": "github_architect",
            "card": str(card_path),
            "send_requested": False,
            "sent": False,
            "status": "PREVIEW_ONLY",
            "preview": str(preview_path),
        })

    if send and manifest["blocked_count"]:
        manifest["status"] = "BLOCKED"
    elif send:
        manifest["status"] = "DISPATCH_ATTEMPTED"
    else:
        manifest["status"] = "DRY_RUN_PASS"

    return manifest


def main() -> int:
    parser = argparse.ArgumentParser(description="Guarded dispatcher for closeout feed rendered cards.")
    parser.add_argument("--render-dir", default="data/reports/closeout_feed_rendered")
    parser.add_argument("--out-dir", default="data/reports/closeout_feed_dispatch")
    parser.add_argument("--send", action="store_true")
    args = parser.parse_args()

    render_dir = Path(args.render_dir)
    out_dir = Path(args.out_dir)
    out_dir.mkdir(parents=True, exist_ok=True)

    manifest = build_manifest(render_dir, out_dir, args.send)
    manifest_path = out_dir / "closeout_feed_dispatch_manifest_001.json"
    manifest_path.write_text(json.dumps(manifest, indent=2), encoding="utf-8")

    print(f"STATUS={manifest['status']}")
    print(f"MODE={manifest['mode']}")
    print(f"DISPATCH_COUNT={manifest['dispatch_count']}")
    print(f"BLOCKED_COUNT={manifest['blocked_count']}")
    print(f"MANIFEST={manifest_path}")

    return 0 if manifest["status"] in {"DRY_RUN_PASS", "DISPATCH_ATTEMPTED"} else 2


if __name__ == "__main__":
    raise SystemExit(main())
