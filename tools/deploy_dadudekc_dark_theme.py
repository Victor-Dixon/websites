#!/usr/bin/env python3
"""Deploy dadudekc.com theme (full directory) and verify homepage marker."""

from pathlib import Path
import sys

import requests

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

THEME_NAME = "dadudekc"
MARKER_TEXT = "Victor builds ambitious systems"
REMOTE_THEME_BASE = f"wp-content/themes/{THEME_NAME}"


def get_local_theme_path() -> Path:
    return (
        Path(__file__).parent.parent
        / "websites"
        / "dadudekc.com"
        / "overlays"
        / "wp"
        / "theme"
        / THEME_NAME
    )


def deploy_theme_directory(deployer: SimpleWordPressDeployer, theme_path: Path) -> bool:
    if not theme_path.exists():
        print(f"❌ Theme path missing: {theme_path}")
        return False

    deployed = 0
    failed = 0

    for local_file in sorted(theme_path.rglob("*")):
        if local_file.is_dir():
            continue
        relative_path = local_file.relative_to(theme_path).as_posix()
        remote_path = f"{REMOTE_THEME_BASE}/{relative_path}"
        result = deployer.deploy_file(local_file, remote_path)
        if result:
            deployed += 1
        else:
            failed += 1
            print(f"❌ Failed to deploy {local_file}")

    print(f"✅ Theme deploy complete. Deployed: {deployed}, Failed: {failed}")
    return failed == 0


def verify_homepage_marker() -> None:
    try:
        response = requests.get("https://dadudekc.com", timeout=15)
        response.raise_for_status()
        if MARKER_TEXT in response.text:
            print("✅ Deploy verification: homepage marker found.")
        else:
            print("❌ Deploy verification failed: marker not found.")
    except requests.RequestException as exc:
        print(f"⚠️ Deploy verification request failed: {exc}")


def main() -> None:
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("dadudekc.com", site_configs)
    theme_path = get_local_theme_path()

    try:
        if not deployer.connect():
            return
        if deploy_theme_directory(deployer, theme_path):
            verify_homepage_marker()
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    main()

