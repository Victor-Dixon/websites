from __future__ import annotations

import re
from dataclasses import dataclass


REQUIRED_H2 = [
    "## Problem",
    "## Fix",
    "## Steps",
    "## Example",
    "## CTA",
]


@dataclass(frozen=True)
class ValidationResult:
    ok: bool
    errors: list[str]
    word_count: int


def _word_count(md: str) -> int:
    # cheap-ish: count word-like tokens
    return len(re.findall(r"\b\w+\b", md))


def validate_markdown(md: str, *, word_count_min: int, word_count_max: int, cta_type: str) -> ValidationResult:
    errors: list[str] = []

    wc = _word_count(md)
    if wc < word_count_min or wc > word_count_max:
        errors.append(f"word_count {wc} not in range [{word_count_min}, {word_count_max}]")

    for h in REQUIRED_H2:
        if h not in md:
            errors.append(f"missing required section heading: {h}")

    # CTA presence check (very lightweight)
    cta_type_lower = (cta_type or "").lower().strip()
    if cta_type_lower == "audit" and "/audit" not in md and "AUDIT" not in md:
        errors.append("CTA type is audit but no /audit link or AUDIT keyword found")
    if cta_type_lower == "scoreboard" and "/scoreboard" not in md and "SCOREBOARD" not in md:
        errors.append("CTA type is scoreboard but no /scoreboard link or SCOREBOARD keyword found")
    if cta_type_lower == "intake" and "/intake" not in md and "INTAKE" not in md:
        errors.append("CTA type is intake but no /intake link or INTAKE keyword found")
    if cta_type_lower == "sprint" and "SPRINT" not in md:
        errors.append("CTA type is sprint but no SPRINT keyword found")

    return ValidationResult(ok=len(errors) == 0, errors=errors, word_count=wc)
