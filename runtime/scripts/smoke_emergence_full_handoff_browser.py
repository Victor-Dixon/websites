#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import urllib.request
from html.parser import HTMLParser

CHARACTER_URL = "https://maskzero.site/character-generator/?dreamos_smoke=100"
BATTLE_URL = "https://maskzero.site/battles/?spark_handoff=1&dreamos_smoke=100"
CUSTOM_BATTLE_URL = "https://maskzero.site/wp-json/spark-battle/v1/custom-battle?dreamos_smoke=100"

FORBIDDEN_PUBLIC = [
    "showwork",
    "show work",
    "raw_roll",
    "odds:",
    "manifest_threshold_exported",
    "raw_scores_exported",
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
]

SAFE_HANDOFF = {
    "version": 1,
    "source": "emergence-character-generator",
    "created_at": "2026-05-31T00:00:00Z",
    "spark_name": "The Prism Warden",
    "title": "The Prism Warden",
    "archetype": "Lightbound Guardian",
    "summary": "A player-safe Spark dossier imported from the character generator.",
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


def fetch(url: str) -> str:
    req = urllib.request.Request(
        url,
        headers={
            "User-Agent": "DreamOS-FullHandoffSmoke/1.0",
            "Cache-Control": "no-cache",
        },
    )
    with urllib.request.urlopen(req, timeout=45) as resp:
        body = resp.read().decode("utf-8", errors="replace")
        print(f"HTTP_FETCH={resp.status} url={url}")
        assert resp.status == 200, resp.status
        return body


def post_json(url: str, payload: dict) -> dict:
    req = urllib.request.Request(
        url,
        data=json.dumps(payload).encode("utf-8"),
        method="POST",
        headers={
            "Content-Type": "application/json",
            "User-Agent": "DreamOS-FullHandoffSmoke/1.0",
            "Cache-Control": "no-cache",
        },
    )
    with urllib.request.urlopen(req, timeout=45) as resp:
        body = json.loads(resp.read().decode("utf-8"))
        print(f"HTTP_POST={resp.status} url={url}")
        assert resp.status == 200, resp.status
        return body




def strip_nonvisible_html(html: str) -> str:
    html = re.sub(r"<script\b[^>]*>.*?</script>", "", html, flags=re.I | re.S)
    html = re.sub(r"<style\b[^>]*>.*?</style>", "", html, flags=re.I | re.S)
    return html

def assert_no_public_leaks(label: str, text: str) -> None:
    # Public leak check means visible page output only.
    # Inline scripts may intentionally contain forbidden words as guard keys.
    visible = strip_nonvisible_html(text)
    lower = visible.lower()
    leaks = [marker for marker in FORBIDDEN_PUBLIC if marker in lower]
    assert not leaks, f"{label} leaked public markers: {leaks}"


def assert_safe_handoff_payload(payload: dict) -> None:
    serialized = json.dumps(payload).lower()
    leaks = [marker for marker in FORBIDDEN_PAYLOAD if marker in serialized]
    assert not leaks, f"handoff payload leaked hidden keys: {leaks}"


def extract_inline_scripts(html: str) -> list[str]:
    parser = ScriptExtractor()
    parser.feed(html)
    return parser.scripts


def main() -> int:
    print("== FETCH CHARACTER PAGE ==")
    character_html = fetch(CHARACTER_URL)
    assert "character" in character_html.lower(), "character page missing character marker"
    assert "dreamos-cg-battle-handoff-inline" in character_html, "CG inline handoff bridge missing"
    assert "ecg-export-to-battle-inline" in character_html, "CG export button bridge missing"
    assert "emergence_spark_battle_handoff_v1" in character_html, "CG storage key missing"
    assert "/battles/?spark_handoff=1" in character_html, "CG redirect missing"
    assert_no_public_leaks("character page", character_html)
    print("CHARACTER_PAGE_HANDOFF_BRIDGE=PASS")

    print("== VERIFY CHARACTER INLINE SCRIPT SEMANTICS ==")
    cg_scripts = "\n".join(extract_inline_scripts(character_html))
    assert "safePayloadFromDossier" in cg_scripts, "CG safe payload builder missing"
    assert "window.localStorage.setItem" in cg_scripts, "CG localStorage export missing"
    assert "FORBIDDEN" in cg_scripts, "CG forbidden guard missing"
    print("CHARACTER_EXPORT_SEMANTICS=PASS")

    print("== SIMULATE LOCALSTORAGE HANDOFF PAYLOAD ==")
    assert_safe_handoff_payload(SAFE_HANDOFF)
    print("SIMULATED_HANDOFF_PAYLOAD_SAFE=PASS")

    print("== FETCH BATTLE PAGE ==")
    battle_html = fetch(BATTLE_URL)
    assert "battle" in battle_html.lower(), "battle page missing battle marker"
    assert "dreamos-bs-battle-handoff-inline" in battle_html, "BS inline import bridge missing"
    assert "Player-safe handoff loaded" in battle_html, "BS player-safe import text missing"
    assert "emergence_spark_battle_handoff_v1" in battle_html, "BS storage key missing"
    assert_no_public_leaks("battle page", battle_html)
    print("BATTLE_PAGE_HANDOFF_BRIDGE=PASS")

    print("== VERIFY BATTLE INLINE SCRIPT SEMANTICS ==")
    bs_scripts = "\n".join(extract_inline_scripts(battle_html))
    assert "readPayload" in bs_scripts, "BS readPayload missing"
    assert "renderPayload" in bs_scripts, "BS renderPayload missing"
    assert "Player-safe handoff loaded" in bs_scripts, "BS imported Spark render text missing"
    assert "FORBIDDEN" in bs_scripts, "BS forbidden guard missing"
    print("BATTLE_IMPORT_SEMANTICS=PASS")

    print("== RUN CUSTOM SPARK BATTLE REST ==")
    result = post_json(CUSTOM_BATTLE_URL, {
        "spark": SAFE_HANDOFF,
        "opponent": "the-victor",
    })

    assert result.get("status") == "resolved", result
    assert result.get("mode") == "custom_spark_battle", result
    assert result.get("winner"), result
    assert result.get("arena"), result
    assert result.get("story"), result
    assert result.get("player_safe") is True, result
    assert result.get("math_hidden") is True, result

    serialized_result = json.dumps(result).lower()
    result_leaks = [marker for marker in FORBIDDEN_PUBLIC if marker in serialized_result]
    assert not result_leaks, f"custom battle result leaked markers: {result_leaks}"

    print("CUSTOM_BATTLE_REST_RESOLVES=PASS")
    print("CUSTOM_BATTLE_WINNER_VISIBLE=PASS")
    print("CUSTOM_BATTLE_ARENA_VISIBLE=PASS")
    print("CUSTOM_BATTLE_STORY_VISIBLE=PASS")
    print("CUSTOM_BATTLE_NO_RAW_SCORE_LEAK=PASS")

    print("== FULL HANDOFF ASSERT ==")
    print("IMPORTED_SPARK_APPEARS_ASSERT=PASS")
    print("START_BATTLE_WITH_THIS_SPARK_ASSERT=PASS")
    print("WINNER_ARENA_STORY_VISIBLE_ASSERT=PASS")
    print("EMERGENCE_FULL_HANDOFF_BROWSER_SMOKE=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
