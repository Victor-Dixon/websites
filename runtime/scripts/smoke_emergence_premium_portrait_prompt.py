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
        REST_URL + "?dreamos_smoke=092",
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
    print("== PUBLIC PLAYER DESIGN ASSETS ==")
    html = fetch(PAGE_URL + "?dreamos_smoke=092")
    js = fetch(JS_URL + "?dreamos_smoke=092")
    css = fetch(CSS_URL + "?dreamos_smoke=092")

    require("build type" in html.lower(), "page copy missing build type")
    require("costume" in html.lower(), "page copy missing costume")
    require("personality" in html.lower(), "page copy missing personality")
    require("ability showcase" in html.lower(), "page copy missing ability showcase")

    require("POWERS TO VISUALLY SHOWCASE" in js, "prompt missing power showcase section")
    require("ABILITY VISUALIZATION" in js, "prompt missing ability visualization")
    require("PLAYER DESIGN DIRECTION" in js, "prompt missing player design direction")
    require("compileVisualMotifs" in js, "missing visual motif compiler")
    require("compilePlayerDesignDirection" in js, "missing player design compiler")

    require("emergence-build-style" in js, "missing build control")
    require("emergence-costume-style" in js, "missing costume control")
    require("emergence-personality-style" in js, "missing personality control")
    require("emergence-showcase-style" in js, "missing ability showcase control")
    require(".ecg-cosmetic-grid" in css, "missing cosmetic grid CSS")
    print("PUBLIC_PLAYER_DESIGN_ASSETS=PASS")

    print("== REST STILL WORKS ==")
    domain = post({"answers": ALL_H})
    require(domain["phase"] == "domain_typing", "domain phase failed")
    require(domain["powers"] == [], "domain pass leaked powers")

    final = post({"answers": ALL_H, "flavor_answers": DUALITY_FLAVOR})
    require(final["phase"] == "flavor_power_selection", "flavor phase failed")
    require(final.get("powers"), "missing powers")
    require("character_sheet" in final, "missing character sheet")
    print("REST_STILL_WORKS=PASS")

    print("== PRIVACY CHECK ==")
    hard_forbidden = ["Domain Scores", "Manifest threshold", "Titan +2", "Duality +2", "Mind +2"]
    for marker in hard_forbidden:
        require(marker not in html, f"HTML leak: {marker}")
        require(marker not in js, f"JS leak: {marker}")

    require("Marvel, DC, Spider-Man, Batman, Superman, X-Men, Avengers, Justice League" in js, "negative prompt missing franchise exclusions")
    print("PLAYER_DESIGN_PROMPT_PRIVACY=PASS")

    print("EMERGENCE_PREMIUM_PORTRAIT_PROMPT_COMPILER=PASS")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
