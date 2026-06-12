#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import urllib.error
import urllib.request
from html.parser import HTMLParser

SAVE_RECORD_URL = "https://maskzero.site/wp-json/emergence/v1/characters?dreamos_smoke=104"
LOAD_RECORD_BASE = "https://maskzero.site/wp-json/emergence/v1/characters/"
CUSTOM_BATTLE_URL = "https://maskzero.site/wp-json/spark-battle/v1/custom-battle?dreamos_smoke=104"
CHARACTER_PAGE_BASE = "https://maskzero.site/character-generator/?dreamos_smoke=104&character_record="
BATTLE_PAGE_BASE = "https://maskzero.site/battles/?dreamos_smoke=104&character_record="

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

SAFE_CHARACTER = {
    "version": 1,
    "source": "emergence-character-generator",
    "visibility": "private",
    "spark_name": "The Prism Warden",
    "title": "The Prism Warden",
    "archetype": "Lightbound Guardian",
    "summary": "Saved player-safe Spark dossier for browser E2E.",
    "cast": "Solo Spark",
    "profile_shape": "Focused high-tier Spark.",
    "selected_powers": [
        {"power": "Laser Light", "domain": "", "lead": True},
        {"power": "Hard Light", "domain": "", "lead": False},
    ],
    "battle_ready_note": "Saved Spark dossier ready for battle simulation.",
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


def request_json(url: str, payload: dict | None = None, method: str = "GET", expect_error: bool = False) -> tuple[int, dict]:
    data = None if payload is None else json.dumps(payload).encode("utf-8")
    req = urllib.request.Request(
        url,
        data=data,
        method=method,
        headers={
            "Content-Type": "application/json",
            "User-Agent": "DreamOS-SavedCharacterSmoke/1.0",
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


def fetch_text(url: str) -> tuple[int, str]:
    req = urllib.request.Request(
        url,
        headers={
            "User-Agent": "DreamOS-SavedCharacterSmoke/1.0",
            "Cache-Control": "no-cache",
        },
    )
    with urllib.request.urlopen(req, timeout=45) as resp:
        body = resp.read().decode("utf-8", errors="replace")
        print(f"HTTP_FETCH={resp.status} url={url}")
        return resp.status, body


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
    print("== SAFE CHARACTER ASSERT ==")
    assert_no_payload_leaks("safe character", SAFE_CHARACTER)
    print("SAFE_CHARACTER_PAYLOAD=PASS")

    print("== SAVE CHARACTER RECORD ==")
    status, saved = request_json(SAVE_RECORD_URL, {"character": SAFE_CHARACTER}, method="POST")
    assert status == 200, saved
    assert saved.get("status") == "saved", saved
    assert saved.get("record_id"), saved
    assert saved.get("reload_url"), saved
    assert saved.get("battle_url"), saved
    assert saved.get("player_safe") is True, saved
    assert_no_payload_leaks("record save response", saved)
    record_id = saved["record_id"]
    print("RECORD_SAVE=PASS")
    print(f"RECORD_ID_LENGTH={len(record_id)}")

    print("== LOAD CHARACTER RECORD ==")
    status, loaded = request_json(LOAD_RECORD_BASE + record_id + "?dreamos_smoke=104")
    assert status == 200, loaded
    assert loaded.get("status") == "loaded", loaded
    assert loaded.get("player_safe") is True, loaded
    assert loaded.get("character", {}).get("spark_name") == SAFE_CHARACTER["spark_name"], loaded
    assert_no_payload_leaks("record load response", loaded)
    print("RECORD_LOAD=PASS")
    print("RECORD_NO_RAW_SCORE_LEAK=PASS")

    print("== INVALID RECORD REJECT ==")
    status, invalid = request_json(
        LOAD_RECORD_BASE + "invalid-record-000000?dreamos_smoke=104",
        expect_error=True,
    )
    assert status in (403, 404), invalid
    assert invalid.get("status") == "invalid", invalid
    print("RECORD_INVALID_REJECTED=PASS")

    print("== CHARACTER PAGE RELOAD PATH ==")
    status, character_html = fetch_text(CHARACTER_PAGE_BASE + record_id)
    assert status == 200, status
    assert "character" in character_html.lower(), "character page marker missing"
    assert "ecg-save-character-record-inline" in character_html, "save record UI bridge missing"
    assert "/wp-json/emergence/v1/characters" in character_html, "character record REST code missing"
    assert_no_visible_leaks("character reload page", character_html)
    print("CHARACTER_RECORD_RELOAD_PAGE=PASS")

    print("== BATTLE PAGE RECORD PATH ==")
    status, battle_html = fetch_text(BATTLE_PAGE_BASE + record_id)
    assert status == 200, status
    assert "battle" in battle_html.lower(), "battle page marker missing"
    assert "dreamos-bs-record-handoff-inline" in battle_html, "battle record bridge missing"
    assert "character_record" in battle_html, "character_record code missing"
    assert "emergence_spark_battle_handoff_v1" in battle_html, "storage bridge missing"
    assert_no_visible_leaks("battle record page", battle_html)

    scripts = extract_scripts(battle_html)
    assert "loadCharacterRecord" in scripts, "loadCharacterRecord missing"
    assert "/wp-json/emergence/v1/characters/" in scripts, "record REST load missing"
    assert "Character record rejected or expired" in scripts, "invalid record UI missing"
    assert "FORBIDDEN" in scripts, "forbidden guard missing"
    print("BATTLE_RECORD_BRIDGE=PASS")
    print("BATTLE_RECORD_IMPORT_SEMANTICS=PASS")

    print("== RECORD TO BATTLE TOKEN ==")
    status, token = request_json(LOAD_RECORD_BASE + record_id + "/battle-token?dreamos_smoke=104", method="POST")
    assert status == 200, token
    assert token.get("status") == "created", token
    assert token.get("token"), token
    assert "spark_token=" in token.get("share_url", ""), token
    assert token.get("player_safe") is True, token
    assert_no_payload_leaks("record battle token response", token)
    print("RECORD_BATTLE_TOKEN=PASS")

    print("== CUSTOM BATTLE FROM SAVED RECORD PAYLOAD ==")
    character = loaded["character"]
    status, result = request_json(
        CUSTOM_BATTLE_URL,
        {"spark": character, "opponent": "the-victor"},
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

    print("SAVED_RECORD_CUSTOM_BATTLE=PASS")
    print("SAVED_RECORD_BATTLE_WINNER_VISIBLE=PASS")
    print("SAVED_RECORD_BATTLE_ARENA_VISIBLE=PASS")
    print("SAVED_RECORD_BATTLE_STORY_VISIBLE=PASS")
    print("SAVED_RECORD_BATTLE_NO_RAW_SCORE_LEAK=PASS")

    print("== FINAL ASSERT ==")
    print("SAVED_RECORD_FRESH_PAGE_ASSERT=PASS")
    print("BATTLE_BRIDGE_FROM_RECORD_ASSERT=PASS")
    print("INVALID_RECORD_REJECT_ASSERT=PASS")
    print("WINNER_ARENA_STORY_VISIBLE_ASSERT=PASS")
    print("EMERGENCE_SAVED_CHARACTER_BROWSER_SMOKE=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
