#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import urllib.request

PAGE_URL = "https://dadudekc.site/character-generator/"
JS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
CSS_URL = "https://dadudekc.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.css"
REST_URL = "https://dadudekc.site/wp-json/emergence/v1/portrait"

PROMPT = (
    'Create a premium original superhero character portrait for a new hero named "The Prism Warden". '
    'STYLE: premium American superhero comic-book aesthetic, bold inked linework, dramatic cinematic lighting. '
    'POWERS TO VISUALLY SHOWCASE: Laser Light, Hard Light. '
    'DO NOT INCLUDE: text labels, UI elements, stat tables, watermarks, raw scores, domain names.'
)

SECRET_PATTERNS = [
    re.compile(r"sk-[A-Za-z0-9_\-]{20,}"),
    re.compile(r"sk-proj-[A-Za-z0-9_\-]{20,}"),
    re.compile(r"Bearer\s+[A-Za-z0-9_\-.]{20,}", re.I),
    re.compile(r"OPENAI_API_KEY\s*[:=]\s*['\"]?[A-Za-z0-9_\-]{8,}", re.I),
    re.compile(r"EMERGENCE_IMAGE_API_KEY\s*[:=]\s*['\"]?[A-Za-z0-9_\-]{8,}", re.I),
]

def fetch(url: str) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"})
    with urllib.request.urlopen(req, timeout=25) as resp:
        return resp.read().decode("utf-8", errors="replace")

def post(url: str, payload: dict) -> dict:
    req = urllib.request.Request(
        url + "?dreamos_smoke=093b",
        data=json.dumps(payload).encode("utf-8"),
        method="POST",
        headers={"Content-Type": "application/json", "User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"},
    )
    with urllib.request.urlopen(req, timeout=25) as resp:
        return json.loads(resp.read().decode("utf-8", errors="replace"))

def require(cond: bool, msg: str) -> None:
    if not cond:
        raise AssertionError(msg)

def assert_no_secret_shape(label: str, text: str) -> None:
    for pattern in SECRET_PATTERNS:
        match = pattern.search(text)
        if match:
            raise AssertionError(f"{label} leaked secret-shaped token: {match.group(0)[:18]}...")

def main() -> int:
    print("== PUBLIC PROVIDER ASSETS ==")
    html = fetch(PAGE_URL + "?dreamos_smoke=093b")
    js = fetch(JS_URL + "?dreamos_smoke=093b")
    css = fetch(CSS_URL + "?dreamos_smoke=093b")

    require("provider scaffold" in html.lower(), "page copy missing provider scaffold")
    require("requestPremiumHeroImage" in js, "JS missing provider request")
    require("/wp-json/emergence/v1/portrait" in js, "JS missing portrait endpoint")
    require("ecg-generate-premium-image" in js, "JS missing provider button")
    require("ecg-premium-image-provider-result" in js, "JS missing provider result mount")
    require(".ecg-premium-provider-actions" in css, "CSS missing provider actions")
    print("PUBLIC_PROVIDER_ASSETS=PASS")

    print("== REST PROVIDER DISABLED/FALLBACK CHECK ==")
    response = post(REST_URL, {
        "spark_name": "The Prism Warden",
        "premium_portrait_prompt": PROMPT,
    })

    require(response.get("status") in ("disabled", "provider_configured_not_called"), f"unexpected status: {response}")
    require(response.get("prompt_only") is True, "prompt_only fallback not true")
    require(response.get("image_url") is None, "image_url should be null in scaffold")
    require("premium_portrait_prompt" in response, "prompt not returned for fallback")
    require("The Prism Warden" in response.get("premium_portrait_prompt", ""), "prompt fallback missing name")
    print("PROVIDER_PROMPT_ONLY_FALLBACK=PASS")

    print("== KEY LEAK CHECK ==")
    serialized = json.dumps(response)
    assert_no_secret_shape("response", serialized)
    assert_no_secret_shape("html", html)
    assert_no_secret_shape("js", js)

    require("EMERGENCE_IMAGE_API_KEY" not in serialized, "response leaked env var name")
    require("OPENAI_API_KEY" not in serialized, "response leaked fallback env var name")
    print("PROVIDER_NO_KEY_LEAKS=PASS")

    print("EMERGENCE_PREMIUM_HERO_IMAGE_PROVIDER=PASS")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
