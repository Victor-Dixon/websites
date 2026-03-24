#!/usr/bin/env python3
"""Write site_identity_map.json and credential_registry.json (no secrets)."""

from __future__ import annotations

import argparse
import json
from pathlib import Path

from credential_ssot_lib import (
    DEFAULT_CONFIG_DIR,
    build_credential_registry,
    build_site_identity_map,
)

DOCS_DEPLOYMENT = Path(__file__).resolve().parents[1] / "docs" / "deployment"


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--env", type=Path, default=DEFAULT_CONFIG_DIR / ".env")
    ap.add_argument("--site-configs", type=Path, default=DEFAULT_CONFIG_DIR / "site_configs.json")
    ap.add_argument("--out-dir", type=Path, default=DOCS_DEPLOYMENT)
    args = ap.parse_args()

    args.out_dir.mkdir(parents=True, exist_ok=True)

    ident = build_site_identity_map(args.env, args.site_configs)
    registry = build_credential_registry(args.env, args.site_configs)

    id_path = args.out_dir / "site_identity_map.json"
    reg_path = args.out_dir / "credential_registry.json"
    id_path.write_text(json.dumps(ident, indent=2, sort_keys=True) + "\n", encoding="utf-8")
    reg_path.write_text(json.dumps(registry, indent=2, sort_keys=True) + "\n", encoding="utf-8")
    print(f"Wrote {id_path}")
    print(f"Wrote {reg_path}")


if __name__ == "__main__":
    main()
