#!/usr/bin/env python3
"""
Systematic repo organization helper
==================================

This script makes the repo navigable and ready for migration into a single layout.

What it does (safe by default):
- Ensures `websites/<domain>/` exists for each domain in configs/sites_registry.json
- Creates standard subfolders:
  - docs/, static/, overlays/, wp/wp-content/themes/, wp/wp-content/plugins/
- Writes `websites/<domain>/SITE_INFO.md` with pointers to current legacy locations

It does NOT move large site trees by default.
"""

from __future__ import annotations

import argparse
import json
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, List, Optional


REPO_ROOT = Path(__file__).resolve().parents[1]
SITES_REGISTRY = REPO_ROOT / "configs" / "sites_registry.json"


@dataclass(frozen=True)
class SiteMeta:
    domain: str
    mode: str
    purpose: str
    primary_owner: str
    notes: str


LEGACY_DOMAIN_POINTERS: Dict[str, List[str]] = {
    # Canonical domain -> legacy folder(s) that represent the real source today
    "freerideinvestor.com": ["FreeRideInvestor"],
    "weareswarm.site": ["Swarm_website"],
}


def load_sites_registry() -> Dict[str, dict]:
    return json.loads(SITES_REGISTRY.read_text(encoding="utf-8"))


def load_site_meta(domain: str, raw: dict) -> SiteMeta:
    return SiteMeta(
        domain=domain,
        mode=str(raw.get("mode", "")).strip() or "UNKNOWN",
        purpose=str(raw.get("purpose", "")).strip() or "",
        primary_owner=str(raw.get("primary_owner", "")).strip() or "",
        notes=str(raw.get("notes", "")).strip() or "",
    )


def existing_pointers(domain: str) -> List[Path]:
    pointers: List[Path] = []

    # Common conventions in this repo
    candidates = [
        REPO_ROOT / domain,
        REPO_ROOT / "sites" / domain,
    ]

    # Known legacy mappings
    for legacy in LEGACY_DOMAIN_POINTERS.get(domain, []):
        candidates.append(REPO_ROOT / legacy)

    # Some domains also have doc-only folders at repo root
    doc_folder = REPO_ROOT / domain
    if doc_folder.exists():
        candidates.append(doc_folder)

    seen = set()
    for p in candidates:
        if p.exists():
            rp = p.resolve()
            if rp not in seen:
                pointers.append(p)
                seen.add(rp)

    return pointers


def ensure_dir(path: Path, apply: bool) -> None:
    if path.exists():
        return
    if apply:
        path.mkdir(parents=True, exist_ok=True)


def write_text(path: Path, content: str, apply: bool) -> None:
    if path.exists():
        # keep user edits; do not overwrite
        return
    if apply:
        path.write_text(content, encoding="utf-8")


def build_site_info_md(meta: SiteMeta, pointers: List[Path]) -> str:
    rels = [str(p.relative_to(REPO_ROOT)) for p in pointers]
    rel_lines = "\n".join([f"- `{r}`" for r in rels]) if rels else "- (none found yet)"
    return (
        f"## {meta.domain}\n\n"
        f"- **mode**: {meta.mode}\n"
        f"- **purpose**: {meta.purpose}\n"
        f"- **primary_owner**: {meta.primary_owner}\n"
        f"- **notes**: {meta.notes}\n\n"
        f"### Current source locations (legacy pointers)\n\n"
        f"{rel_lines}\n\n"
        f"### Target layout (when migrated)\n\n"
        f"- `wp/wp-content/themes/<theme>/`\n"
        f"- `wp/wp-content/plugins/<plugin>/`\n"
        f"- `static/` (if not WordPress)\n"
    )


def main() -> int:
    parser = argparse.ArgumentParser(description="Create systematic websites/ structure + pointers")
    parser.add_argument("--apply", action="store_true", help="Write directories/files (default is dry-run)")
    args = parser.parse_args()

    if not SITES_REGISTRY.exists():
        raise SystemExit(f"Missing registry: {SITES_REGISTRY}")

    registry = load_sites_registry()
    domains = sorted(registry.keys())

    created_dirs: List[str] = []
    created_files: List[str] = []

    for domain in domains:
        meta = load_site_meta(domain, registry[domain])
        site_root = REPO_ROOT / "websites" / domain

        # Standard subfolders
        subdirs = [
            site_root / "docs",
            site_root / "static",
            site_root / "overlays",
            site_root / "wp" / "wp-content" / "themes",
            site_root / "wp" / "wp-content" / "plugins",
        ]

        for d in [site_root, *subdirs]:
            if not d.exists():
                created_dirs.append(str(d.relative_to(REPO_ROOT)))
            ensure_dir(d, apply=args.apply)

        pointers = existing_pointers(domain)
        info_path = site_root / "SITE_INFO.md"
        if not info_path.exists():
            created_files.append(str(info_path.relative_to(REPO_ROOT)))
        write_text(info_path, build_site_info_md(meta, pointers), apply=args.apply)

    mode = "APPLY" if args.apply else "DRY RUN"
    print(f"== organize_repo ({mode}) ==")
    print(f"sites: {len(domains)}")
    print(f"would create dirs: {len(created_dirs)}")
    for p in created_dirs[:50]:
        print(f"- {p}")
    if len(created_dirs) > 50:
        print(f"... +{len(created_dirs) - 50} more")
    print(f"would create files: {len(created_files)}")
    for p in created_files[:50]:
        print(f"- {p}")
    if len(created_files) > 50:
        print(f"... +{len(created_files) - 50} more")

    if not args.apply:
        print("\nRun again with --apply to write the structure.")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())

