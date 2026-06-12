#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import urllib.request
from pathlib import Path

PAGE_URL = "https://maskzero.site/character-generator/"
JS_URL = "https://maskzero.site/wp-content/plugins/emergence-character-generator/assets/emergence-cg.js"
REST_URL = "https://maskzero.site/wp-json/emergence/v1/portrait"
PLUGIN_SOURCE = Path("runtime/plugins/emergence-character-generator/emergence-character-generator.php")

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
]

def fetch(url: str) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "DreamOS-Smoke/1.0", "Cache-Control": "no-cache"})
    with urllib.request.urlopen(req, timeout=25) as resp:
        return resp.read().decode("utf-8", errors="replace")

def post(url: str, payload: dict) -> dict:
    req = urllib.request.Request(
        url + "?dreamos_smoke=094d",
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
    print("== SOURCE PROVIDER SHAPE CHECK ==")
    source = PLUGIN_SOURCE.read_text(encoding="utf-8")
    require("DREAMOS_OPENAI_IMAGE_PROVIDER_V2_BEGIN" in source, "missing v2 provider marker")
    require("function emergence_cg_v2_call_openai_image_provider" in source, "missing v2 openai function")
    require("function emergence_cg_openai_premium_portrait_rest_v2" in source, "missing v2 REST callback")
    require("https://api.openai.com/v1/images/generations" in source, "missing OpenAI endpoint")
    require("EMERGENCE_IMAGE_LIVE" in source, "missing live gate")
    require("wp_remote_post" in source, "missing server-side POST")
    require("b64_json" in source, "missing b64 image handling")
    print("SOURCE_OPENAI_PROVIDER=PASS")

    print("== PUBLIC ASSETS CHECK ==")
    html = fetch(PAGE_URL + "?dreamos_smoke=094d")
    js = fetch(JS_URL + "?dreamos_smoke=094d")
    require("live-gated OpenAI image provider" in html, "page copy missing live-gated provider language")
    require("requestPremiumHeroImage" in js, "JS missing provider request")
    require("/wp-json/emergence/v1/portrait" in js, "JS missing provider endpoint")
    print("PUBLIC_PROVIDER_ASSETS=PASS")

    print("== DISABLED/PROMPT FALLBACK CHECK ==")
    response = post(REST_URL, {
        "spark_name": "The Prism Warden",
        "premium_portrait_prompt": PROMPT,
    })

    require(response.get("status") in ("disabled", "provider_error", "generated"), f"unexpected status: {response}")

    if response.get("status") == "generated":
        require(response.get("image_url"), "generated response missing image_url")
        require(response.get("prompt_only") is False, "generated response should not be prompt_only")
        print("PROVIDER_LIVE_OR_CACHED_GENERATED=PASS")
    else:
        require(response.get("prompt_only") is True, "fallback response should be prompt_only")
        require(response.get("image_url") is None, "fallback image_url should be null")
        print("PROVIDER_DISABLED_OR_SAFE_ERROR_FALLBACK=PASS")

    print("== NO KEY LEAK CHECK ==")
    serialized = json.dumps(response)
    assert_no_secret_shape("response", serialized)
    assert_no_secret_shape("html", html)
    assert_no_secret_shape("js", js)
    require("OPENAI_API_KEY" not in serialized, "response leaked env var name")
    require("EMERGENCE_IMAGE_API_KEY" not in serialized, "response leaked env var name")
    print("PROVIDER_NO_KEY_LEAKS=PASS")

    print("EMERGENCE_OPENAI_PREMIUM_IMAGE_PROVIDER=PASS")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
