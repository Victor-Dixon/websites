from __future__ import annotations

from dataclasses import dataclass

from .models import BacklogItem


@dataclass(frozen=True)
class Prompt:
    system: str
    user: str


def build_prompt(
    *,
    voice_profile_md: str,
    brand_profile_yaml: str,
    example_snippets: str,
    item: BacklogItem,
) -> Prompt:
    system = "Write a blog post in the exact voice defined in VOICE_PROFILE. Do not drift."

    user = (
        "VOICE_PROFILE:\n"
        f"<<<{voice_profile_md}>>>\n\n"
        "BRAND_PROFILE:\n"
        f"<<<{brand_profile_yaml}>>>\n\n"
        "OPTIONAL_EXAMPLES (for style imitation):\n"
        f"<<<{example_snippets}>>>\n\n"
        "POST_BRIEF:\n"
        f"- title: {item.title}\n"
        f"- audience: {item.audience}\n"
        f"- pillar: {item.pillar}\n"
        f"- angle: {item.angle}\n"
        f"- keywords: {item.keywords}\n"
        f"- CTA: {item.cta}\n\n"
        "OUTPUT:\n"
        "- Markdown only\n"
        "- 900–1400 words\n"
        "- Use H2/H3 headings\n"
        "- End with the CTA\n"
        "\nWRITE:\n"
    )

    return Prompt(system=system, user=user)
