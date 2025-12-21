from __future__ import annotations

import argparse
from pathlib import Path

from .paths import repo_root
from .run_daily import run_daily_for_site


def main() -> int:
    parser = argparse.ArgumentParser(description="Autoblogger: run all sites")
    parser.add_argument("--timezone", default="America/Chicago")
    parser.add_argument("--date", help="Override date (YYYY-MM-DD)")
    parser.add_argument("--dry-run", action="store_true")
    parser.add_argument("--auto-publish", action="store_true", help="Force auto-publish for all sites")
    parser.add_argument("--wp-status", default="draft", choices=["draft", "publish"])
    args = parser.parse_args()

    sites_dir = repo_root() / "sites"
    site_files = sorted(sites_dir.glob("*.yaml"))
    if not site_files:
        raise RuntimeError(f"No site configs found in {sites_dir}")

    failures = 0
    for p in site_files:
        site = p.stem
        try:
            run_daily_for_site(
                site=site,
                date_override=args.date,
                timezone=args.timezone,
                auto_publish=args.auto_publish,
                wp_status=args.wp_status,
                dry_run=args.dry_run,
            )
        except Exception:
            failures += 1

    return 0 if failures == 0 else 1


if __name__ == "__main__":
    raise SystemExit(main())
