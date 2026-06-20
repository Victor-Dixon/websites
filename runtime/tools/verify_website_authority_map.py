#!/usr/bin/env python3
from pathlib import Path
import json
import sys

ROOT = Path(__file__).resolve().parents[2]
AUTH = ROOT / "runtime/authority/website_authority_map.v1.json"

SITE_ROOT_CANDIDATES = [
    ROOT / "runtime/content",
    ROOT / "sites/production/websites",
    ROOT / "websites",
    ROOT / "_deploy"
]

def collect_text(site: str) -> str:
    chunks = []
    for base in SITE_ROOT_CANDIDATES:
        p = base / site
        if not p.exists():
            continue
        for f in p.rglob("*"):
            if not f.is_file():
                continue
            if any(part in {".git", "node_modules", "vendor"} for part in f.parts):
                continue
            if f.suffix.lower() not in {".html", ".md", ".json", ".js", ".css", ".php", ".txt"}:
                continue
            try:
                chunks.append(f.read_text(errors="ignore")[:10000])
            except Exception:
                pass
    return "\n".join(chunks).lower()

def main() -> int:
    data = json.loads(AUTH.read_text())
    failures = []
    report = {
        "authority_file": str(AUTH),
        "sites": {}
    }

    for site, spec in data["sites"].items():
        text = collect_text(site)
        required = [x.lower() for x in spec.get("required_keywords_any", [])]
        forbidden = [x.lower() for x in spec.get("forbidden_keywords", [])]

        required_hits = [x for x in required if x in text]
        forbidden_hits = [x for x in forbidden if x in text]

        exists = bool(text.strip())
        ok = exists and bool(required_hits) and not forbidden_hits

        report["sites"][site] = {
            "role": spec["role"],
            "exists": exists,
            "required_hits": required_hits,
            "forbidden_hits": forbidden_hits,
            "ok": ok
        }

        if not ok:
            failures.append(site)

    out_dir = ROOT / "runtime/consolidation/reports"
    out_dir.mkdir(parents=True, exist_ok=True)
    out = out_dir / "website_authority_verify_latest.json"
    out.write_text(json.dumps(report, indent=2))

    print(f"VERIFY_REPORT={out}")
    if failures:
        print("VERIFY=FAIL")
        print("FAILURES=" + ",".join(failures))
        return 1

    print("VERIFY=PASS")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
