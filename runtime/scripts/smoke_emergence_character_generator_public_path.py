#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import sys
import urllib.request
from pathlib import Path


SITE_URL = "https://dadudekc.site/character-generator/"
REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"
JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"


ALL_H = ["H"] * 28
DUALITY_FLAVOR = {"49": "A", "50": "A", "51": "A", "52": "A", "53": "A"}


def fetch_text(url: str, timeout: int = 25) -> str:
    req = urllib.request.Request(
        url,
        headers={
            "User-Agent": "DreamOS-Smoke/1.0",
            "Cache-Control": "no-cache",
        },
    )
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        return resp.read().decode("utf-8", errors="replace")


def post_json(url: str, payload: dict, timeout: int = 25) -> dict:
    data = json.dumps(payload).encode("utf-8")
    req = urllib.request.Request(
        url,
        data=data,
        method="POST",
        headers={
            "Content-Type": "application/json",
            "User-Agent": "DreamOS-Smoke/1.0",
            "Cache-Control": "no-cache",
        },
    )
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        return json.loads(resp.read().decode("utf-8", errors="replace"))


def require(condition: bool, message: str) -> None:
    if not condition:
        raise AssertionError(message)


def main() -> int:
    print("== PUBLIC PAGE FETCH ==")
    html = fetch_text(SITE_URL + "?dreamos_smoke=087")
    require('id="emergence-cg-form"' in html, "missing domain scan form")
    require('id="emergence-cg-result"' in html, "missing result mount")
    require('id="emergence-cg-flavor"' in html, "missing flavor mount")
    require("When something genuinely good finally happens to you" in html, "missing real Protocol v8.5 Q1 text")
    require("I start organizing it" in html, "missing real Protocol v8.5 answer text")
    print("PUBLIC_PAGE_STRUCTURE=PASS")

    print("== PUBLIC JS FETCH ==")
    js = fetch_text(JS_URL + "?dreamos_smoke=087")
    require("Pass 2 Unlocked: Flavor Power Selection" in js, "missing Pass 2 render string")
    require("Generate Character Sheet" in js, "missing final submit string")
    require("flavorMount.scrollIntoView" in js, "missing pass 2 scroll")
    require("question_bank" in html or "questionBank" in js, "missing question bank bridge")
    print("PUBLIC_JS_PHASE_PATH=PASS")

    print("== DOMAIN PASS REST ==")
    domain = post_json(REST_URL + "?dreamos_smoke=087_domain", {"answers": ALL_H})
    require(domain.get("phase") == "domain_typing", f"bad domain phase: {domain.get('phase')}")
    require(domain.get("powers") == [], "domain pass leaked powers")
    require(domain.get("manifested") == ["Duality"], f"unexpected manifested domains: {domain.get('manifested')}")
    require(domain.get("power_selection_status") == "locked_until_flavor_pass", "domain pass did not lock powers")
    print("DOMAIN_PASS_REST=PASS")

    print("== SIMULATED BROWSER PHASE TRANSITION ASSERT ==")
    manifested = domain.get("manifested") or []
    require("Duality" in manifested, "Duality did not manifest for all-H fixture")
    require("49" in DUALITY_FLAVOR and "53" in DUALITY_FLAVOR, "missing Duality flavor fixture")
    require("Pass 2 Unlocked: Flavor Power Selection" in js, "browser would not have visible Pass 2 header")
    require("Only your manifested domains appear here" in js, "browser would not explain manifested-domain-only flavor")
    print("VISIBLE_PASS_2_ASSERT=PASS")

    print("== FLAVOR PASS REST ==")
    flavor = post_json(
        REST_URL + "?dreamos_smoke=087_flavor",
        {"answers": ALL_H, "flavor_answers": DUALITY_FLAVOR},
    )
    require(flavor.get("phase") == "flavor_power_selection", f"bad flavor phase: {flavor.get('phase')}")
    require(flavor.get("powers"), "flavor pass did not select powers")
    require("character_sheet" in flavor, "missing character_sheet")
    sheet = flavor["character_sheet"]
    require(sheet.get("title"), "missing character sheet title")
    require(sheet.get("summary"), "missing character sheet summary")
    require("Spark Signature" in sheet.get("signature_line", ""), "missing signature line")
    for power in flavor.get("powers", []):
        require(power.get("domain") == "Duality", f"non-Duality power leaked: {power}")
    print("FLAVOR_PASS_REST=PASS")

    print("== SIMULATED FINAL PROFILE ASSERT ==")
    require("Spark Profile" in js, "browser would not render Spark Profile heading")
    require("Selected Powers" in js, "browser would not render selected powers section")
    require("Battle Readiness" in js, "browser would not render battle readiness section")
    print("VISIBLE_SPARK_PROFILE_ASSERT=PASS")

    print("== PUBLIC PRIVACY ASSERT ==")
    forbidden_public = [
        "Domain Scores",
        "Manifest threshold",
        "Titan +2",
        "Duality +2",
        "Mind +2",
    ]
    for marker in forbidden_public:
        require(marker not in html, f"public HTML leaked marker: {marker}")
        require(marker not in js, f"public JS leaked marker: {marker}")
    print("PUBLIC_PRIVACY_ASSERT=PASS")

    print("== SUMMARY ==")
    print("EMERGENCE_CG_PUBLIC_PATH_SMOKE=PASS")
    print("URL=" + SITE_URL)
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except Exception as exc:
        print("EMERGENCE_CG_PUBLIC_PATH_SMOKE=FAIL")
        print(f"ERROR={exc}")
        raise
