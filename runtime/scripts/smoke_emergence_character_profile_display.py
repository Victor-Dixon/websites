#!/usr/bin/env python3
from __future__ import annotations

import json
import urllib.request

PAGE_URL = "https://maskzero.site/character-generator/"
JS_URL = "https://maskzero.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
CSS_URL = "https://maskzero.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
REST_URL = "https://maskzero.site/wp-json/emergence/v1/generate"

ALL_H = ["H"] * 28
DUALITY_FLAVOR = {"49": "A", "50": "A", "51": "A", "52": "A", "53": "A"}

def fetch(url: str) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"})
    with urllib.request.urlopen(req, timeout=25) as resp:
        return resp.read().decode("utf-8", errors="replace")

def post(payload: dict) -> dict:
    req = urllib.request.Request(
        REST_URL + "?dreamos_smoke=089b",
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
    print("== PUBLIC PAGE STRUCTURE CHECK ==")
    html = fetch(PAGE_URL + "?dreamos_smoke=089b")
    require('id="emergence-cg-form"' in html, "missing generator form")
    require('id="emergence-cg-result"' in html, "missing result mount")
    require('id="emergence-cg-flavor"' in html, "missing flavor mount")
    print("PUBLIC_PAGE_STRUCTURE=PASS")

    print("== PROFILE ASSET CHECK ==")
    js = fetch(JS_URL + "?dreamos_smoke=089b")
    css = fetch(CSS_URL + "?dreamos_smoke=089b")

    require("Spark Profile Generated" in js, "JS missing Spark Profile Generated")
    require("Manifested Abilities" in js, "JS missing Manifested Abilities")
    require("Story Hook" in js, "JS missing Story Hook")
    require("Use this Spark in Battle Simulator" in js, "JS missing Battle Simulator CTA")
    require("Generate Another Spark" in js, "JS missing regenerate action")

    require(".ecg-profile-card" in css, "CSS missing profile card")
    require(".ecg-profile-hero" in css, "CSS missing profile hero")
    require(".ecg-power-list" in css, "CSS missing power list")
    require(".ecg-profile-cta" in css, "CSS missing CTA")
    print("PUBLIC_PROFILE_ASSETS=PASS")

    print("== REST FINAL PAYLOAD CHECK ==")
    domain = post({"answers": ALL_H})
    require(domain["phase"] == "domain_typing", "domain phase failed")
    require(domain["powers"] == [], "domain pass leaked powers")

    final = post({"answers": ALL_H, "flavor_answers": DUALITY_FLAVOR})
    require(final["phase"] == "flavor_power_selection", "flavor phase failed")
    require("character_sheet" in final, "missing character_sheet")
    sheet = final["character_sheet"]
    require(sheet.get("title"), "missing sheet title")
    require(sheet.get("summary"), "missing sheet summary")
    require(sheet.get("signature_line"), "missing signature line")
    require(final.get("powers"), "missing selected powers")
    print("REST_CHARACTER_PROFILE_PAYLOAD=PASS")

    print("== PUBLIC PRIVACY CHECK ==")
    forbidden = ["Domain Scores", "Manifest threshold", "Titan +2", "Duality +2", "Mind +2"]
    for marker in forbidden:
        require(marker not in html, f"HTML leak: {marker}")
        require(marker not in js, f"JS leak: {marker}")
    print("PROFILE_PUBLIC_PRIVACY=PASS")

    print("EMERGENCE_CHARACTER_PROFILE_DISPLAY=PASS")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
