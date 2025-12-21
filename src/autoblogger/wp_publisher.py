from __future__ import annotations

import os
from dataclasses import dataclass
from typing import Any

import requests
from requests.auth import HTTPBasicAuth


@dataclass(frozen=True)
class WordPressEnvConfig:
    base_url: str
    username: str
    app_password: str


def load_wp_env(*, base_url_env: str, user_env: str, app_password_env: str) -> WordPressEnvConfig:
    base_url = (os.environ.get(base_url_env) or "").strip()
    username = (os.environ.get(user_env) or "").strip()
    app_password = (os.environ.get(app_password_env) or "").strip()

    missing = [k for k, v in [(base_url_env, base_url), (user_env, username), (app_password_env, app_password)] if not v]
    if missing:
        raise RuntimeError(f"Missing WordPress env vars: {missing}")

    return WordPressEnvConfig(base_url=base_url.rstrip("/"), username=username, app_password=app_password)


def publish_wordpress_post(*, cfg: WordPressEnvConfig, title: str, content: str, excerpt: str | None, status: str) -> dict[str, Any]:
    api_url = f"{cfg.base_url}/wp-json/wp/v2/posts"
    auth = HTTPBasicAuth(cfg.username, cfg.app_password)

    payload: dict[str, Any] = {
        "title": title,
        "content": content,
        "status": status,
    }
    if excerpt:
        payload["excerpt"] = excerpt

    resp = requests.post(api_url, auth=auth, json=payload, timeout=30)
    if resp.status_code not in (200, 201):
        raise RuntimeError(f"WordPress publish failed: HTTP {resp.status_code}: {resp.text[:500]}")

    data = resp.json()
    return {
        "success": True,
        "post_id": data.get("id"),
        "link": data.get("link"),
    }
