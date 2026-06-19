#!/usr/bin/env python3
from __future__ import annotations

import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
CONTENT = ROOT / "runtime" / "content" / "meridian"
VISIBLE_DISTRICTS = {
    "spindle",
    "halcyon_heights",
    "drowned_quarter",
    "ladderways",
    "irongate",
}


def main() -> int:
    world = json.loads((CONTENT / "world.json").read_text(encoding="utf-8"))
    missions = json.loads((CONTENT / "missions.json").read_text(encoding="utf-8"))["missions"]
    grid = json.loads((CONTENT / "map-grid.json").read_text(encoding="utf-8"))

    assert world["city"] == "Meridian City"
    assert grid["hidden_layers"][0]["id"] == "undercity"
    assert grid["hidden_layers"][0]["visible_on_map"] is False

    visible_missions = [m for m in missions if not m.get("hidden_district")]
    undercity_missions = [m for m in missions if m.get("hidden_district")]

    for district in VISIBLE_DISTRICTS:
        district_missions = [m for m in visible_missions if m["district"] == district]
        assert district_missions, f"missing mission for {district}"

    assert undercity_missions, "missing undercity mission"

    for mission in missions:
        for key in ("id", "title", "district", "notoriety_reward", "risk", "faction_hooks", "rep_changes", "unlock"):
            assert key in mission, f"{mission.get('id')} missing {key}"

    print("MERIDIAN_MISSION_BOARD_VALIDATE=PASS")
    print(f"MISSIONS={len(missions)}")
    print(f"VISIBLE_DISTRICTS={len(VISIBLE_DISTRICTS)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
