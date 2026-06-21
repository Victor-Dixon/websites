#!/usr/bin/env python3
"""
Build kid-safe promptized task feed for WeAreSwarm kids planner.

Input:
  all_tasks.json

Output:
  kids_tasks.json
  data/reports/kids_planner/kids_task_feed_report.json

Rules:
  - Include only tasks where AI can do the heavy lifting.
  - Standard tasks must be copy-paste ready and require no secrets, billing,
    destructive repo action, live trading, DNS/domain access, or private account access.
  - Token/login/account tasks are allowed only as special_unlock with adult_gate true.
"""

from __future__ import annotations

import json
import re
from datetime import datetime, timezone
from pathlib import Path
from typing import Any, Dict, List, Tuple

ROOT = Path.cwd()

INPUT_CANDIDATES = [
    ROOT / "websites" / "weareswarm.online" / "data" / "planner" / "all_tasks.json",
    ROOT / "all_tasks.json",
    ROOT / "public" / "all_tasks.json",
    ROOT / "data" / "all_tasks.json",
    ROOT / "data" / "tasks" / "all_tasks.json",
]

OUTPUT_CANDIDATES = [
    ROOT / "websites" / "weareswarm.online" / "data" / "planner" / "kids_tasks.json",
    ROOT / "kids_tasks.json",
    ROOT / "public" / "kids_tasks.json",
]

REPORT_PATH = ROOT / "data" / "reports" / "kids_planner" / "kids_task_feed_report.json"

BLOCKED_PATTERNS = [
    r"\bsecret\b",
    r"\btoken\b",
    r"\bapi[_ -]?key\b",
    r"\bpassword\b",
    r"\bcredential\b",
    r"\bbilling\b",
    r"\bpayment\b",
    r"\bstripe\b",
    r"\bdns\b",
    r"\bdomain\b",
    r"\bhostinger\b",
    r"\bdeploy\b.*\bcredential\b",
    r"\bdelete\b",
    r"\bremove\b.*\bbranch\b",
    r"\bdrop\b.*\bdatabase\b",
    r"\bproduction\b.*\bmutation\b",
    r"\blive trading\b",
    r"\brobinhood\b",
    r"\bbrokerage\b",
    r"\bplace order\b",
    r"\bbuy\b.*\boption\b",
    r"\bsell\b.*\boption\b",
    r"\blogin\b",
    r"\bsign in\b",
    r"\baccount access\b",
]

SPECIAL_UNLOCK_PATTERNS = [
    r"\btoken\b",
    r"\blogin\b",
    r"\bsign in\b",
    r"\baccount\b",
    r"\bdns\b",
    r"\bdomain\b",
    r"\bhostinger\b",
    r"\bcredential\b",
    r"\bsecret\b",
]

GOOD_AI_PATTERNS = [
    r"\baudit\b",
    r"\breport\b",
    r"\bcopy\b",
    r"\bwrite\b",
    r"\bdraft\b",
    r"\bgenerate\b",
    r"\bcreate\b",
    r"\bbuild\b",
    r"\bfix\b",
    r"\bpatch\b",
    r"\btest\b",
    r"\bverify\b",
    r"\bhtml\b",
    r"\bcss\b",
    r"\bjson\b",
    r"\byaml\b",
    r"\bmarkdown\b",
    r"\bplanner\b",
    r"\bwebsite\b",
    r"\bpage\b",
    r"\bcard\b",
    r"\bbutton\b",
]


def read_json(path: Path) -> Any:
    return json.loads(path.read_text(encoding="utf-8"))


def write_json(path: Path, payload: Any) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(payload, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")


def find_input() -> Path:
    for path in INPUT_CANDIDATES:
        if path.exists():
            return path
    raise FileNotFoundError(
        "Could not find all_tasks.json. Checked: "
        + ", ".join(str(p) for p in INPUT_CANDIDATES)
    )


def normalize_task_list(raw: Any) -> List[Dict[str, Any]]:
    if isinstance(raw, list):
        return [x for x in raw if isinstance(x, dict)]

    if isinstance(raw, dict):
        for key in ("tasks", "items", "data", "all_tasks"):
            value = raw.get(key)
            if isinstance(value, list):
                return [x for x in value if isinstance(x, dict)]

    raise ValueError("Unsupported all_tasks.json shape. Expected list or object with tasks/items/data/all_tasks.")


def task_text(task: Dict[str, Any]) -> str:
    parts: List[str] = []
    for key in (
        "id",
        "title",
        "summary",
        "description",
        "desc",
        "prompt",
        "agent_prompt",
        "task",
        "objective",
        "lane",
        "type",
        "tags",
        "status",
        "executor",
    ):
        value = task.get(key)
        if value is None:
            continue
        if isinstance(value, (list, tuple)):
            parts.extend(str(v) for v in value)
        elif isinstance(value, dict):
            parts.append(json.dumps(value, sort_keys=True))
        else:
            parts.append(str(value))
    return "\n".join(parts).lower()


def match_any(patterns: List[str], text: str) -> bool:
    return any(re.search(pattern, text, flags=re.I | re.M) for pattern in patterns)


def get_str(task: Dict[str, Any], *keys: str, default: str = "") -> str:
    for key in keys:
        value = task.get(key)
        if value:
            return str(value)
    return default


def stable_id(task: Dict[str, Any], index: int) -> str:
    raw = get_str(task, "id", "task_id", "slug", default=f"task_{index + 1:04d}")
    raw = re.sub(r"[^a-zA-Z0-9_\-]+", "_", raw).strip("_").lower()
    return raw or f"task_{index + 1:04d}"


def is_special_unlock(task: Dict[str, Any], text: str) -> bool:
    explicit = str(task.get("mission_type", "")).lower() == "special_unlock"
    return explicit or match_any(SPECIAL_UNLOCK_PATTERNS, text)


def is_blocked_standard(task: Dict[str, Any], text: str) -> bool:
    return match_any(BLOCKED_PATTERNS, text)


def is_ai_heavy_candidate(task: Dict[str, Any], text: str) -> bool:
    if task.get("ai_heavy_lift") is True:
        return True
    if task.get("copy_paste_ready") is True:
        return True
    if task.get("executor") == "kids":
        return True
    return match_any(GOOD_AI_PATTERNS, text)


def points_for(task: Dict[str, Any], special: bool) -> int:
    raw = task.get("points")
    try:
        if raw is not None:
            points = int(raw)
            return max(10, min(points, 500))
    except Exception:
        pass
    return 150 if special else 50


def title_for(task: Dict[str, Any], task_id: str) -> str:
    title = get_str(task, "title", "name", "summary", default=task_id.replace("_", " ").title())
    return re.sub(r"\s+", " ", title).strip()


def summary_for(task: Dict[str, Any], title: str) -> str:
    summary = get_str(task, "summary", "description", "desc", "objective", default="")
    summary = re.sub(r"\s+", " ", summary).strip()
    return summary if summary else f"Use an AI agent to complete and verify: {title}."


def build_kid_prompt(title: str, summary: str, special: bool) -> str:
    gate = (
        "\n\nThis is a special unlock. Ask Victor/adult lead before running anything that needs login, tokens, accounts, domains, or private access."
        if special
        else ""
    )
    return (
        f"Mission: {title}\n\n"
        f"What you are doing:\n{summary}\n\n"
        "Copy the agent prompt into your coding agent. Watch what it changes. "
        "When it finishes, collect the proof: files changed, tests/build result, and closeout message."
        f"{gate}"
    )


def build_agent_prompt(task_id: str, title: str, summary: str, special: bool) -> str:
    adult_gate = (
        "\nSPECIAL UNLOCK GATE:\n"
        "- Stop before any login, token generation, account access, domain/DNS, billing, deployment secret, or private credential step.\n"
        "- Ask for adult approval before continuing.\n"
        "- Do not expose secrets in logs or files.\n"
        if special
        else ""
    )

    return f"""You are working on a WeAreSwarm kids planner mission.

MISSION_ID: {task_id}
MISSION_TITLE: {title}

OBJECTIVE:
{summary}

DREAM.OS EXECUTION RULES:
1. Use TARGET, ACTION, VERIFY, COMMIT.
2. Make the smallest safe change that closes the task.
3. Prefer copy-paste ready code or content.
4. Do not perform destructive repo actions.
5. Do not use secrets, billing, DNS/domain access, brokerage access, or live trading.
6. Do not delete branches, remove production data, or mutate live systems.
7. Verify with tests, build, file existence checks, screenshots, or exact output.
8. Return a closeout that a kid can paste into Discord and the website.

{adult_gate}

REQUIRED CLOSEOUT FORMAT:
Task:
Actions Taken:
Files Changed:
Verification:
Proof:
Discord Closeout:
Website Closeout:
Commit Message:
Status:

If the task cannot be completed safely, stop and return:
Status: BLOCKED
Reason:
Adult Gate Needed:
"""


def verify_steps_for(special: bool) -> List[str]:
    base = [
        "Confirm the agent returned Files Changed.",
        "Confirm the agent returned Verification.",
        "Confirm the agent returned Proof.",
        "Confirm the agent returned Discord Closeout.",
        "Confirm the agent returned Website Closeout.",
    ]
    if special:
        base.insert(0, "Confirm adult gate was approved before login/token/account/domain steps.")
    return base


def proof_fields_for() -> List[Dict[str, str]]:
    return [
        {"name": "closer_name", "label": "Who closed this task?", "type": "text", "required": "true"},
        {"name": "files_changed", "label": "Files changed", "type": "textarea", "required": "true"},
        {"name": "verification", "label": "Verification result", "type": "textarea", "required": "true"},
        {"name": "proof_link_or_output", "label": "Proof link or terminal output", "type": "textarea", "required": "true"},
        {"name": "discord_closeout", "label": "Discord closeout message", "type": "textarea", "required": "true"},
    ]


def convert_task(task: Dict[str, Any], index: int) -> Tuple[Dict[str, Any] | None, str]:
    text = task_text(task)
    task_id = stable_id(task, index)
    special = is_special_unlock(task, text)

    if not is_ai_heavy_candidate(task, text):
        return None, "not_ai_heavy"

    if is_blocked_standard(task, text) and not special:
        return None, "blocked_standard"

    title = title_for(task, task_id)
    summary = summary_for(task, title)

    mission_type = "special_unlock" if special else "standard"

    kid_task = {
        "id": f"kids_{task_id}",
        "title": title,
        "summary": summary,
        "source_task_id": task_id,
        "points": points_for(task, special),
        "mission_type": mission_type,
        "adult_gate": bool(special),
        "requires_adult_gate": bool(special),
        "locked": bool(special),
        "ai_heavy_lift": True,
        "copy_paste_ready": True,
        "kid_prompt": build_kid_prompt(title, summary, special),
        "agent_prompt": build_agent_prompt(task_id, title, summary, special),
        "verify_steps": verify_steps_for(special),
        "proof_fields": proof_fields_for(),
        "completion_discord_message": (
            f"✅ Mission closed: {title}\n"
            "Closer: {{closer_name}}\n"
            "Verification: {{verification}}\n"
            "Proof: {{proof_link_or_output}}"
        ),
        "completion_website_message": (
            f"{title} was completed by {{{{closer_name}}}}. "
            "Verification: {{verification}}"
        ),
        "safety": {
            "no_secret_required": not special,
            "no_billing_required": True,
            "no_destructive_repo_action": True,
            "no_live_trading_action": True,
            "adult_required_for_accounts_tokens_domains": bool(special),
        },
        "lane": get_str(task, "lane", default=""),
        "status": "available",
        "parent_approved": not special,
        "project": get_str(task, "lane", default="General"),
        "source": "weareswarm.online",
    }

    return kid_task, "included_special" if special else "included_standard"


def main() -> int:
    input_path = find_input()
    raw = read_json(input_path)
    tasks = normalize_task_list(raw)

    included: List[Dict[str, Any]] = []
    excluded: List[Dict[str, Any]] = []
    counts: Dict[str, int] = {}

    for index, task in enumerate(tasks):
        converted, reason = convert_task(task, index)
        counts[reason] = counts.get(reason, 0) + 1

        if converted:
            included.append(converted)
        else:
            excluded.append(
                {
                    "source_task_id": stable_id(task, index),
                    "title": title_for(task, stable_id(task, index)),
                    "reason": reason,
                }
            )

    included.sort(key=lambda x: (x["adult_gate"], -int(x["points"]), x["title"].lower()))

    payload = {
        "schema": "weareswarm.kids_tasks_promptized.v1",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "source": str(input_path),
        "count": len(included),
        "tasks": included,
    }

    for output_path in OUTPUT_CANDIDATES:
        write_json(output_path, payload)

    report = {
        "generated_at": payload["generated_at"],
        "source": str(input_path),
        "outputs": [str(p) for p in OUTPUT_CANDIDATES],
        "input_count": len(tasks),
        "included_count": len(included),
        "standard_count": sum(1 for t in included if t["mission_type"] == "standard"),
        "special_unlock_count": sum(1 for t in included if t["mission_type"] == "special_unlock"),
        "excluded_count": len(excluded),
        "counts_by_reason": counts,
        "excluded_sample": excluded[:100],
    }

    write_json(REPORT_PATH, report)

    print(json.dumps(report, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
