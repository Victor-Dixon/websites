#!/usr/bin/env python3
from __future__ import annotations

import json
import os
import re
import subprocess
from datetime import datetime, timezone
from pathlib import Path
from typing import Any

SECRET_VALUE_PATTERNS = [
    re.compile(r"(ghp_|github_pat_|xox[baprs]-)", re.I),
    re.compile(r"(api[_-]?key|secret|password|token)\s*[:=]\s*['\"]?[A-Za-z0-9_\-]{8,}", re.I),
]

CANONICAL_SEED = [
    {
        "name": "DreamVault",
        "kind": "canonical_core",
        "source": "seed",
        "repo": "Victor-Dixon/DreamVault",
        "local_paths": [
            "D:\\DreamVault",
            "/data/data/com.termux/files/home/projects/DreamVault",
        ],
        "status": "canonical",
        "consolidation_action": "preserve",
        "proof": "Planner, runtime tasks, bridge scripts, governance reports.",
        "next": "Continue pruning generated reports and promotion candidates.",
    },
    {
        "name": "websites",
        "kind": "website",
        "source": "seed",
        "repo": "Victor-Dixon/websites",
        "local_paths": ["D:\\websites"],
        "status": "public_surface",
        "consolidation_action": "publish_or_consolidate_under_websites",
        "proof": "WeAreSwarm deploy source, project board, planner bridge.",
        "next": "Publish generated project and task boards to live routes.",
    },
]


def run(cmd: list[str]) -> str:
    try:
        return subprocess.check_output(cmd, text=True, stderr=subprocess.DEVNULL)
    except Exception:
        return ""


def safe_name(path: Path) -> str:
    return path.name.strip()


def classify(name: str, path: str = "", repo: str = "") -> str:
    n = name.lower()
    joined = f"{name} {path} {repo}".lower()

    if n in {"dreamvault", "dreamos"} or "dreamvault" in joined:
        return "canonical_core"

    if n in {"agenttools", "projectscanner"} or "projectscanner" in joined:
        return "toolbelt"

    if any(
        x in joined
        for x in [
            "weareswarm",
            "dadudekc",
            "freerideinvestor",
            "xthunder",
            "ariajet",
            "websites",
            "digitaldreamscape",
            "crosbyultimateevents",
        ]
    ):
        return "website"

    if any(x in joined for x in ["dream.os", "victor.os", "autodream", "headless", "variant"]):
        return "promotion_candidate"

    if any(x in joined for x in ["backup", "old", "archive", "copy", "snapshot"]):
        return "archive_candidate"

    return "unknown"


def action_for(kind: str) -> str:
    return {
        "canonical_core": "preserve",
        "toolbelt": "preserve_as_toolbelt",
        "website": "publish_or_consolidate_under_websites",
        "promotion_candidate": "inspect_then_promote_or_archive",
        "archive_candidate": "salvage_then_archive",
        "unknown": "inspect",
    }.get(kind, "inspect")


def public_status(kind: str) -> str:
    return {
        "canonical_core": "canonical",
        "toolbelt": "supporting_tool",
        "website": "public_surface",
        "promotion_candidate": "candidate",
        "archive_candidate": "archive_candidate",
        "unknown": "needs_review",
    }.get(kind, "needs_review")


def next_step(kind: str) -> str:
    return {
        "canonical_core": "Protect as source of truth; merge only verified promotion candidates.",
        "toolbelt": "Keep separate; expose as supporting automation tooling.",
        "website": "Connect to WeAreSwarm project board and deploy reports.",
        "promotion_candidate": "Compare against canonical DreamVault before merge.",
        "archive_candidate": "Generate salvage manifest before pruning.",
        "unknown": "Inspect README, git status, remotes, and last activity.",
    }.get(kind, "Inspect.")


def github_repos() -> list[dict[str, Any]]:
    raw = run(
        [
            "gh",
            "repo",
            "list",
            "Victor-Dixon",
            "--limit",
            "200",
            "--json",
            "name,nameWithOwner,url,description,isPrivate,updatedAt",
        ]
    )
    if not raw.strip():
        return []

    try:
        repos = json.loads(raw)
    except Exception:
        return []

    items: list[dict[str, Any]] = []
    for repo in repos:
        name = repo.get("name") or ""
        owner = repo.get("nameWithOwner") or ""
        kind = classify(name, repo=owner)
        items.append(
            {
                "name": name,
                "kind": kind,
                "source": "github",
                "repo": owner,
                "url": repo.get("url"),
                "description": repo.get("description") or "",
                "is_private": bool(repo.get("isPrivate")),
                "updated_at": repo.get("updatedAt"),
                "status": public_status(kind),
                "consolidation_action": action_for(kind),
                "proof": f"GitHub repository detected: {owner}",
                "next": next_step(kind),
                "local_paths": [],
            }
        )
    return items


def local_projects(local_roots: list[Path]) -> list[dict[str, Any]]:
    items: list[dict[str, Any]] = []
    seen: set[str] = set()

    for root in local_roots:
        if not root.exists() or not root.is_dir():
            continue

        candidates = [root]
        if root.name.lower() in {"projects", "websites", "dreamvault"}:
            candidates = [root, *root.iterdir()]

        for base in candidates:
            if not base.is_dir():
                continue

            is_git = (base / ".git").exists()
            has_runtime = (base / "runtime").exists()
            has_package = (base / "package.json").exists() or (base / "pyproject.toml").exists()

            if base == root and not (is_git or has_runtime or has_package):
                continue
            if base != root and not (is_git or has_runtime or has_package):
                continue

            key = str(base.resolve()).lower()
            if key in seen:
                continue
            seen.add(key)

            name = safe_name(base)
            remote = run(["git", "-C", str(base), "remote", "get-url", "origin"]).strip() if is_git else ""
            branch = run(["git", "-C", str(base), "branch", "--show-current"]).strip() if is_git else ""
            head = run(["git", "-C", str(base), "rev-parse", "--short", "HEAD"]).strip() if is_git else ""
            status_short = run(["git", "-C", str(base), "status", "--short"]) if is_git else ""

            kind = classify(name, path=str(base), repo=remote)
            items.append(
                {
                    "name": name,
                    "kind": kind,
                    "source": "local",
                    "repo": remote,
                    "local_paths": [str(base)],
                    "branch": branch,
                    "head": head,
                    "dirty": bool(status_short.strip()),
                    "status": public_status(kind),
                    "consolidation_action": action_for(kind),
                    "proof": f"Local project detected at {base}",
                    "next": next_step(kind),
                }
            )

    return items


def merge_projects(
    github_items: list[dict[str, Any]], local_items: list[dict[str, Any]]
) -> list[dict[str, Any]]:
    merged: dict[str, dict[str, Any]] = {}

    for item in github_items:
        key = (item.get("repo") or item.get("name") or "").lower()
        merged[key] = item

    for item in local_items:
        remote = (item.get("repo") or "").lower()
        name_key = (item.get("name") or "").lower()
        matched_key = None

        for key in merged:
            if remote and (remote in key or key in remote):
                matched_key = key
                break
            if name_key and name_key == (merged[key].get("name") or "").lower():
                matched_key = key
                break

        if matched_key:
            merged[matched_key].setdefault("local_paths", [])
            for path in item.get("local_paths", []):
                if path not in merged[matched_key]["local_paths"]:
                    merged[matched_key]["local_paths"].append(path)
            merged[matched_key]["local_dirty"] = item.get("dirty", False)
            merged[matched_key]["local_branch"] = item.get("branch", "")
            merged[matched_key]["local_head"] = item.get("head", "")
        else:
            key = f"local:{item.get('local_paths', [''])[0]}".lower()
            merged[key] = item

    order = {
        "canonical_core": 0,
        "website": 1,
        "toolbelt": 2,
        "promotion_candidate": 3,
        "unknown": 4,
        "archive_candidate": 5,
    }

    return sorted(
        merged.values(),
        key=lambda x: (order.get(x.get("kind", "unknown"), 9), x.get("name", "").lower()),
    )


def assert_no_secrets(data: dict[str, Any]) -> None:
    text = json.dumps(data, sort_keys=True)
    for pattern in SECRET_VALUE_PATTERNS:
        if pattern.search(text):
            raise AssertionError("secret-looking value detected in public board")


def main() -> int:
    root = Path(os.environ.get("DREAMVAULT_ROOT", ".")).resolve()
    local_roots_raw = os.environ.get("PROJECT_BOARD_LOCAL_ROOTS", str(root))
    local_roots = [Path(p.strip()) for p in local_roots_raw.split(os.pathsep) if p.strip()]

    generated_at = datetime.now(timezone.utc).isoformat()

    gh_items = github_repos()
    local_items = local_projects(local_roots)
    projects = merge_projects(gh_items, local_items)

    if not projects:
        projects = list(CANONICAL_SEED)

    buckets: dict[str, int] = {}
    for project in projects:
        buckets[project["kind"]] = buckets.get(project["kind"], 0) + 1

    data = {
        "generated_at": generated_at,
        "source": "dreamvault.project_board",
        "intent": "Public project consolidation board for GitHub, desktop, and laptop projects.",
        "buckets": buckets,
        "projects": projects,
        "tasks_projection": [
            {
                "name": "Classify project inventory",
                "state": "active",
                "next": "Review unknown and promotion_candidate projects before merge/prune.",
            },
            {
                "name": "Promote canonical work",
                "state": "queued",
                "next": "Move verified work into canonical GitHub repos with manifests.",
            },
            {
                "name": "Archive stale variants",
                "state": "queued",
                "next": "Create salvage manifests before destructive cleanup.",
            },
        ],
    }

    assert_no_secrets(data)

    out = root / "runtime" / "content" / "weareswarm.site" / "data" / "project-board.generated.json"
    report = root / "data" / "reports" / "project_board" / "latest.md"

    out.parent.mkdir(parents=True, exist_ok=True)
    report.parent.mkdir(parents=True, exist_ok=True)

    out.write_text(json.dumps(data, indent=2, sort_keys=True) + "\n", encoding="utf-8")

    lines = [
        "# WeAreSwarm Project Board",
        "",
        f"generated_at: {generated_at}",
        f"projects: {len(projects)}",
        "",
        "## Buckets",
        *[f"- {key}: {value}" for key, value in sorted(buckets.items())],
        "",
        "## Projects",
    ]
    for project in projects:
        lines.append(
            f"- {project['kind']} / {project['status']}: {project['name']} — {project['consolidation_action']}"
        )

    report.write_text("\n".join(lines) + "\n", encoding="utf-8")

    print(f"PROJECT_BOARD_JSON={out}")
    print(f"REPORT={report}")
    print(f"PROJECTS={len(projects)}")
    for key, value in sorted(buckets.items()):
        print(f"BUCKET_{key.upper()}={value}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
