from __future__ import annotations

import json
from pathlib import Path

ROOT = Path.cwd()
THEME = ROOT / "FreerideinvestorWebsite/_salvage/freerideinvestor-theme"
OUT_JSON = ROOT / "data/reports/marketing/freerideinvestor_salvage_manifest_001.json"
OUT_MD = ROOT / "data/reports/marketing/freerideinvestor_salvage_manifest_001.md"

PRESERVE_TERMS = [
    "plugin", "shortcode", "day", "planner", "trade", "trading", "stock",
    "market", "journal", "checklist", "dashboard", "pomodoro", "cron",
    "automation", "strategy", "chart"
]

ARCHIVE_TERMS = [
    "about", "contact", "courses", "login", "logout", "signup", "thank-you",
    "community", "services", "education", "dev-blog", "test-template"
]

def rel(path: Path) -> str:
    return str(path.relative_to(ROOT))

def classify(path: Path) -> dict:
    low = str(path).lower()
    name = path.name.lower()

    reason = []
    decision = "archive"

    if any(t in low for t in PRESERVE_TERMS):
        decision = "preserve_candidate"
        reason.append("matches_trading_or_plugin_term")

    if any(t in low for t in ARCHIVE_TERMS):
        if decision != "preserve_candidate":
            decision = "archive_candidate"
        reason.append("matches_generic_site_page_term")

    if "page-front-page.php" in name:
        decision = "replace_with_clean_funnel"
        reason.append("old_homepage_template")

    if "page-trading-journal" in name:
        decision = "rebuild_clean"
        reason.append("trading_journal_surface")

    if "day" in low and "trade" in low:
        decision = "preserve_candidate"
        reason.append("possible_day_trade_planner")

    if "inc/" in low or "/inc/" in low:
        reason.append("theme_include_or_plugin_logic")

    if "assets/" in low or "/js/" in low:
        reason.append("frontend_asset_review")

    return {
        "path": rel(path),
        "decision": decision,
        "reason": sorted(set(reason)) or ["default_archive_review"],
        "suffix": path.suffix,
        "bytes": path.stat().st_size if path.exists() and path.is_file() else None,
    }

def main() -> None:
    if not THEME.exists():
        raise SystemExit(f"MISSING_THEME={THEME}")

    files = [p for p in THEME.rglob("*") if p.is_file()]
    items = [classify(p) for p in files]

    counts = {}
    for item in items:
        counts[item["decision"]] = counts.get(item["decision"], 0) + 1

    preserve = [i for i in items if i["decision"] == "preserve_candidate"]
    rebuild = [i for i in items if i["decision"] == "rebuild_clean"]
    replace = [i for i in items if i["decision"] == "replace_with_clean_funnel"]

    report = {
        "theme_root": rel(THEME),
        "files_scanned": len(files),
        "decision_counts": counts,
        "canonical_decision": "clean_rebuild_with_plugin_salvage",
        "preserve_candidates": preserve,
        "rebuild_clean_candidates": rebuild,
        "replace_candidates": replace,
        "all_items": items,
        "recommended_site_shape": {
            "homepage": "sales funnel for agent-powered trading journal",
            "core_offer": "behavior replay + discipline scorecard",
            "workflow_pages": [
                "early access",
                "replay proof",
                "day trade planner",
                "behavior scorecard",
                "operator rules",
                "contact/intake"
            ],
            "plugin_salvage_focus": [
                "day trade planner",
                "trading journal",
                "shortcodes",
                "market data utilities",
                "checklist/dashboard tools"
            ],
        },
        "status": "PASS",
    }

    OUT_JSON.write_text(json.dumps(report, indent=2), encoding="utf-8")

    lines = [
        "# FreeRideInvestor Salvage Manifest 001",
        "",
        f"- Status: `{report['status']}`",
        f"- Theme root: `{report['theme_root']}`",
        f"- Files scanned: `{report['files_scanned']}`",
        f"- Canonical decision: `{report['canonical_decision']}`",
        "",
        "## Decision Counts",
    ]

    for k, v in sorted(counts.items()):
        lines.append(f"- `{k}`: `{v}`")

    lines.extend([
        "",
        "## Preserve Candidates",
    ])
    for item in preserve[:80]:
        lines.append(f"- `{item['path']}` reason=`{', '.join(item['reason'])}`")

    lines.extend([
        "",
        "## Rebuild Clean Candidates",
    ])
    for item in rebuild[:40]:
        lines.append(f"- `{item['path']}` reason=`{', '.join(item['reason'])}`")

    lines.extend([
        "",
        "## Replace Candidates",
    ])
    for item in replace[:20]:
        lines.append(f"- `{item['path']}` reason=`{', '.join(item['reason'])}`")

    lines.extend([
        "",
        "## Recommended Clean Site Shape",
        "- Homepage: sales funnel for agent-powered trading journal.",
        "- Product proof: TSLA behavior replay scorecard.",
        "- Workflow core: intake → replay → scorecard → rule candidate → Discord/operator card.",
        "- Preserve only plugins/tools that support the workflow.",
        "",
        "## Next",
        "1. Create clean `runtime/content/freerideinvestor.com/` site root.",
        "2. Add funnel as `index.html` or WordPress front-page package.",
        "3. Extract day trade planner/custom plugin candidates into a separate salvage bundle.",
        "4. Do not continue old theme as canonical.",
    ])

    OUT_MD.write_text("\n".join(lines) + "\n", encoding="utf-8")

    print("FREERIDEINVESTOR_SALVAGE_MANIFEST=PASS")
    print(f"THEME_ROOT={rel(THEME)}")
    print(f"FILES_SCANNED={len(files)}")
    print(f"DECISION_COUNTS={counts}")
    print(f"PRESERVE_CANDIDATES={len(preserve)}")
    print(f"REPORT_JSON={rel(OUT_JSON)}")
    print(f"REPORT_MD={rel(OUT_MD)}")

if __name__ == "__main__":
    main()
