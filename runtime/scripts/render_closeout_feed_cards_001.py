#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
from pathlib import Path
from typing import Any


def load_json(path: Path) -> dict[str, Any]:
    return json.loads(path.read_text(encoding="utf-8"))


def render_discord_markdown(feed: dict[str, Any]) -> str:
    card = feed.get("discord_card", {})
    verification = feed.get("verification", {})
    links = card.get("links", [])

    lines = [
        f"# {card.get('title', feed.get('project', 'Closeout Feed Card'))}",
        "",
        f"**Status:** `{card.get('status', feed.get('status', 'UNKNOWN'))}`",
        "",
        card.get("body", ""),
        "",
        "## Links",
        "",
    ]

    for link in links:
        lines.append(f"- {link}")

    lines.extend([
        "",
        "## Verification",
        "",
    ])

    for key, value in verification.items():
        lines.append(f"- `{key}`: `{value}`")

    lines.extend([
        "",
        "## Source Artifacts",
        "",
    ])

    for artifact in feed.get("source_artifacts", []):
        lines.append(f"- `{artifact}`")

    return "\n".join(lines).strip() + "\n"


def render_github_architect_markdown(feed: dict[str, Any]) -> str:
    note = feed.get("github_architect_note", {})
    lines = [
        f"# GitHub Architect Closeout: {feed.get('project', feed.get('id', 'unknown'))}",
        "",
        f"- Status: `{feed.get('status', 'UNKNOWN')}`",
        f"- Repo: `{feed.get('repo', 'unknown')}`",
        f"- Head: `{feed.get('head', 'unknown')}`",
        f"- Root: `{feed.get('live_root', '')}`",
        f"- Route: `{feed.get('live_route', '')}`",
        "",
        "## Summary",
        "",
        note.get("summary", ""),
        "",
        "## Capabilities Unlocked",
        "",
    ]

    for item in feed.get("capabilities_unlocked", []):
        lines.append(f"- {item}")

    lines.extend([
        "",
        "## Verification",
        "",
    ])

    for key, value in feed.get("verification", {}).items():
        lines.append(f"- `{key}`: `{value}`")

    lines.extend([
        "",
        "## Next Lane",
        "",
        note.get("next_lane", "none"),
        "",
    ])

    return "\n".join(lines).strip() + "\n"


def main() -> int:
    parser = argparse.ArgumentParser(description="Dry-run render closeout feed cards.")
    parser.add_argument("--feed-dir", default="runtime/feeds/closeouts")
    parser.add_argument("--out-dir", default="data/reports/closeout_feed_rendered")
    args = parser.parse_args()

    feed_dir = Path(args.feed_dir)
    out_dir = Path(args.out_dir)
    out_dir.mkdir(parents=True, exist_ok=True)

    feeds = sorted(feed_dir.glob("*.json"))
    rendered = []

    for feed_path in feeds:
        feed = load_json(feed_path)
        feed_id = feed.get("id") or feed_path.stem

        discord_path = out_dir / f"{feed_id}.discord.md"
        github_path = out_dir / f"{feed_id}.github_architect.md"

        discord_path.write_text(render_discord_markdown(feed), encoding="utf-8")
        github_path.write_text(render_github_architect_markdown(feed), encoding="utf-8")

        rendered.append({
            "feed": str(feed_path),
            "feed_id": feed_id,
            "discord": str(discord_path),
            "github_architect": str(github_path),
            "status": feed.get("status", "UNKNOWN"),
        })

    manifest = {
        "status": "DRY_RUN_RENDERED",
        "feed_count": len(feeds),
        "rendered_count": len(rendered),
        "rendered": rendered,
        "guardrail": "Dry-run only. No Discord/GitHub dispatch performed.",
    }

    manifest_path = out_dir / "closeout_feed_render_manifest_001.json"
    manifest_path.write_text(json.dumps(manifest, indent=2), encoding="utf-8")

    print(f"STATUS={manifest['status']}")
    print(f"FEED_COUNT={manifest['feed_count']}")
    print(f"RENDERED_COUNT={manifest['rendered_count']}")
    print(f"MANIFEST={manifest_path}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
