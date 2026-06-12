#!/usr/bin/env python3
"""Export WeAreSwarm task planner feeds from the public contract SSOT."""
from __future__ import annotations

import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
SSOT = ROOT / "runtime" / "data" / "weareswarm_public_contract.json"
OUT_DIRS = [ROOT / "data" / "planner", ROOT / "_deploy" / "weareswarm" / "data" / "planner"]
KIDS_FLAGS = (
    "ai_heavy_lift",
    "copy_paste_ready",
    "no_secret_required",
    "no_billing_required",
    "no_destructive_repo_action",
    "no_live_trading_action",
)


def load_contract() -> dict:
    with SSOT.open(encoding="utf-8") as handle:
        return json.load(handle)


def kids_task(task: dict) -> bool:
    return all(task.get(flag) is True for flag in KIDS_FLAGS) and task.get("kids_safe") is True


def public_task(task: dict) -> dict:
    return {**task, "source_of_truth": str(SSOT.relative_to(ROOT))}


def write_json(name: str, payload: object) -> None:
    for out_dir in OUT_DIRS:
        out_dir.mkdir(parents=True, exist_ok=True)
        path = out_dir / name
        path.write_text(json.dumps(payload, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")
        print(f"wrote {path.relative_to(ROOT)}")


def main() -> None:
    contract = load_contract()
    tasks = [public_task(task) for task in contract["tasks"]]
    regular_kids = [task for task in tasks if kids_task(task) and not task.get("requires_adult_gate")]
    special = [task for task in tasks if task.get("mission_type") == "special_unlock"]
    kids_payload = {
        "last_sync": contract["last_sync"],
        "source_of_truth": str(SSOT.relative_to(ROOT)),
        "filter": {flag: True for flag in KIDS_FLAGS},
        "tasks": regular_kids,
        "special_missions": special,
    }
    write_json("all_tasks.json", {"last_sync": contract["last_sync"], "tasks": tasks})
    write_json("kids_tasks.json", kids_payload)
    write_json("next_lane.json", contract["next_lane"])
    write_json("skill_tree.json", {"last_sync": contract["last_sync"], **contract["skill_tree"]})


if __name__ == "__main__":
    main()
