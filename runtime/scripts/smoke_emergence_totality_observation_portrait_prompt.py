#!/usr/bin/env python3
from __future__ import annotations

import json
import urllib.request

PAGE_URL = "https://dadudekc.site/character-generator/"
JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"

ALL_H = ["H"] * 28
DUALITY_FLAVOR = {"49": "A", "50": "A", "51": "A", "52": "A", "53": "A"}

def fetch(url: str) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"})
    with urllib.request.urlopen(req, timeout=25) as resp:
        return resp.read().decode("utf-8", errors="replace")

def post(payload: dict) -> dict:
    req = urllib.request.Request(
        REST_URL + "?dreamos_smoke=091",
        data=json.dumps(payload).encode("utf-8"),
        method="POST",
        headers={"Content-Type": "application/json", "User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"},
    )
    with urllib.request.urlopen(req, timeout=25) as resp:
        return json.loads(resp.read().decode("utf-8", errors="replace"))

def require(cond: bool, msg: str) -> None:
    if not cond:
        raise AssertionError(msg)

def main() -> int:
    print("== PUBLIC TOTALITY ASSET CHECK ==")
    html = fetch(PAGE_URL + "?dreamos_smoke=091")
    js = fetch(JS_URL + "?dreamos_smoke=091")
    css = fetch(CSS_URL + "?dreamos_smoke=091")

    require("Totality Observation" in html, "page copy missing Totality Observation")
    require("Totality Observation" in js, "JS missing Totality Observation")
    require("Name the Spark" in js, "JS missing name step")
    require("Create Final Dossier" in js, "JS missing dossier gate")
    require("premium American superhero comic-book character art" in js, "JS missing premium prompt compiler")
    require("ecg-premium-prompt" in js, "JS missing prompt output")
    require(".ecg-totality-form" in css, "CSS missing totality form")
    require(".ecg-premium-prompt" in css, "CSS missing prompt textarea")
    print("PUBLIC_TOTALITY_ASSETS=PASS")

    print("== REST FINAL PAYLOAD STILL WORKS ==")
    domain = post({"answers": ALL_H})
    require(domain["phase"] == "domain_typing", "domain phase failed")
    require(domain["powers"] == [], "domain pass leaked powers")

    final = post({"answers": ALL_H, "flavor_answers": DUALITY_FLAVOR})
    require(final["phase"] == "flavor_power_selection", "flavor phase failed")
    require("character_sheet" in final, "missing character_sheet")
    require(final.get("powers"), "missing powers")
    print("REST_FINAL_PAYLOAD=PASS")

    print("== PROMPT PRIVACY STATIC CHECK ==")
    forbidden = [
      "Marvel",
      "DC Comics",
      "Domain Scores",
      "Manifest threshold",
      "Titan +2",
      "Duality +2",
      "Mind +2",
      "debug summaries",
    ]
    for marker in forbidden:
      require(marker not in html, f"HTML leak: {marker}")
      require(marker not in js, f"JS leak: {marker}")
    print("PROMPT_PRIVACY_STATIC=PASS")

    print("== FLOW STATIC ASSERT ==")
    require("renderTotalityObservation(finalPayload)" in js, "flavor submit does not route to Totality Observation")
    require("renderCharacterProfile(namedPayload)" in js, "name submit does not route to final dossier")
    require("spark_name" in js, "named payload missing spark_name")
    require("premium_portrait_prompt" in js, "named payload missing premium prompt")
    require("deterministic-svg-preview" in js, "SVG preview marker missing")
    print("TOTALITY_FLOW_STATIC=PASS")

    print("EMERGENCE_TOTALITY_OBSERVATION_PORTRAIT_PROMPT=PASS")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
