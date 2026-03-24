"""
Shared helpers for credential SSOT: parse config/.env WP triplets, merge with site_configs keys.

No secret values are returned from disk reads except via get_wp_credentials_for_site()
used only by local validation probes — never log those values.
"""

from __future__ import annotations

import json
import os
from pathlib import Path
from typing import Any, Dict, List, Optional, Tuple
from urllib.parse import urlparse

WEBSITES_ROOT = Path(__file__).resolve().parents[1]
DEFAULT_CONFIG_DIR = WEBSITES_ROOT / "config"
DEFAULT_ENV_PATH = DEFAULT_CONFIG_DIR / ".env"
DEFAULT_SITE_CONFIGS_PATH = DEFAULT_CONFIG_DIR / "site_configs.json"
DEFAULT_AGENT_ENV_PATH = Path(os.environ.get("AGENT_CELLPHONE_ENV", "D:/Agent_Cellphone_V2_Repository/.env"))

def parse_dotenv_keys(path: Path) -> Dict[str, str]:
    """Parse KEY=VAL lines (no multiline). Values are not redacted here — callers must not persist."""
    out: Dict[str, str] = {}
    if not path.exists():
        return out
    for line in path.read_text(encoding="utf-8", errors="replace").splitlines():
        line = line.strip()
        if not line or line.startswith("#"):
            continue
        if "=" not in line:
            continue
        k, _, v = line.partition("=")
        key = k.strip()
        val = v.strip().strip('"').strip("'")
        out[key] = val
    return out


def domain_from_wp_rest_base(url: str) -> str:
    u = url.strip().rstrip("/")
    parsed = urlparse(u if "://" in u else f"https://{u}")
    host = (parsed.netloc or parsed.path.split("/")[0]).lower()
    if host.startswith("www."):
        host = host[4:]
    return host


def origin_from_wp_rest_base(url: str) -> str:
    d = domain_from_wp_rest_base(url)
    return f"https://{d}"


def load_wp_env_prefix_map(env_path: Path = DEFAULT_ENV_PATH) -> Dict[str, Dict[str, str]]:
    """
    Returns domain -> { env_prefix, keys: {url,user,pass} }
    """
    data = parse_dotenv_keys(env_path)
    by_domain: Dict[str, Dict[str, str]] = {}
    for key, val in data.items():
        if not key.endswith("_WP_URL"):
            continue
        prefix = key[: -len("_WP_URL")]
        raw_url = val.strip()
        domain = domain_from_wp_rest_base(raw_url)
        user_key = f"{prefix}_WP_USER"
        pass_key = f"{prefix}_WP_APP_PASS"
        by_domain[domain] = {
            "env_prefix": prefix,
            "env_keys": {
                "wp_url": f"{prefix}_WP_URL",
                "wp_user": user_key,
                "wp_app_password": pass_key,
            },
        }
    return by_domain


def load_site_config_keys(site_configs_path: Path = DEFAULT_SITE_CONFIGS_PATH) -> List[str]:
    if not site_configs_path.exists():
        return []
    with open(site_configs_path, encoding="utf-8") as f:
        cfg = json.load(f)
    return sorted(cfg.keys())


def build_site_identity_map(
    env_path: Path = DEFAULT_ENV_PATH,
    site_configs_path: Path = DEFAULT_SITE_CONFIGS_PATH,
) -> Dict[str, Any]:
    wp_by_domain = load_wp_env_prefix_map(env_path)
    sc_keys = set(load_site_config_keys(site_configs_path))
    notes: List[str] = []
    # Resolved naming: SWARMONLINE -> weareswarm.online, SWARMSITE -> weareswarm.site
    swarm = [
        d for d in wp_by_domain if "swarm" in d and d.endswith((".online", ".site"))
    ]
    if len(swarm) >= 2:
        notes.append(
            "SWARMONLINE_* binds to weareswarm.online; SWARMSITE_* binds to weareswarm.site (distinct domains)."
        )

    sites_out: Dict[str, Any] = {}
    for domain in sorted(set(wp_by_domain) | sc_keys):
        entry: Dict[str, Any] = {
            "canonical_domain": domain,
            "credential_sources": {
                "wordpress_env_file": str(env_path.as_posix()),
                "site_configs_json": str(site_configs_path.as_posix()) if domain in sc_keys else None,
                "hostinger_env_file": str(DEFAULT_AGENT_ENV_PATH.as_posix()),
            },
        }
        if domain in wp_by_domain:
            entry["wordpress_env"] = {
                "env_prefix": wp_by_domain[domain]["env_prefix"],
                "env_keys": wp_by_domain[domain]["env_keys"],
            }
        else:
            entry["wordpress_env"] = None
        entry["site_configs_present"] = domain in sc_keys
        sites_out[domain] = entry

    return {
        "version": 1,
        "generated_from": {
            "env_path": str(env_path.as_posix()),
            "site_configs_path": str(site_configs_path.as_posix()),
        },
        "notes": notes,
        "sites": sites_out,
    }


def build_credential_registry(
    env_path: Path = DEFAULT_ENV_PATH,
    site_configs_path: Path = DEFAULT_SITE_CONFIGS_PATH,
) -> Dict[str, Any]:
    ident = build_site_identity_map(env_path, site_configs_path)
    sites: Dict[str, Any] = {}
    for domain, row in ident["sites"].items():
        sites[domain] = {
            "canonical_domain": domain,
            "wordpress": row.get("wordpress_env"),
            "sftp": (
                {"source": "site_configs.json", "path": str(site_configs_path.as_posix())}
                if row.get("site_configs_present")
                else {
                    "source": "config.env",
                    "keys": ["SFTP_HOST", "SFTP_PORT", "SFTP_USERNAME", "SFTP_PASSWORD"],
                    "path": str(env_path.as_posix()),
                }
            ),
            "hostinger_shared_env": str(DEFAULT_AGENT_ENV_PATH.as_posix()),
        }
    return {
        "version": 1,
        "description": "Canonical registry: domains + env key references only (no secret values).",
        "sources": {
            "wordpress_credentials": "websites/config/.env (*_WP_URL/_USER/_APP_PASS)",
            "site_sftp": "websites/config/site_configs.json#sftp when present; else config/.env SFTP_*",
            "shared_hostinger": "Agent_Cellphone_V2_Repository/.env HOSTINGER_*",
        },
        "sites": sites,
    }


def get_wp_credentials_for_site(
    domain: str,
    env_path: Path = DEFAULT_ENV_PATH,
) -> Optional[Tuple[str, str, str]]:
    """Returns (origin, user, app_password) or None if missing."""
    wp_map = load_wp_env_prefix_map(env_path)
    if domain not in wp_map:
        return None
    data = parse_dotenv_keys(env_path)
    keys = wp_map[domain]["env_keys"]
    url = data.get(keys["wp_url"])
    user = data.get(keys["wp_user"])
    pw = data.get(keys["wp_app_password"])
    if not all([url, user, pw]):
        return None
    origin = origin_from_wp_rest_base(url)
    return origin, user, pw
