from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Any

import yaml

from .paths import repo_root


@dataclass(frozen=True)
class PublishConfig:
    provider: str
    wp_base_url_env: str | None = None
    wp_user_env: str | None = None
    wp_app_password_env: str | None = None


@dataclass(frozen=True)
class SiteConfig:
    site_id: str
    domain: str
    voice_profile_path: Path
    brand_profile_path: Path
    backlog_path: Path
    calendar_path: Path
    examples_glob: str | None
    publish: PublishConfig
    word_count_min: int
    word_count_max: int
    auto_publish_default: bool


def _load_yaml(path: Path) -> dict[str, Any]:
    return yaml.safe_load(path.read_text(encoding="utf-8")) or {}


def load_site_config(site: str) -> SiteConfig:
    root = repo_root()
    site_path = root / "sites" / f"{site}.yaml"
    if not site_path.exists():
        raise RuntimeError(f"Site config not found: {site_path}")

    d = _load_yaml(site_path)

    publish_d = d.get("publish") or {}
    publish = PublishConfig(
        provider=str(publish_d.get("provider", "wordpress")).strip(),
        wp_base_url_env=publish_d.get("wp_base_url_env"),
        wp_user_env=publish_d.get("wp_user_env"),
        wp_app_password_env=publish_d.get("wp_app_password_env"),
    )

    defaults = d.get("defaults") or {}

    def _abs(p: str) -> Path:
        return (root / p).resolve()

    return SiteConfig(
        site_id=str(d.get("site_id", site)).strip(),
        domain=str(d.get("domain", "")).strip(),
        voice_profile_path=_abs(str(d.get("voice_profile"))),
        brand_profile_path=_abs(str(d.get("brand_profile"))),
        backlog_path=_abs(str(d.get("backlog"))),
        calendar_path=_abs(str(d.get("calendar"))),
        examples_glob=str(d.get("examples_glob")).strip() if d.get("examples_glob") else None,
        publish=publish,
        word_count_min=int(defaults.get("word_count_min", 900)),
        word_count_max=int(defaults.get("word_count_max", 1400)),
        auto_publish_default=bool(defaults.get("auto_publish", False)),
    )
