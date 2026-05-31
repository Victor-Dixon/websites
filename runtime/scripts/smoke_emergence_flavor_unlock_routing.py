#!/usr/bin/env python3
from __future__ import annotations

import json
import itertools
import urllib.request
from pathlib import Path


REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
PAGE_URL = "https://dadudekc.site/character-generator/"
JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
DOMAIN_KEY_PATH = Path("runtime/plugins/emergence-character-generator/assets/spark-protocol-v85-domain-key.json")

DOMAINS = ["Titan", "Velocity", "Energy", "Specter", "Duality", "Omni", "Primal", "Mind"]
LETTERS = list("ABCDEFGH")


def fetch(url: str) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"})
    with urllib.request.urlopen(req, timeout=25) as resp:
        return resp.read().decode("utf-8", errors="replace")


def post(payload: dict) -> dict:
    req = urllib.request.Request(
        REST_URL + "?dreamos_smoke=088c",
        data=json.dumps(payload).encode("utf-8"),
        method="POST",
        headers={"Content-Type": "application/json", "User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"},
    )
    with urllib.request.urlopen(req, timeout=25) as resp:
        return json.loads(resp.read().decode("utf-8", errors="replace"))


def require(cond: bool, msg: str) -> None:
    if not cond:
        raise AssertionError(msg)


def normalize_entry(entry):
    """
    Accepts common formats:
      ["Titan", 2]
      {"domain":"Titan","points":2}
      {"trait":"Titan","score":2}
    """
    if isinstance(entry, list) and len(entry) >= 2:
        return str(entry[0]), int(entry[1])

    if isinstance(entry, dict):
        domain = entry.get("domain") or entry.get("trait") or entry.get("key") or entry.get("name")
        points = entry.get("points", entry.get("score", entry.get("weight", entry.get("value", 1))))
        if domain:
            return str(domain), int(points)

    raise ValueError(f"unsupported entry: {entry!r}")


def unwrap_domain_key(raw):
    """
    Returns shape:
      {"1": {"A": ("Titan", 2), ...}, ...}
    """

    candidates = []

    if isinstance(raw, dict):
        candidates.append(raw)
        for key in [
            "domain_key",
            "domain_keys",
            "domain_questions",
            "question_key",
            "questions",
            "scoring_key",
            "answer_key",
            "Q1_Q28_domain_key",
        ]:
            if key in raw:
                candidates.append(raw[key])

    for candidate in candidates:
        # Dict keyed by question number.
        if isinstance(candidate, dict) and all(str(i) in candidate for i in range(1, 29)):
            return {
                str(q): {letter: normalize_entry(candidate[str(q)][letter]) for letter in LETTERS if letter in candidate[str(q)]}
                for q in range(1, 29)
            }

        # List of question rows.
        if isinstance(candidate, list):
            out = {}
            for row in candidate:
                if not isinstance(row, dict):
                    continue
                q = row.get("q") or row.get("question") or row.get("id")
                options = row.get("options") or row.get("answers") or row.get("key")
                if q is None or not isinstance(options, dict):
                    continue
                out[str(int(q))] = {
                    letter: normalize_entry(options[letter])
                    for letter in LETTERS
                    if letter in options
                }
            if all(str(i) in out for i in range(1, 29)):
                return out

    raise ValueError("Could not normalize domain key schema")


def load_domain_key() -> dict:
    raw = json.loads(DOMAIN_KEY_PATH.read_text())
    key = unwrap_domain_key(raw)

    for q in range(1, 29):
        qkey = str(q)
        require(qkey in key, f"missing q{q}")
        require(set(key[qkey].keys()) == set(LETTERS), f"q{q} missing letters: {key[qkey].keys()}")

    print("DOMAIN_KEY_NORMALIZED=PASS")
    return key


def score_answers(domain_key: dict, answers: list[str]) -> dict[str, int]:
    scores = {d: 0 for d in DOMAINS}
    for q, letter in enumerate(answers, start=1):
        domain, points = domain_key[str(q)][letter]
        scores[domain] += points
    return scores


def best_answer_for(domain_key: dict, q: int, target: str) -> str | None:
    hits = []
    for letter, (domain, points) in domain_key[str(q)].items():
        if domain == target:
            hits.append((points, letter))
    if not hits:
        return None
    return sorted(hits, reverse=True)[0][1]


def single_domain_fixture(domain_key: dict, target: str) -> list[str]:
    answers = []
    for q in range(1, 29):
        letter = best_answer_for(domain_key, q, target)
        if letter is None:
            # Choose least damaging answer.
            options = sorted(domain_key[str(q)].items(), key=lambda kv: kv[1][1])
            letter = options[0][0]
        answers.append(letter)
    return answers


def coprimary_fixture_search(domain_key: dict, a: str, b: str) -> list[str]:
    """
    Beam search for an answer vector where:
      - a and b both cross threshold remotely
      - a and b tie by tier remotely
    Local scoring is used only to propose candidates.
    """
    beam = [([], {d: 0 for d in DOMAINS})]

    for q in range(1, 29):
        next_beam = []
        for answers, scores in beam:
            for letter, (domain, points) in domain_key[str(q)].items():
                ns = scores.copy()
                ns[domain] += points
                na = answers + [letter]

                # Favor target totals, balance, and suppress off-target leaders.
                target_total = ns[a] + ns[b]
                target_gap = abs(ns[a] - ns[b])
                off_max = max(ns[d] for d in DOMAINS if d not in (a, b))
                target_min = min(ns[a], ns[b])
                value = (target_min * 10) + target_total - (target_gap * 4) - (off_max * 3)

                next_beam.append((value, na, ns))

        next_beam.sort(key=lambda x: x[0], reverse=True)
        beam = [(na, ns) for _, na, ns in next_beam[:500]]

    # Try best local candidates remotely; remote is source of truth.
    for answers, local_scores in beam[:120]:
        payload = post({"answers": answers})
        scores = payload.get("scores", {})
        tiers = payload.get("tiers", {})
        manifested = payload.get("manifested", [])
        threshold = float(payload.get("manifest_threshold", 0))

        if (
            a in manifested
            and b in manifested
            and float(scores.get(a, 0)) >= threshold
            and float(scores.get(b, 0)) >= threshold
            and tiers.get(a) == tiers.get(b)
            and payload.get("powers") == []
        ):
            print(f"COPRIMARY_FIXTURE_FOUND_{a}_{b}=PASS local={local_scores} remote_scores={scores} tiers={tiers}")
            return answers

    raise AssertionError(f"no co-primary fixture found for {a}/{b}")


def assert_rest_domain_path(label: str, answers: list[str], expected_contains: list[str]) -> dict:
    payload = post({"answers": answers})
    require(payload.get("phase") == "domain_typing", f"{label}: bad phase {payload.get('phase')}")
    require(payload.get("powers") == [], f"{label}: powers leaked before flavor")
    for domain in expected_contains:
        require(domain in payload.get("manifested", []), f"{label}: missing {domain}; got {payload.get('manifested')}")
    print(f"{label}=PASS manifested={','.join(payload['manifested'])} scores={payload['scores']} tiers={payload.get('tiers')}")
    return payload


def main() -> int:
    domain_key = load_domain_key()

    print("== PUBLIC PRIVACY STATIC CHECK ==")
    html = fetch(PAGE_URL + "?dreamos_smoke=088c")
    js = fetch(JS_URL + "?dreamos_smoke=088c")

    require('id="emergence-cg-flavor"' in html, "missing flavor mount")
    require("Unlocked Flavor Block" in js, "missing generic flavor block label")
    require("domains stay hidden" in js, "missing hidden-domain copy")

    forbidden = [
        "Unlocked domains:",
        "Manifested Domains",
        "Only your manifested domains appear here",
        "Titan Flavor Block",
        "Velocity Flavor Block",
        "Energy Flavor Block",
        "Specter Flavor Block",
        "Duality Flavor Block",
        "Omni Flavor Block",
        "Primal Flavor Block",
        "Mind Flavor Block",
    ]

    for marker in forbidden:
        require(marker not in html, f"HTML leaks routing: {marker}")
        require(marker not in js, f"JS leaks routing: {marker}")

    print("PUBLIC_DOMAIN_ROUTING_PRIVACY=PASS")

    print("== SINGLE DOMAIN FIXTURES ==")
    for domain in DOMAINS:
        answers = single_domain_fixture(domain_key, domain)
        assert_rest_domain_path(f"DOMAIN_FIXTURE_{domain}", answers, [domain])

    print("== CO-PRIMARY FIXTURES ==")
    pairs = [
        ("Titan", "Velocity"),
        ("Energy", "Specter"),
        ("Duality", "Mind"),
        ("Omni", "Primal"),
    ]

    for a, b in pairs:
        answers = coprimary_fixture_search(domain_key, a, b)
        payload = assert_rest_domain_path(f"COPRIMARY_FIXTURE_{a}_{b}", answers, [a, b])
        require(payload["tiers"][a] == payload["tiers"][b], f"{a}/{b}: tier mismatch")
        print(f"COPRIMARY_ASSERT_{a}_{b}=PASS")

    require("Unlocked Flavor Block ' + esc(String(index + 1))" in js, "JS does not use generic flavor block numbering")
    require("esc(domain) + ' Flavor Block" not in js, "JS still names domain flavor block")
    print("FLAVOR_BLOCK_NAMES_HIDDEN=PASS")

    print("EMERGENCE_FLAVOR_PRIVACY_UNLOCK_ROUTING=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
