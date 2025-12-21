#!/usr/bin/env python3
"""
Repo Inventory (Websites / Themes / Plugins)
==========================================

Produces a deterministic inventory of:
- websites listed in configs/sites_registry.json
- where each site's theme(s) appear in the repo
- where each site's plugin(s) appear in the repo (best-effort)

This is meant to support a systematic migration into a single canonical layout.
"""

from __future__ import annotations

import json
import re
from dataclasses import dataclass
from pathlib import Path
from typing import Iterable, List, Optional, Tuple


REPO_ROOT = Path(__file__).resolve().parents[1]
SITES_REGISTRY = REPO_ROOT / "configs" / "sites_registry.json"


@dataclass(frozen=True)
class ThemeCandidate:
    path: Path
    theme_name: Optional[str]


def load_domains() -> List[str]:
    data = json.loads(SITES_REGISTRY.read_text(encoding="utf-8"))
    # registry keys are domains
    return sorted(data.keys())


def iter_style_css_files() -> Iterable[Path]:
    # A WP theme is typically identified by style.css with a header.
    # This scan is bounded by only looking for style.css filenames.
    yield from REPO_ROOT.rglob("style.css")


def parse_theme_name(style_css: Path) -> Optional[str]:
    try:
        text = style_css.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        return None
    m = re.search(r"^\s*Theme Name:\s*(.+?)\s*$", text, flags=re.MULTILINE)
    if not m:
        return None
    return m.group(1).strip()


def is_probable_wp_theme_dir(theme_dir: Path) -> bool:
    # Heuristic: contains style.css and at least one php file.
    if not (theme_dir / "style.css").exists():
        return False
    return any(p.suffix == ".php" for p in theme_dir.glob("*.php"))


def classify_theme_candidates(domains: List[str]) -> List[ThemeCandidate]:
    candidates: List[ThemeCandidate] = []
    for style_css in iter_style_css_files():
        theme_dir = style_css.parent
        if not is_probable_wp_theme_dir(theme_dir):
            continue

        # Keep only themes that are plausibly associated with one of our domains
        # by containment in a domain-named directory (common in this repo).
        parts = set(theme_dir.parts)
        if not any(d in parts for d in domains) and "wp-content" not in parts and "wordpress-theme" not in parts:
            # still keep it if it lives in a known theme-style location
            continue

        candidates.append(ThemeCandidate(path=theme_dir, theme_name=parse_theme_name(style_css)))

    # Stable output order
    return sorted(candidates, key=lambda c: str(c.path).lower())


def find_site_locations(domain: str) -> List[Path]:
    locations: List[Path] = []
    # Common patterns in this repo: root folder named as domain, or under sites/<domain>
    for p in [
        REPO_ROOT / domain,
        REPO_ROOT / "sites" / domain,
        REPO_ROOT / "sites" / f"{domain}",
    ]:
        if p.exists():
            locations.append(p)
    return locations


def main() -> int:
    if not SITES_REGISTRY.exists():
        raise SystemExit(f"Missing registry: {SITES_REGISTRY}")

    domains = load_domains()
    theme_candidates = classify_theme_candidates(domains)

    print("== Website inventory (from configs/sites_registry.json) ==")
    for d in domains:
        locs = find_site_locations(d)
        loc_str = ", ".join(str(p.relative_to(REPO_ROOT)) for p in locs) if locs else "(no directory found)"
        print(f"- {d}: {loc_str}")

    print("\n== Detected WordPress theme candidates (heuristic) ==")
    if not theme_candidates:
        print("(none found)")
    else:
        for t in theme_candidates:
            rel = t.path.relative_to(REPO_ROOT)
            name = t.theme_name or "(Theme Name header not found)"
            print(f"- {rel} :: {name}")

    print("\nNext: use this output to decide a safe migration plan.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

