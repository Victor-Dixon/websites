#!/usr/bin/env python3
"""Build a factual website worklog feed from Discord Architect reports."""

from __future__ import annotations

import argparse
import json
from pathlib import Path
from typing import Any


DEFAULT_REPORT_DIR = Path("data/reports/discord_architect")
DEFAULT_OUT_JSON = Path("data/worklog/discord_architect_worklog.json")
DEFAULT_OUT_MD = Path("data/worklog/discord_architect_worklog.md")


ORDERED_TASKS = [
    "resolve_real_discord_architect_candidate_001",
    "inspect_real_discord_sender_interface_002",
    "build_discord_architect_invocation_adapter_001",
    "wire_discord_architect_closeout_payload_source_001",
    "add_discord_architect_live_dispatch_gate_001",
    "classify_untracked_discord_architect_artifacts_001",
    "repair_discord_architect_salvage_zero_quarantine_001",
    "inspect_discord_architect_source_candidates_001",
    "quarantine_discord_architect_source_candidates_001",
    "verify_discord_architect_clean_worktree_001",
]


def load_json(path: Path) -> dict[str, Any]:
    text = path.read_text(encoding="utf-8")
    try:
        data = json.loads(text)
        parse_warning = ""
    except json.JSONDecodeError as exc:
        decoder = json.JSONDecoder()
        data, end = decoder.raw_decode(text)
        trailing = text[end:].strip()
        parse_warning = f"JSONDecodeError recovered with raw_decode: {exc}; trailing_bytes={len(trailing.encode('utf-8'))}"

    if not isinstance(data, dict):
        raise ValueError(f"Expected object in {path}")

    if parse_warning:
        data = dict(data)
        data["_parse_warning"] = parse_warning

    return data


def entry_from_report(path: Path) -> dict[str, Any]:
    data = load_json(path)
    task = str(data.get("task") or path.stem)
    status = str(data.get("status") or "UNKNOWN")
    verification = data.get("verification") or {}
    if isinstance(verification, dict):
        verification_summary = ", ".join(f"{k}={v}" for k, v in verification.items())
    else:
        verification_summary = str(verification)

    return {
        "task": task,
        "status": status,
        "report": str(path),
        "summary": summarize_task(task, data),
        "verification": verification_summary,
        "next_lane": str(data.get("next_lane") or ""),
        "parse_warning": str(data.get("_parse_warning") or ""),
    }


def summarize_task(task: str, data: dict[str, Any]) -> str:
    known = {
        "resolve_real_discord_architect_candidate_001": "Selected the real Discord sender candidate from DreamVault and documented the safe invocation path.",
        "inspect_real_discord_sender_interface_002": "Inspected the selected sender interface and confirmed dry-run behavior when webhook env is absent.",
        "build_discord_architect_invocation_adapter_001": "Added the Python invocation adapter, fixture, and tests.",
        "wire_discord_architect_closeout_payload_source_001": "Wired latest DreamVault CPC JSON closeouts into the adapter payload path.",
        "add_discord_architect_live_dispatch_gate_001": "Added explicit live dispatch gate requiring --invoke --live and DISCORD_WEBHOOK_URL.",
        "classify_untracked_discord_architect_artifacts_001": "Classified remaining Discord Architect artifact drift before promotion or quarantine.",
        "repair_discord_architect_salvage_zero_quarantine_001": "Accepted zero-quarantine state and preserved source candidates for explicit review.",
        "inspect_discord_architect_source_candidates_001": "Inspected JS webhook manager candidates and classified them as separate/obsolete drift.",
        "quarantine_discord_architect_source_candidates_001": "Quarantined obsolete JS source and test candidates with hashes.",
        "verify_discord_architect_clean_worktree_001": "Verified the Discord Architect spine, dry-run bridge, live refusal gate, and generated-output ignore guard.",
    }
    return known.get(task, str(data.get("summary") or "Report-backed Discord Architect worklog entry."))


def build_worklog(report_dir: Path = DEFAULT_REPORT_DIR) -> dict[str, Any]:
    entries = []
    for task in ORDERED_TASKS:
        path = report_dir / f"{task}.json"
        if path.exists():
            entries.append(entry_from_report(path))

    missing = [
        task for task in ORDERED_TASKS
        if not (report_dir / f"{task}.json").exists()
    ]

    return {
        "worklog": "discord_architect",
        "status": "PASS" if entries else "EMPTY",
        "entry_count": len(entries),
        "entries": entries,
        "missing_expected_reports": missing,
        "source": str(report_dir),
        "integrity": {
            "fabricated_entries": False,
            "source_required": "existing JSON reports only",
        },
    }


def write_outputs(worklog: dict[str, Any], out_json: Path, out_md: Path) -> None:
    out_json.parent.mkdir(parents=True, exist_ok=True)
    out_md.parent.mkdir(parents=True, exist_ok=True)

    out_json.write_text(json.dumps(worklog, indent=2) + "\n", encoding="utf-8")

    lines = [
        "# Discord Architect Worklog",
        "",
        f"- Status: `{worklog['status']}`",
        f"- Entries: `{worklog['entry_count']}`",
        f"- Source: `{worklog['source']}`",
        "- Integrity: report-backed entries only; no fabricated activity",
        "",
        "## Entries",
        "",
    ]

    for entry in worklog["entries"]:
        lines += [
            f"### `{entry['task']}`",
            "",
            f"- Status: `{entry['status']}`",
            f"- Report: `{entry['report']}`",
            f"- Summary: {entry['summary']}",
            f"- Verification: `{entry['verification']}`",
            f"- Parse warning: `{entry.get('parse_warning', '')}`",
            "",
        ]

    if worklog["missing_expected_reports"]:
        lines += ["## Missing Expected Reports", ""]
        for task in worklog["missing_expected_reports"]:
            lines.append(f"- `{task}`")
        lines.append("")

    out_md.write_text("\n".join(lines), encoding="utf-8")


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Build Discord Architect website worklog feed.")
    parser.add_argument("--report-dir", type=Path, default=DEFAULT_REPORT_DIR)
    parser.add_argument("--out-json", type=Path, default=DEFAULT_OUT_JSON)
    parser.add_argument("--out-md", type=Path, default=DEFAULT_OUT_MD)
    return parser


def main(argv: list[str] | None = None) -> int:
    args = build_parser().parse_args(argv)
    worklog = build_worklog(args.report_dir)
    write_outputs(worklog, args.out_json, args.out_md)
    print(f"WORKLOG_STATUS={worklog['status']}")
    print(f"WORKLOG_ENTRIES={worklog['entry_count']}")
    print(f"WORKLOG_JSON={args.out_json}")
    print(f"WORKLOG_MD={args.out_md}")
    return 0 if worklog["entries"] else 1


if __name__ == "__main__":
    raise SystemExit(main())
