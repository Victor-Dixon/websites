from __future__ import annotations

import json
from pathlib import Path
from typing import Any

ROOT = Path.cwd()

MATCHES_FILE = ROOT / "runtime/trading_journal/data/robinhood_tsla_may_market_matches_yfinance_all_001.json"
OUT_JSON = ROOT / "data/reports/trading/robinhood_contract_identity_raw_source_rank_001.json"
OUT_MD = ROOT / "data/reports/trading/robinhood_contract_identity_raw_source_rank_001.md"

SEARCH_ROOTS = [
    ROOT / "runtime/trading_journal",
    ROOT / "data/reports/trading",
    ROOT / "data",
    ROOT / "runtime",
]

EXCLUDE_PARTS = {
    "data/reports/cpc",
    "runtime/exports",
    "__pycache__",
}

EXCLUDE_FILE_TERMS = [
    "tsla_option_trade_pairs",
    "robinhood_tsla_matches_yfinance",
    "tsla_yfinance_full_rebuild",
    "robinhood_contract_identity_source_rank",
    "robinhood_option_contract_identity",
    "tsla_intraday",
    "yfinance_fetch",
    "candle_locator",
    "market_matches_yfinance",
]

FILL_KEYS = {
    "order_id", "activity_type", "asset_type", "order_type",
    "price", "quantity", "side", "state", "symbol", "timestamp",
}

CONTRACT_KEYS = {
    "option_symbol", "option_id", "option_contract_id", "contract_id",
    "instrument", "instrument_id", "instrument_url", "option", "contract",
    "strike", "expiration", "expiry", "expiration_date",
    "chain_id", "chain_symbol", "type", "call", "put",
    "position_effect", "opening_strategy", "closing_strategy",
}

def rel(path: Path) -> str:
    return str(path.relative_to(ROOT))

def should_exclude(path: Path) -> bool:
    rp = rel(path)
    low = rp.lower()
    if any(part in low for part in EXCLUDE_PARTS):
        return True
    if any(term in low for term in EXCLUDE_FILE_TERMS):
        return True
    return False

def scrub(obj: Any) -> Any:
    secret_terms = ("token", "secret", "password", "webhook", "authorization", "api_key")
    if isinstance(obj, dict):
        out = {}
        for k, v in obj.items():
            lk = str(k).lower()
            if any(t in lk for t in secret_terms):
                out[k] = "[REDACTED]"
            elif lk == "url" and isinstance(v, str):
                out[k] = "[URL_REDACTED]"
            else:
                out[k] = scrub(v)
        return out
    if isinstance(obj, list):
        return [scrub(x) for x in obj[:5]]
    if isinstance(obj, str) and len(obj) > 180:
        return obj[:180] + "..."
    return obj

def walk(obj: Any, path: str = ""):
    if isinstance(obj, dict):
        yield path, obj
        for k, v in obj.items():
            yield from walk(v, f"{path}.{k}" if path else str(k))
    elif isinstance(obj, list):
        for i, v in enumerate(obj[:5000]):
            yield from walk(v, f"{path}[{i}]")

def load_known_order_ids() -> set[str]:
    data = json.loads(MATCHES_FILE.read_text(encoding="utf-8", errors="ignore"))
    ids = set()
    for m in data.get("matches", []):
        fill = m.get("fill") or {}
        if fill.get("order_id"):
            ids.add(str(fill["order_id"]))
    return ids

def main() -> None:
    known_order_ids = load_known_order_ids()

    files = []
    seen = set()
    for root in SEARCH_ROOTS:
        if not root.exists():
            continue
        for path in root.rglob("*.json"):
            if path in seen or should_exclude(path):
                continue
            seen.add(path)
            files.append(path)

    ranked = []

    for path in files:
        try:
            data = json.loads(path.read_text(encoding="utf-8", errors="ignore"))
        except Exception:
            continue

        text = json.dumps(data, sort_keys=True, default=str)[:500000]
        low_text = text.lower()

        known_id_hits = [oid for oid in known_order_ids if oid in text]
        best_nodes = []

        file_score = 0

        for node_path, node in walk(data):
            if not isinstance(node, dict):
                continue

            keys = {str(k).lower() for k in node.keys()}
            fill_overlap = keys & FILL_KEYS
            contract_overlap = keys & CONTRACT_KEYS

            score = 0
            score += len(fill_overlap) * 5
            score += len(contract_overlap) * 8

            node_text = json.dumps(node, sort_keys=True, default=str)[:8000]
            node_order_hits = [oid for oid in known_order_ids if oid in node_text]
            score += len(node_order_hits) * 40

            if score >= 15:
                best_nodes.append({
                    "path": node_path,
                    "score": score,
                    "fill_keys": sorted(fill_overlap),
                    "contract_keys": sorted(contract_overlap),
                    "known_order_id_hits": len(node_order_hits),
                    "sample": scrub(node),
                })
                file_score += score

        if known_id_hits:
            file_score += len(known_id_hits) * 50

        raw_terms = ["robinhood", "activity", "order", "fill", "position", "option"]
        file_score += sum(1 for t in raw_terms if t in low_text)

        if best_nodes or known_id_hits:
            has_contract = any(n["contract_keys"] for n in best_nodes)
            has_fill = any(n["fill_keys"] for n in best_nodes)
            has_known_order = bool(known_id_hits)

            if has_contract and has_known_order:
                classification = "RAW_FILL_LEVEL_CONTRACT_SOURCE"
            elif has_contract and has_fill:
                classification = "POSSIBLE_RAW_CONTRACT_SOURCE"
            elif has_fill:
                classification = "RAW_FILL_SOURCE_NO_CONTRACT"
            else:
                classification = "CONTEXT_ONLY"

            ranked.append({
                "file": rel(path),
                "score": file_score,
                "classification": classification,
                "known_order_id_hits": len(known_id_hits),
                "best_nodes": sorted(best_nodes, key=lambda x: x["score"], reverse=True)[:5],
            })

    ranked.sort(key=lambda x: x["score"], reverse=True)

    verdict = "NO_RAW_FILL_LEVEL_CONTRACT_SOURCE_FOUND"
    if any(r["classification"] == "RAW_FILL_LEVEL_CONTRACT_SOURCE" for r in ranked):
        verdict = "RAW_FILL_LEVEL_CONTRACT_SOURCE_FOUND"
    elif any(r["classification"] == "POSSIBLE_RAW_CONTRACT_SOURCE" for r in ranked):
        verdict = "POSSIBLE_RAW_CONTRACT_SOURCE_FOUND"
    elif any(r["classification"] == "RAW_FILL_SOURCE_NO_CONTRACT" for r in ranked):
        verdict = "RAW_FILL_SOURCE_FOUND_BUT_NO_CONTRACT"

    report = {
        "known_order_ids": len(known_order_ids),
        "files_scanned_after_exclusions": len(files),
        "ranked_files": len(ranked),
        "verdict": verdict,
        "top_ranked": ranked[:50],
    }

    OUT_JSON.write_text(json.dumps(report, indent=2), encoding="utf-8")

    lines = [
        "# Robinhood Contract Identity Raw Source Rank 001",
        "",
        f"- Known order IDs: `{report['known_order_ids']}`",
        f"- Files scanned after exclusions: `{report['files_scanned_after_exclusions']}`",
        f"- Ranked files: `{report['ranked_files']}`",
        f"- Verdict: `{verdict}`",
        "",
        "## Top Ranked Raw Sources",
    ]

    for r in ranked[:20]:
        lines.append(f"### `{r['file']}`")
        lines.append(f"- score: `{r['score']}`")
        lines.append(f"- classification: `{r['classification']}`")
        lines.append(f"- known order id hits: `{r['known_order_id_hits']}`")
        for node in r["best_nodes"][:3]:
            lines.append(f"- node `{node['path']}` score=`{node['score']}` fill_keys=`{','.join(node['fill_keys'])}` contract_keys=`{','.join(node['contract_keys'])}` order_hits=`{node['known_order_id_hits']}`")
            lines.append("```json")
            lines.append(json.dumps(node["sample"], indent=2)[:2000])
            lines.append("```")
        lines.append("")

    lines.extend(["## Next"])
    if verdict == "RAW_FILL_LEVEL_CONTRACT_SOURCE_FOUND":
        lines.append("- Patch fill enrichment using the top raw source.")
    elif verdict == "POSSIBLE_RAW_CONTRACT_SOURCE_FOUND":
        lines.append("- Inspect top source manually; patch only if order_id or fill timestamp can join.")
    elif verdict == "RAW_FILL_SOURCE_FOUND_BUT_NO_CONTRACT":
        lines.append("- Current raw fills lack contract identity; export richer Robinhood options order data.")
    else:
        lines.append("- Proceed with fill-level behavior replay; exact option P/L remains blocked.")

    OUT_MD.write_text("\n".join(lines) + "\n", encoding="utf-8")

    print("ROBINHOOD_CONTRACT_IDENTITY_RAW_SOURCE_RANK=PASS")
    print(f"KNOWN_ORDER_IDS={report['known_order_ids']}")
    print(f"FILES_SCANNED_AFTER_EXCLUSIONS={report['files_scanned_after_exclusions']}")
    print(f"RANKED_FILES={report['ranked_files']}")
    print(f"VERDICT={verdict}")
    print(f"REPORT_JSON={rel(OUT_JSON)}")
    print(f"REPORT_MD={rel(OUT_MD)}")

if __name__ == "__main__":
    main()
