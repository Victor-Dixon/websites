#!/usr/bin/env python3
from __future__ import annotations

import json
import os
from datetime import datetime, timezone
from pathlib import Path
from typing import Any


def read_json(path: Path) -> dict[str, Any]:
    if not path.exists():
        return {}
    try:
        value = json.loads(path.read_text(encoding="utf-8"))
        return value if isinstance(value, dict) else {}
    except Exception:
        return {}


def first_existing(paths: list[Path]) -> Path | None:
    for path in paths:
        if path.exists():
            return path
    return None


def normalize_lane(raw: str | None) -> str:
    lane = (raw or "").strip()
    if not lane or lane.lower() == "unknown":
        return "operator_queue"
    return lane


def titleize_lane(lane: str) -> str:
    return lane.replace("_", " ").replace("-", " ").title()


def task_row(name: str, state: str, next_action: str) -> dict[str, str]:
    return {
        "task": name,
        "name": name,
        "state": state,
        "progress": state.upper(),
        "next": next_action,
    }


def build_status(root: Path) -> dict[str, Any]:
    planner_candidates = [
        root / "data" / "reports" / "planner" / "next_lane.json",
        root / "data" / "reports" / "planner" / "latest_planner_event.json",
        root / "runtime" / "data" / "planner" / "next_lane.json",
        root / "runtime" / "reports" / "planner" / "next_lane.json",
        root
        / "runtime"
        / "quarantine"
        / "discord_architect_artifacts_001"
        / "discord_architect"
        / "data"
        / "runtime"
        / "events"
        / "latest_planner_event.json",
    ]
    planner_path = first_existing(planner_candidates)
    planner = read_json(planner_path) if planner_path else {}

    payload = planner.get("payload") if isinstance(planner.get("payload"), dict) else {}
    source = planner.get("source") or payload.get("source") or "dreamvault.planner"
    next_lane = normalize_lane(
        planner.get("next_lane")
        or payload.get("next_lane")
        or planner.get("lane")
        or payload.get("lane")
    )
    latest_task_preview = (
        planner.get("latest_task_preview")
        or payload.get("latest_task_preview")
        or planner.get("task")
        or ""
    )
    generated_at = datetime.now(timezone.utc).isoformat()
    lane_title = titleize_lane(next_lane)

    projects = [
        {
            "name": "Planner Bridge",
            "state": "active",
            "proof": (
                "WeAreSwarm status is generated from planner governance artifacts. "
                f"Source: {source}."
            ),
        },
        {
            "name": f"Active Lane: {lane_title}",
            "state": "queued",
            "proof": latest_task_preview
            or f"Planner selected next lane `{next_lane}` for operator execution.",
        },
        {
            "name": "WeAreSwarm Operations Board",
            "state": "operational",
            "proof": (
                "Projects and tasks refresh from generated JSON before static route rendering."
            ),
        },
    ]

    unfinished_tasks = [
        task_row(
            f"Execute {lane_title}",
            "next",
            latest_task_preview
            or f"Run the task artifact assigned to `{next_lane}` and publish closeout proof.",
        ),
        task_row(
            "Publish planner closeouts",
            "queued",
            "Map completed task reports into public /projects/ proof cards.",
        ),
        task_row(
            "Render static WeAreSwarm routes",
            "queued",
            "Run the static renderer after generated status changes.",
        ),
    ]

    return {
        "generated_at": generated_at,
        "updated_at": generated_at,
        "source": source,
        "planner_artifact": str(planner_path) if planner_path else None,
        "next_lane": next_lane,
        "projects": projects,
        "unfinished_tasks": unfinished_tasks,
        "feed": [
            {
                "title": "Planner status synchronized",
                "type": "planner_transition",
                "summary": f"Next lane is `{next_lane}`.",
                "generated_at": generated_at,
            }
        ],
    }


def main() -> int:
    root = Path(os.environ.get("DREAMVAULT_ROOT", ".")).resolve()
    out = root / "runtime" / "content" / "weareswarm.site" / "data" / "swarm-status.generated.json"
    report = root / "data" / "reports" / "weareswarm_planner_bridge" / "latest.md"

    out.parent.mkdir(parents=True, exist_ok=True)
    report.parent.mkdir(parents=True, exist_ok=True)

    status = build_status(root)
    out.write_text(json.dumps(status, indent=2, sort_keys=True) + "\n", encoding="utf-8")
    report.write_text(
        "\n".join(
            [
                "# WeAreSwarm Planner Bridge",
                "",
                f"generated_at: {status['generated_at']}",
                f"source: {status['source']}",
                f"planner_artifact: {status['planner_artifact']}",
                f"next_lane: {status['next_lane']}",
                "",
                "## Projects",
                *[
                    f"- {project['state']}: {project['name']} — {project['proof']}"
                    for project in status["projects"]
                ],
                "",
                "## Tasks",
                *[
                    f"- {task['state']}: {task['task']} — {task['next']}"
                    for task in status["unfinished_tasks"]
                ],
                "",
            ]
        ),
        encoding="utf-8",
    )

    print(f"STATUS_JSON={out}")
    print(f"REPORT={report}")
    print(f"NEXT_LANE={status['next_lane']}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
