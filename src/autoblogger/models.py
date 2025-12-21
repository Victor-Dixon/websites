from __future__ import annotations

from dataclasses import dataclass
from typing import Any


@dataclass(frozen=True)
class BacklogItem:
    id: str
    pillar: str
    audience: str
    title: str
    angle: str
    keywords: list[str]
    cta: str
    status: str

    @staticmethod
    def from_dict(d: dict[str, Any]) -> "BacklogItem":
        return BacklogItem(
            id=str(d.get("id", "")).strip(),
            pillar=str(d.get("pillar", "")).strip(),
            audience=str(d.get("audience", "")).strip(),
            title=str(d.get("title", "")).strip(),
            angle=str(d.get("angle", "")).strip(),
            keywords=[str(x).strip() for x in (d.get("keywords") or [])],
            cta=str(d.get("cta", "")).strip(),
            status=str(d.get("status", "")).strip(),
        )


@dataclass(frozen=True)
class BrandProfile:
    word_count_min: int
    word_count_max: int
    internal_links: dict[str, str]

    @staticmethod
    def from_dict(d: dict[str, Any]) -> "BrandProfile":
        wc = (d.get("content_rules") or {}).get("word_count") or [900, 1400]
        wc_min = int(wc[0])
        wc_max = int(wc[1])
        internal_links = (d.get("seo") or {}).get("internal_links") or {}
        return BrandProfile(word_count_min=wc_min, word_count_max=wc_max, internal_links=dict(internal_links))
