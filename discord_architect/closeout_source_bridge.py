#!/usr/bin/env python3
"""Bridge latest DreamVault CPC closeout JSON into Discord Architect adapter."""

from __future__ import annotations

import argparse
import json
import os
import subprocess
import sys
from pathlib import Path
from typing import Any


DEFAULT_CPC_JSON_DIR = Path.home() / "projects/DreamVault/data/reports/cpc/json"
DEFAULT_ADAPTER = Path("discord_architect/invocation_adapter.py")
DEFAULT_SELECTED_SENDER = Path.home() / "projects/DreamVault/runtime/scripts/send_discord_paper_trade_payload.py"
DEFAULT_OUTPUT_PAYLOAD = Path("runtime/trading/discord/latest_paper_trade_payload.json")


def find_latest_cpc_json(cpc_json_dir: Path = DEFAULT_CPC_JSON_DIR) -> Path:
    if not cpc_json_dir.exists():
        raise FileNotFoundError(f"CPC JSON directory not found: {cpc_json_dir}")

    candidates = sorted(
        [path for path in cpc_json_dir.glob("*.json") if path.is_file()],
        key=lambda path: (path.stat().st_mtime_ns, path.name),
        reverse=True,
    )
    if not candidates:
        raise FileNotFoundError(f"No CPC JSON files found in {cpc_json_dir}")
    return candidates[0]


def load_json(path: Path) -> dict[str, Any]:
    data = json.loads(path.read_text(encoding="utf-8"))
    if not isinstance(data, dict):
        raise ValueError(f"Expected JSON object at {path}")
    return data


def canonicalize_cpc_payload(raw: dict[str, Any], source_path: Path) -> dict[str, Any]:
    closeout = raw.get("closeout") if isinstance(raw.get("closeout"), dict) else {}
    proof = raw.get("proof") if isinstance(raw.get("proof"), dict) else {}

    task = (
        raw.get("task")
        or closeout.get("Task")
        or closeout.get("task")
        or raw.get("NEXT_TASK")
        or source_path.stem
    )
    status = (
        raw.get("status")
        or closeout.get("Status")
        or closeout.get("status")
        or proof.get("CPC_STATUS")
        or raw.get("CPC_STATUS")
        or "UNKNOWN"
    )

    payload = {
        "task": str(task),
        "status": str(status),
        "lane": str(raw.get("lane") or raw.get("NEXT_LANE") or ""),
        "actions_taken": str(closeout.get("Actions Taken") or closeout.get("actions_taken") or raw.get("summary") or ""),
        "verification": str(closeout.get("Verification") or closeout.get("verification") or ""),
        "commit_message": str(closeout.get("Commit Message") or closeout.get("commit_message") or ""),
        "report": str(raw.get("REPORT") or raw.get("report") or ""),
        "json": str(source_path),
        "next_lane": str(raw.get("NEXT_LANE") or closeout.get("Next lane") or ""),
        "next_task": str(raw.get("NEXT_TASK") or closeout.get("Next task") or ""),
    }

    return payload


def write_bridge_payload(payload: dict[str, Any], path: Path) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(payload, indent=2) + "\n", encoding="utf-8")


def run_adapter(adapter: Path, source_payload: Path, output_payload: Path, sender: Path, invoke: bool) -> subprocess.CompletedProcess[str]:
    cmd = [
        sys.executable,
        str(adapter),
        "--input",
        str(source_payload),
        "--output",
        str(output_payload),
        "--sender",
        str(sender),
    ]
    if invoke:
        cmd.append("--invoke")

    return subprocess.run(
        cmd,
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        check=False,
    )


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Wire latest CPC closeout JSON into Discord Architect adapter.")
    parser.add_argument("--cpc-json-dir", type=Path, default=DEFAULT_CPC_JSON_DIR)
    parser.add_argument("--cpc-json", type=Path, default=None)
    parser.add_argument("--adapter", type=Path, default=DEFAULT_ADAPTER)
    parser.add_argument("--sender", type=Path, default=DEFAULT_SELECTED_SENDER)
    parser.add_argument("--output", type=Path, default=DEFAULT_OUTPUT_PAYLOAD)
    parser.add_argument("--bridge-payload", type=Path, default=Path("runtime/trading/discord/latest_cpc_closeout_bridge_payload.json"))
    parser.add_argument("--invoke", action="store_true", help="Invoke selected sender after writing payload")
    parser.add_argument(
        "--live",
        action="store_true",
        help="Allow live dispatch. Requires --invoke and DISCORD_WEBHOOK_URL.",
    )
    return parser


def main(argv: list[str] | None = None) -> int:
    args = build_parser().parse_args(argv)

    if args.live:
        if not args.invoke:
            print("LIVE_GATE=FAIL reason=--live requires --invoke", file=sys.stderr)
            return 3
        if not os.environ.get("DISCORD_WEBHOOK_URL", "").strip():
            print("LIVE_GATE=FAIL reason=DISCORD_WEBHOOK_URL not set", file=sys.stderr)
            return 4
        print("LIVE_GATE=PASS")
    else:
        print("LIVE_GATE=DRY_RUN")

    source = args.cpc_json or find_latest_cpc_json(args.cpc_json_dir)
    raw = load_json(source)
    bridge_payload = canonicalize_cpc_payload(raw, source)
    write_bridge_payload(bridge_payload, args.bridge_payload)

    print(f"CPC_SOURCE={source}")
    print(f"BRIDGE_PAYLOAD=PASS path={args.bridge_payload}")

    result = run_adapter(args.adapter, args.bridge_payload, args.output, args.sender, args.invoke)
    if result.stdout:
        print(result.stdout, end="")
    if result.stderr:
        print(result.stderr, end="", file=sys.stderr)
    if result.returncode != 0:
        print(f"BRIDGE_ADAPTER=FAIL code={result.returncode}", file=sys.stderr)
        return result.returncode

    print("BRIDGE_ADAPTER=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
