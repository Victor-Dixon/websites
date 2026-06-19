#!/usr/bin/env python3
import json
import re
from pathlib import Path
from datetime import datetime, timezone

DREAMVAULT = Path.home() / "projects" / "DreamVault"
DREAMVAULT_KIDS = DREAMVAULT / "runtime" / "tasks" / "kids"

WEBSITES = Path.home() / "websites"
KIDS_FEEDS = [
    WEBSITES / "data" / "planner" / "kids_tasks.json",
    WEBSITES / "_deploy" / "weareswarm" / "data" / "planner" / "kids_tasks.json",
]
ALL_FEEDS = [
    WEBSITES / "data" / "planner" / "all_tasks.json",
    WEBSITES / "_deploy" / "weareswarm" / "data" / "planner" / "all_tasks.json",
]

REPORT_JSON = WEBSITES / "data" / "reports" / "planner" / "dreamvault_kids_feed_bridge_001.json"
REPORT_MD = WEBSITES / "data" / "reports" / "planner" / "dreamvault_kids_feed_bridge_001.md"

REQUIRED_TRUE = [
    "kid_safe",
    "parent_approved",
    "ai_heavy_lift",
    "copy_paste_ready",
    "no_secret_required",
    "no_billing_required",
    "no_destructive_repo_action",
    "no_live_trading_action",
]

RISK_WORDS = [
    "secret", "token", "password", "credential", ".env",
    "billing", "payment", "stripe",
    "delete", "rm -rf", "destructive",
    "robinhood", "live trading", "brokerage",
    "hostinger", "sftp", "private key",
]


def parse_value(raw: str):
    raw = raw.strip()
    raw = raw.strip('"').strip("'")
    low = raw.lower()
    if low in {"true", "yes", "1"}:
        return True
    if low in {"false", "no", "0"}:
        return False
    return raw


def parse_simple_yaml(text: str) -> dict:
    data = {}
    lines = text.splitlines()
    i = 0

    while i < len(lines):
        line = lines[i]
        m = re.match(r"^([A-Za-z0-9_\-]+):\s*(.*)$", line)
        if not m:
            i += 1
            continue

        key = m.group(1)
        value = m.group(2).rstrip()

        if value in {"|", ">"}:
            block = []
            i += 1
            while i < len(lines):
                nxt = lines[i]
                if re.match(r"^[A-Za-z0-9_\-]+:\s*", nxt):
                    i -= 1
                    break
                block.append(nxt.strip("\n"))
                i += 1
            data[key] = "\n".join(block).strip()
        elif value == "":
            # crude list parser
            vals = []
            j = i + 1
            while j < len(lines):
                nxt = lines[j]
                if re.match(r"^[A-Za-z0-9_\-]+:\s*", nxt):
                    break
                lm = re.match(r"^\s*-\s+(.*)$", nxt)
                if lm:
                    vals.append(parse_value(lm.group(1)))
                j += 1
            if vals:
                data[key] = vals
                i = j - 1
            else:
                data[key] = ""
        else:
            data[key] = parse_value(value)

        i += 1

    return data


def load_feed(path: Path):
    if not path.exists():
        return {"tasks": []}

    try:
        data = json.loads(path.read_text(encoding="utf-8"))
    except Exception:
        return {"tasks": []}

    if isinstance(data, list):
        return {"tasks": data, "_shape": "list"}

    if isinstance(data, dict):
        if isinstance(data.get("tasks"), list):
            data["_shape"] = "dict_tasks"
            return data
        if isinstance(data.get("items"), list):
            data["tasks"] = data["items"]
            data["_shape"] = "dict_items"
            return data

    return {"tasks": []}


def save_feed(path: Path, feed: dict):
    path.parent.mkdir(parents=True, exist_ok=True)
    shape = feed.pop("_shape", "dict_tasks")

    if shape == "list":
        path.write_text(json.dumps(feed["tasks"], indent=2), encoding="utf-8")
    elif shape == "dict_items":
        feed["items"] = feed["tasks"]
        path.write_text(json.dumps(feed, indent=2), encoding="utf-8")
    else:
        path.write_text(json.dumps(feed, indent=2), encoding="utf-8")


def is_safe_task(data: dict, raw: str):
    for key in REQUIRED_TRUE:
        if data.get(key) is not True:
            return False, f"missing_or_false_flag:{key}"

    # Important:
    # Do NOT scan the full agent_prompt for risk words.
    # Kids-safe tasks intentionally include safety disclaimers like:
    # "Do not use secrets, tokens, credentials..." and "Do not delete files."
    # Those words are guardrails, not task requirements.
    risk_scan_fields = {
        "id": data.get("id", ""),
        "title": data.get("title", ""),
        "summary": data.get("summary", ""),
        "project": data.get("project", ""),
        "repo": data.get("repo", ""),
        "source_path": data.get("source_path", ""),
        "source_task_id": data.get("source_task_id", ""),
    }

    blob = json.dumps(risk_scan_fields, default=str).lower()
    hits = [w for w in RISK_WORDS if w in blob]

    if hits:
        return False, "risk_words:" + ",".join(hits[:5])

    return True, "safe"


def normalize_task(path: Path, data: dict) -> dict:
    task_id = str(data.get("id") or path.stem)
    title = str(data.get("title") or task_id)

    return {
        "id": task_id,
        "title": title,
        "status": data.get("status", "active"),
        "priority": data.get("priority", "P2"),
        "executor": "kids",
        "lane": "kids_planner",
        "project": data.get("project", "DreamVault"),
        "repo": "DreamVault",
        "origin": "dreamvault",
        "source_repo": "DreamVault",
        "source_path": str(path),
        "summary": data.get("summary", ""),
        "why_it_matters": data.get("why_it_matters", ""),
        "mission_type": data.get("mission_type", "copy_paste_agent_supervision"),
        "agent_prompt": data.get("agent_prompt", ""),
        "acceptance_criteria": data.get("acceptance_criteria", []),
        "proof_required": data.get("proof_required", []),
        "points": data.get("points", 100),
        "estimated_time_minutes": data.get("estimated_time_minutes", 20),
        "kid_safe": True,
        "parent_approved": True,
        "ai_heavy_lift": True,
        "copy_paste_ready": True,
        "no_secret_required": True,
        "no_billing_required": True,
        "no_destructive_repo_action": True,
        "no_live_trading_action": True,
    }


def merge_tasks(existing, imported):
    by_id = {}
    for task in existing:
        tid = task.get("id")
        if tid:
            by_id[tid] = task

    added = 0
    updated = 0

    for task in imported:
        tid = task["id"]
        if tid in by_id:
            merged = dict(by_id[tid])
            merged.update(task)
            by_id[tid] = merged
            updated += 1
        else:
            by_id[tid] = task
            added += 1

    return list(by_id.values()), added, updated


def main():
    if not DREAMVAULT_KIDS.exists():
        raise SystemExit(f"DreamVault kids task folder not found: {DREAMVAULT_KIDS}")

    imported = []
    skipped = []

    for path in sorted(DREAMVAULT_KIDS.glob("*.yaml")):
        raw = path.read_text(encoding="utf-8", errors="replace")
        data = parse_simple_yaml(raw)
        ok, reason = is_safe_task(data, raw)
        if ok:
            imported.append(normalize_task(path, data))
        else:
            skipped.append({
                "path": str(path),
                "id": data.get("id", path.stem),
                "title": data.get("title", path.stem),
                "reason": reason,
            })

    results = {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "source_dir": str(DREAMVAULT_KIDS),
        "imported_count": len(imported),
        "skipped_count": len(skipped),
        "imported_ids": [x["id"] for x in imported],
        "skipped": skipped,
        "feeds_written": [],
    }

    for feed_path in KIDS_FEEDS:
        feed = load_feed(feed_path)
        merged, added, updated = merge_tasks(feed["tasks"], imported)
        feed["tasks"] = merged
        feed["generated_at"] = datetime.now(timezone.utc).isoformat()
        feed["dreamvault_import_count"] = len(imported)
        save_feed(feed_path, feed)
        results["feeds_written"].append({
            "path": str(feed_path),
            "type": "kids",
            "total": len(merged),
            "added": added,
            "updated": updated,
        })

    for feed_path in ALL_FEEDS:
        if not feed_path.exists():
            continue
        feed = load_feed(feed_path)
        merged, added, updated = merge_tasks(feed["tasks"], imported)
        feed["tasks"] = merged
        feed["generated_at"] = datetime.now(timezone.utc).isoformat()
        feed["dreamvault_import_count"] = len(imported)
        save_feed(feed_path, feed)
        results["feeds_written"].append({
            "path": str(feed_path),
            "type": "all",
            "total": len(merged),
            "added": added,
            "updated": updated,
        })

    REPORT_JSON.parent.mkdir(parents=True, exist_ok=True)
    REPORT_JSON.write_text(json.dumps(results, indent=2), encoding="utf-8")

    md = "# DreamVault → WeAreSwarm Kids Feed Bridge\n\n"
    md += f"Generated: `{results['generated_at']}`\n\n"
    md += f"Imported: `{len(imported)}`\n\n"
    md += f"Skipped: `{len(skipped)}`\n\n"
    md += "## Imported IDs\n\n"
    for task in imported:
        md += f"- `{task['id']}` — {task['title']}\n"
    md += "\n## Feeds Written\n\n"
    for f in results["feeds_written"]:
        md += f"- `{f['path']}` total={f['total']} added={f['added']} updated={f['updated']}\n"
    if skipped:
        md += "\n## Skipped\n\n"
        for item in skipped:
            md += f"- `{item['id']}` — {item['reason']}\n"

    REPORT_MD.write_text(md, encoding="utf-8")

    print("TARGET: DreamVault to WeAreSwarm kids feed bridge")
    print(f"IMPORTED={len(imported)}")
    print(f"SKIPPED={len(skipped)}")
    for f in results["feeds_written"]:
        print(f"FEED={f['path']} total={f['total']} added={f['added']} updated={f['updated']}")
    print(f"REPORT_JSON={REPORT_JSON}")
    print(f"REPORT_MD={REPORT_MD}")
    print("STATUS=BRIDGE_IMPORT_COMPLETE")


if __name__ == "__main__":
    main()
