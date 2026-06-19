#!/usr/bin/env python3
"""Export WeAreSwarm project portfolio feeds from the public contract SSOT."""
from __future__ import annotations

import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
SSOT = ROOT / "runtime" / "data" / "weareswarm_public_contract.json"
OUT_DIRS = [ROOT / "data" / "planner", ROOT / "_deploy" / "weareswarm" / "data" / "planner"]


def load_contract() -> dict:
    with SSOT.open(encoding="utf-8") as handle:
        return json.load(handle)


def write_json(name: str, payload: object) -> None:
    for out_dir in OUT_DIRS:
        out_dir.mkdir(parents=True, exist_ok=True)
        path = out_dir / name
        path.write_text(json.dumps(payload, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")
        print(f"wrote {path.relative_to(ROOT)}")


def board_card(project: dict) -> dict:
    keys = ("project_id", "title", "domain", "status", "category", "one_line", "proof", "live_url", "next_unlock")
    return {key: project.get(key, "") for key in keys}


def main() -> None:
    contract = load_contract()
    projects = [
        {**project, "source_of_truth": str(SSOT.relative_to(ROOT))}
        for project in contract["projects"]
    ]
    write_json("projects_full.json", {"last_sync": contract["last_sync"], "projects": projects})
    write_json(
        "projects_board_enriched.json",
        {"last_sync": contract["last_sync"], "projects": [board_card(project) for project in projects]},
    )
    write_json("closeouts.json", {"last_sync": contract["last_sync"], "closeouts": contract["closeouts"]})


if __name__ == "__main__":
    main()
