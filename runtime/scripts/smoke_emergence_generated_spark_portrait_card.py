#!/usr/bin/env python3
from __future__ import annotations

import json
import urllib.request

PAGE_URL = "https://dadudekc.site/character-generator/"
JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
REST_URL = "https://dadudekc.site/wp-json/emergence/v1/generate"

ALL_H = ["H"] * 28
ALL_G = ["G"] * 28
DUALITY_FLAVOR = {"49": "A", "50": "A", "51": "A", "52": "A", "53": "A"}
MIND_FLAVOR = {"64": "A", "65": "A", "66": "A", "67": "A", "68": "A"}

def fetch(url: str) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"})
    with urllib.request.urlopen(req, timeout=25) as resp:
        return resp.read().decode("utf-8", errors="replace")

def post(payload: dict) -> dict:
    req = urllib.request.Request(
        REST_URL + "?dreamos_smoke=090",
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
    print("== PUBLIC SVG ASSET CHECK ==")
    html = fetch(PAGE_URL + "?dreamos_smoke=090")
    js = fetch(JS_URL + "?dreamos_smoke=090")
    css = fetch(CSS_URL + "?dreamos_smoke=090")

    require("deterministic SVG portrait card" in html, "page copy missing SVG card language")
    require("buildSparkPortraitSvg" in js, "JS missing SVG builder")
    require("ecg-generated-portrait" in js, "JS missing portrait mount")
    require("ecg-spark-svg" in js, "JS missing SVG class")
    require(".ecg-spark-svg" in css, "CSS missing SVG style")
    print("PUBLIC_SVG_ASSETS=PASS")

    print("== REST PROFILE PAYLOAD CHECK ==")
    domain = post({"answers": ALL_H})
    require(domain["phase"] == "domain_typing", "domain phase failed")
    require(domain["powers"] == [], "domain pass leaked powers")

    final_a = post({"answers": ALL_H, "flavor_answers": DUALITY_FLAVOR})
    final_b = post({"answers": ALL_H, "flavor_answers": DUALITY_FLAVOR})
    final_c = post({"answers": ALL_G, "flavor_answers": MIND_FLAVOR})

    require(final_a["phase"] == "flavor_power_selection", "final A phase failed")
    require("character_sheet" in final_a, "final A missing sheet")
    require(final_a.get("powers"), "final A missing powers")

    stable_a = json.dumps({
        "title": final_a["character_sheet"].get("title"),
        "powers": final_a.get("powers"),
        "spark_signature": final_a.get("spark_signature"),
        "combat_capability": final_a.get("combat_capability"),
    }, sort_keys=True)

    stable_b = json.dumps({
        "title": final_b["character_sheet"].get("title"),
        "powers": final_b.get("powers"),
        "spark_signature": final_b.get("spark_signature"),
        "combat_capability": final_b.get("combat_capability"),
    }, sort_keys=True)

    stable_c = json.dumps({
        "title": final_c["character_sheet"].get("title"),
        "powers": final_c.get("powers"),
        "spark_signature": final_c.get("spark_signature"),
        "combat_capability": final_c.get("combat_capability"),
    }, sort_keys=True)

    require(stable_a == stable_b, "same fixture did not produce stable payload basis")
    require(stable_a != stable_c, "different fixture did not produce different payload basis")
    print("DETERMINISTIC_PAYLOAD_BASIS=PASS")

    print("== PUBLIC PRIVACY CHECK ==")
    forbidden = ["Domain Scores", "Manifest threshold", "Titan +2", "Duality +2", "Mind +2", "debugSummary"]
    for marker in forbidden:
        require(marker not in html, f"HTML leak: {marker}")
    for marker in ["Domain Scores", "Manifest threshold", "Titan +2", "Duality +2", "Mind +2"]:
        require(marker not in js, f"JS leak: {marker}")
    print("SVG_PROFILE_PUBLIC_PRIVACY=PASS")

    print("EMERGENCE_GENERATED_SPARK_PORTRAIT_CARD=PASS")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
