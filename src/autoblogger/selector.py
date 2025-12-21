from __future__ import annotations

import json
from dataclasses import asdict
from datetime import date
from pathlib import Path
from typing import Any

import yaml

from .models import BacklogItem


def load_yaml(path: Path) -> dict[str, Any]:
    if not path.exists():
        return {}
    return yaml.safe_load(path.read_text(encoding="utf-8")) or {}


def load_backlog(backlog_path: Path) -> list[BacklogItem]:
    data = load_yaml(backlog_path)
    posts = data.get("posts") or []
    return [BacklogItem.from_dict(p) for p in posts]


def load_state(state_path: Path) -> dict[str, Any]:
    if not state_path.exists():
        return {"used_ids": [], "history": [], "failures": []}
    return json.loads(state_path.read_text(encoding="utf-8"))


def pick_post_id(today: date, calendar_path: Path, backlog: list[BacklogItem], state: dict[str, Any]) -> str:
    cal = load_yaml(calendar_path)
    schedule = cal.get("schedule") or {}
    today_key = today.isoformat()

    if today_key in schedule:
        return str(schedule[today_key]).strip()

    used = set(state.get("used_ids") or [])
    for item in backlog:
        if item.status.lower() == "ready" and item.id not in used:
            return item.id

    raise RuntimeError("No READY backlog items available (or all used). Add more posts to content/backlog.yaml")


def mark_backlog_used(backlog_path: Path, post_id: str) -> None:
    data = load_yaml(backlog_path)
    posts = data.get("posts") or []
    updated = False

    for p in posts:
        if str(p.get("id", "")).strip() == post_id:
            p["status"] = "used"
            updated = True
            break

    if not updated:
        raise RuntimeError(f"Post id '{post_id}' not found in backlog")

    backlog_path.write_text(yaml.safe_dump({"posts": posts}, sort_keys=False, allow_unicode=True), encoding="utf-8")


def get_backlog_item(backlog: list[BacklogItem], post_id: str) -> BacklogItem:
    for item in backlog:
        if item.id == post_id:
            return item
    raise RuntimeError(f"Post id '{post_id}' not found in backlog")
