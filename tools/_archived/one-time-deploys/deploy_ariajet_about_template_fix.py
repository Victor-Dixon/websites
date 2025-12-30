#!/usr/bin/env python3
"""
Deploy About-page template fix to ariajet.site
=============================================

This uploads the updated `page-about.php` and `functions.php` for the AriaJet themes
so the About page stops showing the hardcoded placeholder content.

By default it deploys ALL related themes found in this repo:
- ariajet
- ariajet-studio
- ariajet-cosmic

Requires SFTP/SSH creds in `/workspace/.env` (see `.env.example`).
"""

from __future__ import annotations

import sys
from pathlib import Path


REPO_ROOT = Path(__file__).resolve().parents[1]
SITE = "ariajet.site"


def main(argv: list[str]) -> int:
    import argparse

    parser = argparse.ArgumentParser(description="Deploy ariajet About template fix.")
    parser.add_argument("--theme", action="append", help="Deploy only a specific theme slug (repeatable).")
    args = parser.parse_args(argv)

    sys.path.insert(0, str(REPO_ROOT))

    from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    from ops.deployment.wp_remote_utils import detect_wp_path, load_repo_dotenv

    load_repo_dotenv(repo_root=REPO_ROOT)

    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(SITE, site_configs)
    if not deployer.connect():
        return 1

    try:
        wp_path = detect_wp_path(deployer=deployer, site_domain=SITE)
        if not wp_path:
            print("❌ Could not detect WordPress path on server.")
            return 1

        themes_root = f"{wp_path}/wp-content/themes"
        local_themes_root = REPO_ROOT / "websites" / SITE / "wp" / "wp-content" / "themes"

        theme_slugs = args.theme or ["ariajet", "ariajet-studio", "ariajet-cosmic"]

        print(f"🚀 Deploying About template fix to {SITE}")
        print(f"📂 Remote WP root: {wp_path}")
        print(f"📂 Remote themes: {themes_root}")
        print(f"🎨 Themes: {', '.join(theme_slugs)}")

        for slug in theme_slugs:
            local_theme_dir = local_themes_root / slug
            if not local_theme_dir.exists():
                print(f"⚠️  Skipping missing local theme: {slug}")
                continue

            for filename in ("page-about.php", "functions.php"):
                local_file = local_theme_dir / filename
                if not local_file.exists():
                    print(f"⚠️  Skipping missing local file: {slug}/{filename}")
                    continue

                remote_file = f"{themes_root}/{slug}/{filename}"
                ok = deployer.deploy_file(local_file, remote_file)
                print(f" - {slug}/{filename}: {'✅ uploaded' if ok else '❌ failed'}")
                if not ok:
                    return 1

        # Flush caches to ensure changes show
        deployer.execute_command(f"cd {wp_path} && wp cache flush 2>&1")
        deployer.execute_command(f"cd {wp_path} && wp transient delete --all 2>&1")

        print("✅ Done. About page template updated on server.")
        print(f"🔗 Check: https://{SITE}/about")
        return 0
    finally:
        try:
            deployer.disconnect()
        except Exception:
            pass


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))

