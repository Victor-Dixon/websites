#!/usr/bin/env python3
from __future__ import annotations

import json
import sys
from pathlib import Path

ROOT = Path.cwd()
PATHS = [
    ROOT / "websites" / "weareswarm.online" / "data" / "planner" / "kids_tasks.json",
    ROOT / "kids_tasks.json",
    ROOT / "public" / "kids_tasks.json",
]


def fail(msg: str) -> int:
    print(f"FAIL: {msg}")
    return 1


def main() -> int:
    found = [p for p in PATHS if p.exists()]
    if not found:
        return fail("kids_tasks.json not found in root or public")

    for path in found:
        payload = json.loads(path.read_text(encoding="utf-8"))
        tasks = payload.get("tasks", [])
        if not tasks:
            return fail(f"{path} has zero tasks")

        for i, task in enumerate(tasks):
            prefix = f"{path} task[{i}] {task.get('id')}"
            for field in ["kid_prompt", "agent_prompt", "verify_steps", "points", "mission_type", "adult_gate"]:
                if field not in task:
                    return fail(f"{prefix} missing {field}")

            if not task["kid_prompt"].strip():
                return fail(f"{prefix} empty kid_prompt")

            if not task["agent_prompt"].strip():
                return fail(f"{prefix} empty agent_prompt")

            if not task["verify_steps"]:
                return fail(f"{prefix} empty verify_steps")

            if task.get("mission_type") == "special_unlock" and task.get("adult_gate") is not True:
                return fail(f"{prefix} special_unlock missing adult_gate true")

            if task.get("mission_type") != "special_unlock":
                safety = task.get("safety", {})
                required_true = [
                    "no_secret_required",
                    "no_billing_required",
                    "no_destructive_repo_action",
                    "no_live_trading_action",
                ]
                for key in required_true:
                    if safety.get(key) is not True:
                        return fail(f"{prefix} standard task safety.{key} is not true")

    print("PASS: kids task feed verified")
    print("files:", ", ".join(str(p) for p in found))
    return 0


if __name__ == "__main__":
    sys.exit(main())
