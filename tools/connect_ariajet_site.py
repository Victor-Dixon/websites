#!/usr/bin/env python3
"""
Connection check for ariajet.site
================================

This script validates that credentials are present and attempts a lightweight
connection to ariajet.site using either:

- WordPress REST API (recommended for content posting)
- SFTP/SSH (used for theme/file deployments)

Credentials are read from environment variables (optionally loaded via a local
`.env` if you use python-dotenv).

See `.env.example` for the supported variables.
"""

from __future__ import annotations

import os
import sys
from pathlib import Path


REPO_ROOT = Path(__file__).resolve().parents[1]
SITE = "ariajet.site"


def _load_dotenv_if_available(path: Path) -> None:
    try:
        from dotenv import load_dotenv  # type: ignore
    except Exception:
        return
    if path.exists():
        load_dotenv(path, override=False)


def _get(name: str) -> str:
    return (os.environ.get(name) or "").strip()


def _rest_env() -> tuple[str, str, str]:
    site_url = _get("ARIAJET_SITE_WP_SITE_URL") or _get("WP_SITE_URL") or "https://ariajet.site"
    username = _get("ARIAJET_SITE_WP_USERNAME") or _get("WP_USERNAME")
    app_password = _get("ARIAJET_SITE_WP_APP_PASSWORD") or _get("WP_APP_PASSWORD")
    return site_url.rstrip("/"), username, app_password


def _sftp_env() -> tuple[str, str, str, int]:
    host = _get("ARIAJET_SITE_SFTP_HOST") or _get("SFTP_HOST") or _get("HOSTINGER_HOST")
    user = _get("ARIAJET_SITE_SFTP_USER") or _get("SFTP_USER") or _get("HOSTINGER_USER")
    pw = _get("ARIAJET_SITE_SFTP_PASS") or _get("SFTP_PASS") or _get("HOSTINGER_PASS")
    port_str = _get("ARIAJET_SITE_SFTP_PORT") or _get("SFTP_PORT") or _get("HOSTINGER_PORT") or "65002"
    try:
        port = int(port_str)
    except ValueError:
        port = 65002
    return host, user, pw, port


def check_rest() -> int:
    try:
        import requests
        from requests.auth import HTTPBasicAuth
    except Exception:
        print("❌ Missing dependency: requests (install with `pip install requests`)")
        return 2

    site_url, username, app_password = _rest_env()
    if not username or not app_password:
        print("❌ REST API credentials missing.")
        print("   Set ARIAJET_SITE_WP_USERNAME and ARIAJET_SITE_WP_APP_PASSWORD (optional: ARIAJET_SITE_WP_SITE_URL).")
        return 2

    # /users/me is a good auth validation endpoint on most WP installs.
    url = f"{site_url}/wp-json/wp/v2/users/me"
    try:
        resp = requests.get(url, auth=HTTPBasicAuth(username, app_password), timeout=20)
    except Exception as e:
        print(f"❌ REST API connection failed: {e}")
        return 1

    if resp.status_code == 200:
        data = resp.json()
        print("✅ REST API auth OK.")
        print(f"   Site: {site_url}")
        print(f"   User: {data.get('name') or data.get('username') or '(unknown)'}")
        return 0

    print(f"❌ REST API auth failed: HTTP {resp.status_code}")
    print(resp.text[:500])
    return 1


def check_sftp() -> int:
    try:
        from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except Exception as e:
        print(f"❌ Could not import deployer: {e}")
        return 2

    # Validate env first (so failure output is clear even if configs are placeholders).
    host, user, pw, port = _sftp_env()
    if not (host and user and pw):
        print("❌ SFTP credentials missing.")
        print("   Set ARIAJET_SITE_SFTP_HOST / ARIAJET_SITE_SFTP_USER / ARIAJET_SITE_SFTP_PASS (optional: ARIAJET_SITE_SFTP_PORT).")
        return 2

    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(SITE, site_configs)
    if not deployer.connect():
        return 1

    try:
        # A lightweight check: list remote root or configured remote_path
        base = getattr(deployer, "remote_path", "") or "."
        listing = deployer.sftp.listdir(base)[:20]  # type: ignore[attr-defined]
        print("✅ SFTP connected OK.")
        print(f"   Host: {host}:{port}")
        print(f"   Path: {base}")
        print(f"   Sample entries: {', '.join(listing) if listing else '(empty)'}")
        return 0
    except Exception as e:
        print(f"⚠️  Connected but could not list directory: {e}")
        return 0
    finally:
        try:
            deployer.disconnect()
        except Exception:
            pass


def main(argv: list[str]) -> int:
    import argparse

    _load_dotenv_if_available(REPO_ROOT / ".env")

    parser = argparse.ArgumentParser(description="Check connectivity to ariajet.site (REST API and/or SFTP).")
    parser.add_argument("--method", choices=["auto", "rest", "sftp"], default="auto")
    args = parser.parse_args(argv)

    if args.method == "rest":
        return check_rest()
    if args.method == "sftp":
        return check_sftp()

    # auto
    rest_site_url, rest_user, rest_pw = _rest_env()
    sftp_host, sftp_user, sftp_pw, _ = _sftp_env()

    if rest_user and rest_pw:
        return check_rest()
    if sftp_host and sftp_user and sftp_pw:
        return check_sftp()

    print("❌ No credentials found for ariajet.site.")
    print("   See `.env.example` and create `/workspace/.env` with either REST API or SFTP creds.")
    return 2


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))

