#!/usr/bin/env python3
"""Discord Architect invocation adapter.

Normalizes a DreamOS/CPC closeout payload into the payload file expected by
DreamVault's canonical Discord paper-trade sender.

Default behavior is dry-run safe: if DISCORD_WEBHOOK_URL is absent, the selected
sender prints DISCORD_SEND=DRY_RUN and does not dispatch.
"""

from __future__ import annotations

import argparse
import json
import os
import subprocess
import sys
from pathlib import Path
from typing import Any


DEFAULT_SELECTED_SENDER = Path.home() / "projects/DreamVault/runtime/scripts/send_discord_paper_trade_payload.py"
DEFAULT_OUTPUT_PAYLOAD = Path("runtime/trading/discord/latest_paper_trade_payload.json")


def load_json(path: Path) -> dict[str, Any]:
    with path.open("r", encoding="utf-8") as handle:
        data = json.load(handle)
    if not isinstance(data, dict):
        raise ValueError(f"Expected JSON object at {path}")
    return data


def normalize_closeout_payload(raw: dict[str, Any]) -> dict[str, Any]:
    task = str(raw.get("task") or raw.get("Task") or "unknown_task")
    status = str(raw.get("status") or raw.get("Status") or "UNKNOWN")
    lane = str(raw.get("lane") or raw.get("NEXT_LANE") or raw.get("next_lane") or "")

    actions = raw.get("actions_taken") or raw.get("Actions Taken") or raw.get("actions") or ""
    verification = raw.get("verification") or raw.get("Verification") or ""
    commit = raw.get("commit_message") or raw.get("Commit Message") or ""
    report = raw.get("report") or raw.get("REPORT") or ""
    json_report = raw.get("json") or raw.get("JSON") or ""
    next_lane = raw.get("next_lane") or raw.get("NEXT_LANE") or ""
    next_task = raw.get("next_task") or raw.get("NEXT_TASK") or ""

    title = f"DreamOS Closeout: {task} [{status}]"

    fields = [
        {"name": "Task", "value": task, "inline": False},
        {"name": "Status", "value": status, "inline": True},
    ]

    if lane:
        fields.append({"name": "Lane", "value": str(lane), "inline": True})
    if actions:
        fields.append({"name": "Actions Taken", "value": str(actions), "inline": False})
    if verification:
        fields.append({"name": "Verification", "value": str(verification), "inline": False})
    if commit:
        fields.append({"name": "Commit", "value": str(commit), "inline": False})
    if report:
        fields.append({"name": "Report", "value": str(report), "inline": False})
    if json_report:
        fields.append({"name": "JSON", "value": str(json_report), "inline": False})
    if next_lane:
        fields.append({"name": "Next Lane", "value": str(next_lane), "inline": True})
    if next_task:
        fields.append({"name": "Next Task", "value": str(next_task), "inline": False})

    content = f"{status}: {task}"

    return {
        "content": content,
        "embeds": [
            {
                "title": title,
                "description": "CPC closeout normalized by Discord Architect invocation adapter.",
                "fields": fields,
            }
        ],
        "dreamos_meta": {
            "source": "discord_architect.invocation_adapter",
            "task": task,
            "status": status,
            "next_lane": str(next_lane),
            "next_task": str(next_task),
        },
    }


def write_payload(payload: dict[str, Any], output_path: Path) -> None:
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(payload, indent=2) + "\n", encoding="utf-8")


def invoke_selected_sender(selected_sender: Path, cwd: Path) -> subprocess.CompletedProcess[str]:
    env = os.environ.copy()
    # Deliberately do not set DISCORD_WEBHOOK_URL. The canonical sender dry-runs
    # when the env var is absent.
    env.pop("DISCORD_WEBHOOK_URL", None)
    return subprocess.run(
        [sys.executable, str(selected_sender)],
        cwd=str(cwd),
        env=env,
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        check=False,
    )


def build_arg_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Normalize and dry-run Discord Architect payload dispatch.")
    parser.add_argument("--input", required=True, type=Path, help="Input CPC/closeout JSON payload")
    parser.add_argument("--output", type=Path, default=DEFAULT_OUTPUT_PAYLOAD, help="Sender payload path")
    parser.add_argument("--sender", type=Path, default=DEFAULT_SELECTED_SENDER, help="Selected canonical sender script")
    parser.add_argument("--invoke", action="store_true", help="Invoke selected sender after writing payload")
    return parser


def main(argv: list[str] | None = None) -> int:
    args = build_arg_parser().parse_args(argv)
    raw = load_json(args.input)
    payload = normalize_closeout_payload(raw)
    write_payload(payload, args.output)

    print(f"ADAPTER_WRITE=PASS output={args.output}")

    if args.invoke:
        if not args.sender.exists():
            print(f"ADAPTER_INVOKE=FAIL missing_sender={args.sender}", file=sys.stderr)
            return 2
        result = invoke_selected_sender(args.sender, Path.cwd())
        if result.stdout:
            print(result.stdout, end="")
        if result.stderr:
            print(result.stderr, end="", file=sys.stderr)
        if result.returncode != 0:
            print(f"ADAPTER_INVOKE=FAIL code={result.returncode}", file=sys.stderr)
            return result.returncode
        print("ADAPTER_INVOKE=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
