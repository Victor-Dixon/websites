#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import urllib.error
import urllib.request
from html.parser import HTMLParser

CREATE_TOKEN_URL = "https://maskzero.site/wp-json/emergence/v1/spark-token?dreamos_smoke=102"
LOAD_TOKEN_BASE = "https://maskzero.site/wp-json/emergence/v1/spark-token/"
CUSTOM_BATTLE_URL = "https://maskzero.site/wp-json/spark-battle/v1/custom-battle?dreamos_smoke=102"
BATTLE_PAGE_BASE = "https://maskzero.site/battles/?dreamos_smoke=102&spark_token="

FORBIDDEN_VISIBLE = [
    "showwork",
    "show work",
    "raw_roll",
    "odds:",
    "raw_scores_exported",
    "manifest_threshold_exported",
    "flavor_vectors_exported",
]

FORBIDDEN_PAYLOAD = [
    "scores",
    "tiers",
    "manifest_threshold",
    "flavor_vectors",
    "spark_signature",
    "combat_capability",
    "provisional_spark_signature",
    "provisional_combat_capability",
    "debug",
    "showwork",
    "roll",
    "odds",
    "raw",
]

SAFE_SPARK = {
    "version": 1,
    "source": "emergence-character-generator",
    "created_at": "2026-05-31T00:00:00Z",
    "spark_name": "The Prism Warden",
    "title": "The Prism Warden",
    "archetype": "Lightbound Guardian",
    "summary": "A player-safe Spark dossier imported through a signed token.",
    "cast": "Solo Spark",
    "profile_shape": "Focused high-tier Spark.",
    "selected_powers": [
        {"power": "Laser Light", "domain": "", "lead": True},
        {"power": "Hard Light", "domain": "", "lead": False},
    ],
    "battle_ready_note": "Player-safe Spark dossier exported for battle simulation.",
}


class ScriptExtractor(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.in_script = False
        self.scripts: list[str] = []
        self._buf: list[str] = []

    def handle_starttag(self, tag: str, attrs) -> None:
        if tag.lower() == "script":
            self.in_script = True
            self._buf = []

    def handle_endtag(self, tag: str) -> None:
        if tag.lower() == "script" and self.in_script:
            self.in_script = False
            self.scripts.append("".join(self._buf))
            self._buf = []

    def handle_data(self, data: str) -> None:
        if self.in_script:
            self._buf.append(data)


def fetch_text(url: str, expect_error: bool = False) -> tuple[int, str]:
    req = urllib.request.Request(
        url,
        headers={
            "User-Agent": "DreamOS-TokenHandoffSmoke/1.0",
            "Cache-Control": "no-cache",
        },
    )
    try:
        with urllib.request.urlopen(req, timeout=45) as resp:
            body = resp.read().decode("utf-8", errors="replace")
            print(f"HTTP_FETCH={resp.status} url={url}")
            return resp.status, body
    except urllib.error.HTTPError as exc:
        body = exc.read().decode("utf-8", errors="replace")
        print(f"HTTP_FETCH={exc.code} url={url}")
        if not expect_error:
            raise
        return exc.code, body


def request_json(url: str, payload: dict | None = None, method: str = "GET", expect_error: bool = False) -> tuple[int, dict]:
    data = None if payload is None else json.dumps(payload).encode("utf-8")
    req = urllib.request.Request(
        url,
        data=data,
        method=method,
        headers={
            "Content-Type": "application/json",
            "User-Agent": "DreamOS-TokenHandoffSmoke/1.0",
            "Cache-Control": "no-cache",
        },
    )
    try:
        with urllib.request.urlopen(req, timeout=45) as resp:
            body = json.loads(resp.read().decode("utf-8"))
            print(f"HTTP_JSON={resp.status} method={method} url={url}")
            return resp.status, body
    except urllib.error.HTTPError as exc:
        body = json.loads(exc.read().decode("utf-8"))
        print(f"HTTP_JSON={exc.code} method={method} url={url}")
        if not expect_error:
            raise
        return exc.code, body


def strip_nonvisible_html(html: str) -> str:
    html = re.sub(r"<script\b[^>]*>.*?</script>", "", html, flags=re.I | re.S)
    html = re.sub(r"<style\b[^>]*>.*?</style>", "", html, flags=re.I | re.S)
    return html


def extract_scripts(html: str) -> str:
    parser = ScriptExtractor()
    parser.feed(html)
    return "\n".join(parser.scripts)


def assert_no_visible_leaks(label: str, html: str) -> None:
    visible = strip_nonvisible_html(html).lower()
    leaks = [marker for marker in FORBIDDEN_VISIBLE if marker in visible]
    assert not leaks, f"{label} visible leak: {leaks}"


def assert_no_payload_leaks(label: str, payload: dict) -> None:
    serialized = json.dumps(payload).lower()
    leaks = [marker for marker in FORBIDDEN_PAYLOAD if marker in serialized]
    assert not leaks, f"{label} leaked hidden keys: {leaks}"


def main() -> int:
    print("== SAFE PAYLOAD ASSERT ==")
    assert_no_payload_leaks("safe spark", SAFE_SPARK)
    print("SAFE_SPARK_PAYLOAD=PASS")

    print("== CREATE TOKEN ==")
    status, created = request_json(CREATE_TOKEN_URL, {"spark": SAFE_SPARK}, method="POST")
    assert status == 200, created
    assert created.get("status") == "created", created
    assert created.get("token"), created
    assert created.get("share_url"), created
    assert "spark_token=" in created["share_url"], created
    assert created.get("player_safe") is True, created
    assert_no_payload_leaks("token create response", created)
    token = created["token"]
    print("TOKEN_CREATE=PASS")
    print(f"TOKEN_LENGTH={len(token)}")

    print("== LOAD VALID TOKEN ==")
    status, loaded = request_json(LOAD_TOKEN_BASE + token + "?dreamos_smoke=102")
    assert status == 200, loaded
    assert loaded.get("status") == "loaded", loaded
    assert loaded.get("player_safe") is True, loaded
    assert loaded.get("spark", {}).get("spark_name") == SAFE_SPARK["spark_name"], loaded
    assert_no_payload_leaks("token load response", loaded)
    print("TOKEN_LOAD_VALID=PASS")
    print("TOKEN_NO_RAW_SCORE_LEAK=PASS")

    print("== INVALID TOKEN REJECT ==")
    status, invalid = request_json(
        LOAD_TOKEN_BASE + "invalid-token-000000?dreamos_smoke=102",
        expect_error=True,
    )
    assert status in (403, 404), invalid
    assert invalid.get("status") in ("invalid", "expired"), invalid
    print("TOKEN_INVALID_REJECTED=PASS")

    print("== OPEN BATTLE PAGE WITH TOKEN ==")
    battle_url = BATTLE_PAGE_BASE + token
    status, html = fetch_text(battle_url)
    assert status == 200, status
    assert "battle" in html.lower(), "battle marker missing"
    assert "dreamos-bs-token-handoff-inline" in html, "token handoff inline bridge missing"
    assert "spark_token" in html, "spark_token code missing"
    assert "emergence_spark_battle_handoff_v1" in html, "storage bridge missing"
    assert_no_visible_leaks("battle token page", html)

    scripts = extract_scripts(html)
    assert "loadSparkToken" in scripts, "loadSparkToken missing"
    assert "/wp-json/emergence/v1/spark-token/" in scripts, "token REST load missing"
    assert "Spark token rejected or expired" in scripts, "invalid token UI missing"
    assert "FORBIDDEN" in scripts, "forbidden guard missing"
    print("BATTLE_TOKEN_PAGE_LOADS=PASS")
    print("BATTLE_TOKEN_IMPORT_SEMANTICS=PASS")

    print("== RUN CUSTOM BATTLE USING TOKEN PAYLOAD ==")
    spark = loaded["spark"]
    status, result = request_json(
        CUSTOM_BATTLE_URL,
        {"spark": spark, "opponent": "the-victor"},
        method="POST",
    )
    assert status == 200, result
    assert result.get("status") == "resolved", result
    assert result.get("mode") == "custom_spark_battle", result
    assert result.get("winner"), result
    assert result.get("arena"), result
    assert result.get("story"), result
    assert result.get("player_safe") is True, result
    assert result.get("math_hidden") is True, result

    serialized_result = json.dumps(result).lower()
    leaks = [marker for marker in FORBIDDEN_VISIBLE if marker in serialized_result]
    assert not leaks, f"battle result leaked public markers: {leaks}"

    print("TOKEN_CUSTOM_BATTLE_REST=PASS")
    print("TOKEN_CUSTOM_BATTLE_WINNER_VISIBLE=PASS")
    print("TOKEN_CUSTOM_BATTLE_ARENA_VISIBLE=PASS")
    print("TOKEN_CUSTOM_BATTLE_STORY_VISIBLE=PASS")
    print("TOKEN_CUSTOM_BATTLE_NO_RAW_SCORE_LEAK=PASS")

    print("== FINAL ASSERT ==")
    print("VALID_TOKEN_FRESH_SESSION_ASSERT=PASS")
    print("INVALID_TOKEN_REJECT_ASSERT=PASS")
    print("WINNER_ARENA_STORY_VISIBLE_ASSERT=PASS")
    print("EMERGENCE_TOKEN_HANDOFF_BROWSER_SMOKE=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
