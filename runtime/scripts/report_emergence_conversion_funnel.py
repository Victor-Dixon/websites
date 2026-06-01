#!/usr/bin/env python3
from __future__ import annotations

import json
import sys
import urllib.request
from datetime import datetime, timezone
from pathlib import Path
from typing import Any

ROOT = Path(__file__).resolve().parents[2]
JSON_OUT = ROOT / "_reports" / "emergence_conversion_funnel_report_114.json"
MD_OUT = ROOT / "_reports" / "emergence_conversion_funnel_report_114.md"

SUMMARY_URL = "https://dadudekc.site/wp-json/emergence/v1/events/summary?dreamos_smoke=114"

FORBIDDEN = [
    "scores",
    "tiers",
    "manifest_threshold",
    "flavor_vectors",
    "spark_signature",
    "combat_capability",
    "answers",
    "domain_key",
    "debug",
    "showwork",
    "raw_roll",
    "odds:",
    "api_key",
    "token_secret",
]

FUNNEL = [
    ("character_started", "Character Started"),
    ("scan_completed", "Scan Completed"),
    ("premium_prompt_copied", "Prompt Copied"),
    ("character_saved", "Character Saved"),
    ("battle_started", "Battle Started"),
]


def require(condition: bool, message: str) -> None:
    if not condition:
        raise AssertionError(message)


def fetch_summary() -> dict[str, Any]:
    req = urllib.request.Request(
        SUMMARY_URL,
        headers={"User-Agent": "DreamOS-FunnelReport/1.0", "Cache-Control": "no-cache"},
    )
    with urllib.request.urlopen(req, timeout=35) as resp:
        body = json.loads(resp.read().decode("utf-8"))
        print(f"HTTP_SUMMARY={resp.status}")
        require(resp.status == 200, f"summary HTTP {resp.status}")
        return body


def assert_no_leaks(label: str, payload: Any) -> None:
    serialized = json.dumps(payload, sort_keys=True).lower()
    leaks = [item for item in FORBIDDEN if item.lower() in serialized]
    require(not leaks, f"{label} leaked forbidden markers: {leaks}")


def count(summary: dict[str, Any], key: str) -> int:
    try:
        return max(0, int(summary.get(key, 0)))
    except Exception:
        return 0


def pct(numerator: int, denominator: int) -> float:
    if denominator <= 0:
        return 0.0
    return round((numerator / denominator) * 100.0, 1)


def classify_step(step_rate: float, total_started: int, step_key: str) -> str:
    if total_started <= 0:
        return "no_traffic_yet"

    if step_key == "character_started":
        return "entry"

    if step_rate < 10:
        return "critical_dropoff"
    if step_rate < 30:
        return "weak_step"
    if step_rate < 60:
        return "watch"
    return "healthy"


def main() -> int:
    print("== FETCH EVENT SUMMARY ==")
    body = fetch_summary()
    require(body.get("status") == "ok", f"bad summary status: {body}")
    require(body.get("player_safe") is True, "summary not marked player_safe")

    summary = body.get("summary", {})
    require(isinstance(summary, dict), "summary missing dict")

    assert_no_leaks("event summary", body)

    started = count(summary, "character_started")
    rows = []

    print("== CALCULATE FUNNEL ==")
    previous_count = None
    for key, label in FUNNEL:
        value = count(summary, key)
        from_start = pct(value, started)

        if previous_count is None:
            from_previous = 100.0 if value > 0 else 0.0
            dropoff_from_previous = 0.0
        else:
            from_previous = pct(value, previous_count)
            dropoff_from_previous = round(100.0 - from_previous, 1) if previous_count > 0 else 0.0

        status = classify_step(from_start, started, key)
        row = {
            "key": key,
            "label": label,
            "count": value,
            "rate_from_started_pct": from_start,
            "rate_from_previous_pct": from_previous,
            "dropoff_from_previous_pct": dropoff_from_previous,
            "status": status,
        }
        rows.append(row)
        previous_count = value

        print(
            f"FUNNEL_{key}=count:{value} "
            f"from_started:{from_start}% "
            f"from_previous:{from_previous}% "
            f"status:{status}"
        )

    weak_steps = [
        row for row in rows
        if row["status"] in {"critical_dropoff", "weak_step"}
    ]

    if started <= 0:
        top_recommendation = "Traffic is too low to diagnose conversion. Send test users through the demo and keep tracking."
    elif weak_steps:
        first = weak_steps[0]
        top_recommendation = (
            f"Focus next on {first['label']}: "
            f"{first['rate_from_started_pct']}% from start, "
            f"{first['dropoff_from_previous_pct']}% dropoff from previous step."
        )
    else:
        top_recommendation = "No critical dropoff detected yet. Continue collecting traffic and polish visible friction."

    report = {
        "version": 1,
        "status": "pass",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "source": SUMMARY_URL,
        "summary": {
            "total_events": count(summary, "_total"),
            "last_event": str(summary.get("_last_event", "")),
            "last_at": count(summary, "_last_at"),
            "character_started": started,
            "weak_step_count": len(weak_steps),
            "top_recommendation": top_recommendation,
        },
        "funnel": rows,
        "weak_steps": weak_steps,
        "privacy": {
            "private_mechanics_excluded": True,
            "user_response_details_excluded": True,
            "backend_math_hidden": True,
            "leak_scan_completed": True,
        },
    }

    assert_no_leaks("funnel report", report)

    JSON_OUT.write_text(json.dumps(report, indent=2))

    lines = []
    lines.append("# Emergence Conversion Funnel Report 114")
    lines.append("")
    lines.append("## Summary")
    lines.append("")
    lines.append(f"- Total safe events: `{report['summary']['total_events']}`")
    lines.append(f"- Character starts: `{started}`")
    lines.append(f"- Weak steps flagged: `{len(weak_steps)}`")
    lines.append(f"- Recommendation: {top_recommendation}")
    lines.append("")
    lines.append("## Funnel")
    lines.append("")
    lines.append("| Step | Count | From Start | From Previous | Dropoff | Status |")
    lines.append("|---|---:|---:|---:|---:|---|")
    for row in rows:
        lines.append(
            f"| {row['label']} | {row['count']} | "
            f"{row['rate_from_started_pct']}% | "
            f"{row['rate_from_previous_pct']}% | "
            f"{row['dropoff_from_previous_pct']}% | "
            f"{row['status']} |"
        )

    lines.append("")
    lines.append("## Weak Steps")
    lines.append("")
    if weak_steps:
        for row in weak_steps:
            lines.append(
                f"- **{row['label']}**: {row['rate_from_started_pct']}% from start, "
                f"{row['dropoff_from_previous_pct']}% dropoff from previous step."
            )
    else:
        lines.append("- No weak steps flagged yet, or traffic is too low.")

    lines.append("")
    lines.append("## Privacy")
    lines.append("")
    lines.append("- User response details are not included.")
    lines.append("- Private scoring details are not included.")
    lines.append("- Hidden routing details are not included.")
    lines.append("- Backend math remains hidden.")

    MD_OUT.write_text("\n".join(lines) + "\n")

    print("FUNNEL_REPORT_JSON_WRITTEN=PASS")
    print(f"FUNNEL_REPORT_JSON={JSON_OUT}")
    print("FUNNEL_REPORT_MD_WRITTEN=PASS")
    print(f"FUNNEL_REPORT_MD={MD_OUT}")
    print("FUNNEL_RATES_CALCULATED=PASS")
    print("FUNNEL_WEAK_STEPS_FLAGGED=PASS")
    print("FUNNEL_NO_RAW_SCORE_LEAK=PASS")
    print("EMERGENCE_CONVERSION_FUNNEL_REPORT=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
