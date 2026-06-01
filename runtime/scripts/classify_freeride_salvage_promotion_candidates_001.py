from __future__ import annotations

import json
from pathlib import Path

ROOT = Path.cwd()
SRC = ROOT / "runtime/salvage/freerideinvestor.com/custom-plugin-candidates"
OUT_JSON = ROOT / "data/reports/marketing/freeride_salvage_promotion_candidates_001.json"
OUT_MD = ROOT / "data/reports/marketing/freeride_salvage_promotion_candidates_001.md"

PROMOTE_TERMS = [
    "trading journal", "journal", "shortcode", "dashboard", "checklist",
    "stock", "market", "strategy", "planner", "trade"
]
REWRITE_TERMS = [
    "wp_query", "template_name", "get_header", "get_footer", "ajax",
    "admin-post", "nonce", "script", "style"
]
RISK_TERMS = [
    "eval(", "exec(", "shell_exec", "curl_exec", "file_get_contents('http",
    "password", "token", "secret", "api_key", "webhook"
]
ARCHIVE_TERMS = [
    "test", "testing", "sample", "demo"
]

def rel(path: Path) -> str:
    return str(path.relative_to(ROOT))

def classify(path: Path) -> dict:
    text = path.read_text(encoding="utf-8", errors="ignore")
    low = text.lower()
    low_path = str(path).lower()

    promote_score = sum(1 for t in PROMOTE_TERMS if t in low or t in low_path)
    rewrite_score = sum(1 for t in REWRITE_TERMS if t in low)
    risk_score = sum(1 for t in RISK_TERMS if t in low)
    archive_score = sum(1 for t in ARCHIVE_TERMS if t in low_path)

    decision = "archive"
    reason = []

    if risk_score:
      decision = "rewrite"
      reason.append("risk_terms_present")

    if promote_score >= 2 and risk_score == 0:
      decision = "promote_review"
      reason.append("strong_workflow_relevance")

    if rewrite_score >= 2:
      decision = "rewrite"
      reason.append("wordpress_coupled_needs_clean_rebuild")

    if archive_score and promote_score < 2:
      decision = "archive"
      reason.append("test_or_demo_artifact")

    if "page-trading-journal.php" in low_path:
      decision = "rewrite"
      reason.append("priority_trading_journal_surface")

    if "scripts/freeride-journal/app/main.py" in low_path:
      decision = "promote_review"
      reason.append("priority_python_journal_logic")

    if "inc/custom-shortcodes.php" in low_path:
      decision = "rewrite"
      reason.append("priority_shortcode_logic_extract_cleanly")

    if not reason:
      reason.append("low_signal_archive_review")

    return {
        "path": rel(path),
        "decision": decision,
        "promote_score": promote_score,
        "rewrite_score": rewrite_score,
        "risk_score": risk_score,
        "archive_score": archive_score,
        "reason": reason,
        "bytes": path.stat().st_size,
    }

def main() -> None:
    if not SRC.exists():
        raise SystemExit(f"MISSING_SRC={SRC}")

    files = sorted(p for p in SRC.rglob("*") if p.is_file())
    items = [classify(p) for p in files]

    counts = {}
    for item in items:
        counts[item["decision"]] = counts.get(item["decision"], 0) + 1

    priority = [
        i for i in items
        if i["path"].endswith("page-templates/page-Trading-Journal.php")
        or i["path"].endswith("scripts/freeride-journal/app/main.py")
        or i["path"].endswith("inc/custom-shortcodes.php")
    ]

    report = {
        "source": rel(SRC),
        "files_scanned": len(files),
        "decision_counts": counts,
        "priority": priority,
        "items": items,
        "status": "PASS",
    }
    OUT_JSON.write_text(json.dumps(report, indent=2), encoding="utf-8")

    lines = [
        "# FreeRideInvestor Salvage Promotion Candidates 001",
        "",
        f"- Status: `{report['status']}`",
        f"- Source: `{report['source']}`",
        f"- Files scanned: `{report['files_scanned']}`",
        "",
        "## Decision Counts",
    ]

    for k, v in sorted(counts.items()):
        lines.append(f"- `{k}`: `{v}`")

    lines.extend(["", "## Priority Files"])
    for item in priority:
        lines.append(f"- `{item['path']}` decision=`{item['decision']}` reason=`{', '.join(item['reason'])}`")

    for decision in ["promote_review", "rewrite", "archive", "discard"]:
        lines.extend(["", f"## {decision}"])
        for item in [i for i in items if i["decision"] == decision]:
            lines.append(f"- `{item['path']}` reason=`{', '.join(item['reason'])}`")

    lines.extend([
        "",
        "## Next",
        "- Rewrite the Trading Journal page into the clean FreeRideInvestor workflow.",
        "- Extract reusable Python journal logic if it is not dead/demo code.",
        "- Extract shortcodes as clean components only if they support the new funnel/workflow.",
    ])

    OUT_MD.write_text("\n".join(lines) + "\n", encoding="utf-8")

    print("FREERIDE_SALVAGE_PROMOTION_CLASSIFICATION=PASS")
    print(f"FILES_SCANNED={len(files)}")
    print(f"DECISION_COUNTS={counts}")
    print(f"REPORT_JSON={rel(OUT_JSON)}")
    print(f"REPORT_MD={rel(OUT_MD)}")

if __name__ == "__main__":
    main()
