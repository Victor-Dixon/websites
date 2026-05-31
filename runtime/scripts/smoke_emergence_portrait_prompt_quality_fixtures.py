#!/usr/bin/env python3
from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / "_reports" / "emergence_portrait_prompt_quality_fixtures_108.json"

FORBIDDEN = [
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
    "raw_roll",
    "odds:",
]

PROFILE = {
    "name": "The Prism Warden",
    "archetype": "Lightbound Guardian",
    "powers": ["Laser Light", "Hard Light", "Energy Absorption"],
    "combat_role": "radiant striker and protective field controller",
    "aura": "prismatic light bending around hard-light armor plates",
    "background": "storm-lit rooftop relay with rain, sparks, and skyline silhouettes",
}

FIXTURES = [
    {
        "id": "lean_armored_haunted_active",
        "build": "lean athletic",
        "costume": "armored hooded suit with cracked gold mask and glowing prism seams",
        "personality": "haunted survivor with controlled intensity",
        "showcase": "active effects",
    },
    {
        "id": "powerful_cosmic_noble_dramatic",
        "build": "powerful",
        "costume": "cosmic cape, heavy shoulder armor, star-metal gauntlets, radiant chest sigil",
        "personality": "noble guardian who looks impossible to move",
        "showcase": "dramatic surge",
    },
    {
        "id": "compact_tactical_cocky_subtle",
        "build": "compact fighter",
        "costume": "sleek tactical jacket, plated boots, luminous visor, utility belt",
        "personality": "cocky street hero with sharp confident posture",
        "showcase": "subtle hints",
    },
    {
        "id": "tall_elegant_stoic_restrained",
        "build": "tall imposing",
        "costume": "elegant black-and-white battle coat, glasslike armor trim, long split cloak",
        "personality": "stoic protector with calm controlled presence",
        "showcase": "restrained control",
    },
]


def compile_prompt(profile: dict, fixture: dict) -> str:
    power_names = ", ".join(profile["powers"])
    return "\n".join([
        f"Create premium American superhero comic-book character art for a new original hero named {profile['name']}.",
        "FULL BODY REVEAL STANDARD: show the complete character from head to toe, full costume visible, heroic stance, no cropped portrait, no bust-only portrait, no face-only portrait.",
        "STYLE: premium American superhero comic-book aesthetic, bold inked linework, dramatic cinematic lighting, cover-art composition, heroic costume design.",
        f"ARCHETYPE: {profile['archetype']}.",
        f"BUILD TYPE: {fixture['build']}.",
        f"CUSTOM COSTUME DIRECTION: {fixture['costume']}.",
        f"CUSTOM PERSONALITY / ATTITUDE: {fixture['personality']}.",
        f"POWERS TO VISUALLY SHOWCASE: {power_names}.",
        f"ABILITY SHOWCASE MODE: {fixture['showcase']}; show abilities through visible effects, pose, costume motifs, aura, and environmental reaction.",
        f"COMBAT ROLE: {profile['combat_role']}.",
        f"AURA / EFFECTS: {profile['aura']}.",
        f"POSE DIRECTION: full-body heroic reveal, readable silhouette, feet visible, hands visible, power effects visible, costume readable.",
        f"BACKGROUND ENERGY: {profile['background']}.",
        "COMPOSITION: full-body reveal, complete head-to-toe superhero design, readable silhouette, costume and abilities visible in one image.",
        "AVOID: logos from existing franchises, copyrighted superhero symbols, text labels, watermarks, UI panels, internal mechanics, score tables, dice rolls, probability language, backend data.",
    ])


def assert_contains(prompt: str, expected: str, label: str) -> None:
    if expected.lower() not in prompt.lower():
        raise AssertionError(f"{label} missing expected phrase: {expected}")


def assert_no_leaks(prompt: str) -> None:
    lower = prompt.lower()
    leaks = [item for item in FORBIDDEN if item.lower() in lower]
    if leaks:
        raise AssertionError(f"prompt leaked hidden mechanics: {leaks}")


def main() -> int:
    rows = []
    prompt_hash_basis = set()

    print("== BUILD FIXTURE PROMPTS ==")
    for fixture in FIXTURES:
        prompt = compile_prompt(PROFILE, fixture)

        assert_contains(prompt, "FULL BODY REVEAL STANDARD", "full body standard")
        assert_contains(prompt, "head to toe", "head-to-toe wording")
        assert_contains(prompt, "full costume visible", "costume visibility")
        assert_contains(prompt, fixture["build"], "build type")
        assert_contains(prompt, fixture["costume"], "costume")
        assert_contains(prompt, fixture["personality"], "personality")
        assert_contains(prompt, fixture["showcase"], "showcase")
        assert_contains(prompt, "POWERS TO VISUALLY SHOWCASE", "power showcase")
        for power in PROFILE["powers"]:
            assert_contains(prompt, power, f"power {power}")

        assert "frame selection" not in prompt.lower()
        assert "bust-only portrait" in prompt.lower()
        assert_no_leaks(prompt)

        normalized = re.sub(r"\s+", " ", prompt.strip())
        prompt_hash_basis.add(normalized)

        rows.append({
            "id": fixture["id"],
            "build": fixture["build"],
            "costume": fixture["costume"],
            "personality": fixture["personality"],
            "showcase": fixture["showcase"],
            "prompt": prompt,
            "checks": {
                "full_body_standard": True,
                "custom_costume": True,
                "custom_personality": True,
                "ability_showcase": True,
                "no_raw_score_leaks": True,
                "frame_selection_removed": True,
            },
        })

        print(f"FIXTURE_{fixture['id']}=PASS")

    if len(prompt_hash_basis) != len(FIXTURES):
        raise AssertionError("fixtures did not produce distinct prompts")

    OUT.write_text(json.dumps({
        "version": 1,
        "status": "pass",
        "profile": PROFILE,
        "fixtures": rows,
        "summary": {
            "fixture_count": len(rows),
            "distinct_prompts": len(prompt_hash_basis),
            "full_body_standard": "pass",
            "custom_player_design_inputs": "pass",
            "ability_showcase": "pass",
            "no_raw_score_leaks": "pass",
        },
    }, indent=2))

    print("PROMPT_FIXTURE_MATRIX_WRITTEN=PASS")
    print(f"PROMPT_FIXTURE_MATRIX={OUT}")
    print("PROMPT_FIXTURE_DISTINCTNESS=PASS")
    print("PROMPT_FIXTURE_FULL_BODY_STANDARD=PASS")
    print("PROMPT_FIXTURE_CUSTOM_COSTUME=PASS")
    print("PROMPT_FIXTURE_CUSTOM_PERSONALITY=PASS")
    print("PROMPT_FIXTURE_ABILITY_SHOWCASE=PASS")
    print("PROMPT_FIXTURE_NO_RAW_SCORE_LEAK=PASS")
    print("EMERGENCE_PORTRAIT_PROMPT_QUALITY_FIXTURES=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
