#!/usr/bin/env python3
"""
Validate credential SSOT artifacts: env keys exist, site_configs alignment.
Exits 0 on success, 1 if any required wordpress env triple is incomplete.
Does not print secret values.
"""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

from credential_ssot_lib import (
    DEFAULT_CONFIG_DIR,
    build_credential_registry,
    load_site_config_keys,
    parse_dotenv_keys,
)

DOCS_DEPLOYMENT = Path(__file__).resolve().parents[1] / "docs" / "deployment"


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--env", type=Path, default=DEFAULT_CONFIG_DIR / ".env")
    ap.add_argument("--site-configs", type=Path, default=DEFAULT_CONFIG_DIR / "site_configs.json")
    ap.add_argument("--registry", type=Path, default=DOCS_DEPLOYMENT / "credential_registry.json")
    args = ap.parse_args()

    env_data = parse_dotenv_keys(args.env)
    expected = build_credential_registry(args.env, args.site_configs)
    sc_keys = set(load_site_config_keys(args.site_configs))

    errors: list[str] = []
    warnings: list[str] = []

    if args.registry.exists():
        try:
            on_disk = json.loads(args.registry.read_text(encoding="utf-8"))
            if on_disk.get("sites", {}).keys() != expected["sites"].keys():
                warnings.append("credential_registry.json on disk differs from live build (re-run build_ssot_credential_artifacts.py)")
        except json.JSONDecodeError as e:
            errors.append(f"Invalid JSON in {args.registry}: {e}")

    for domain, site in sorted(expected["sites"].items()):
        wp = site.get("wordpress")
        if not wp:
            warnings.append(f"{domain}: no *_WP_* env mapping (wordpress automation may be incomplete)")
            continue
        ek = wp["env_keys"]
        missing = [k for k in (ek["wp_url"], ek["wp_user"], ek["wp_app_password"]) if not env_data.get(k)]
        if missing:
            errors.append(f"{domain}: missing env keys: {', '.join(missing)}")

        if domain not in sc_keys:
            warnings.append(f"{domain}: not in site_configs.json (sftp block may rely on SFTP_* in .env only)")

    print("=== credential registry validation ===")
    for w in warnings:
        print(f"WARN: {w}")
    for e in errors:
        print(f"FAIL: {e}")

    if errors:
        sys.exit(1)
    print("OK: all mapped sites have complete wordpress env key references")
    sys.exit(0)


if __name__ == "__main__":
    main()
