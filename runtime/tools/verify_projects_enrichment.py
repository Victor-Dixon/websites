#!/usr/bin/env python3
"""Verify projects_board_enriched.json has truthful metadata."""
import json
from collections import Counter
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
ENRICHED = ROOT / "websites" / "weareswarm.online" / "data" / "planner" / "projects_board_enriched.json"
BOARD = ROOT / "websites" / "weareswarm.online" / "data" / "planner" / "public_project_board.json"


def main() -> int:
    d = json.loads(ENRICHED.read_text(encoding="utf-8"))
    projects = d.get("projects", [])
    missing = [x["project"] for x in projects if not (x.get("tagline") or x.get("description"))]
    buckets = Counter(x.get("bucket") for x in projects)
    board = json.loads(BOARD.read_text(encoding="utf-8"))
    board_buckets = {k: len(v) for k, v in (board.get("buckets") or {}).items()}

    by_canonical = {x.get("canonical_id"): x.get("bucket") for x in projects}
    checks = {
        "AgentTools": by_canonical.get("AgentTools"),
        "projectscanner": by_canonical.get("projectscanner"),
        "AutoDream.Os": by_canonical.get("AutoDream.Os"),
        "Victor.os": by_canonical.get("Victor.os"),
        "SWARM": by_canonical.get("SWARM"),
        "DreamVault": by_canonical.get("DreamVault"),
    }

    print(f"project_count={len(projects)}")
    print(f"missing_descriptions={len(missing)} {missing}")
    print(f"bucket_counts={dict(buckets)}")
    print(f"board_bucket_counts={board_buckets}")
    print(f"classification_checks={checks}")
    for x in projects:
        print(
            f"  {x.get('project')} -> {x.get('canonical_id')} [{x.get('bucket')}] "
            f"tagline={bool(x.get('tagline'))} desc={bool(x.get('description'))}"
        )
    return 1 if missing else 0


if __name__ == "__main__":
    raise SystemExit(main())
