#!/usr/bin/env python3
"""
Cleanup ariajet.site: remove "Irish cafe" content + blank About page
===================================================================

What it does (when run with --apply):
- Finds pages/posts that mention any of the target terms (default: "irish cafe", "irish", "cafe")
  and moves them to trash (or permanently deletes with --force-delete).
- Removes nav menu items that contain those terms.
- Blanks the About page editor content, sets comments closed, and flushes cache/rewrite rules.

Safety:
- Default mode is DRY RUN (no changes).
- When applying, it writes a local JSON backup of matched items to `runtime/`.

Requires:
- SFTP/SSH credentials available via `/workspace/.env` (see `.env.example`)
- WP-CLI installed on the server
"""

from __future__ import annotations

import json
import os
import sys
from dataclasses import dataclass
from datetime import datetime
from pathlib import Path


REPO_ROOT = Path(__file__).resolve().parents[1]
RUNTIME_DIR = REPO_ROOT / "runtime"
SITE = "ariajet.site"


def _load_dotenv_if_available() -> None:
    try:
        from dotenv import load_dotenv  # type: ignore
    except Exception:
        return
    env_path = REPO_ROOT / ".env"
    if env_path.exists():
        load_dotenv(env_path, override=False)


def _get(name: str) -> str:
    return (os.environ.get(name) or "").strip()


@dataclass(frozen=True)
class FoundPost:
    id: int
    post_type: str
    post_status: str
    post_title: str
    post_name: str


def _json_from_wpcli(output: str) -> list[dict]:
    output = (output or "").strip()
    if not output:
        return []
    try:
        return json.loads(output)
    except Exception:
        # Sometimes WP-CLI prints warnings before JSON; try to salvage last JSON block.
        start = output.find("[")
        end = output.rfind("]")
        if start >= 0 and end >= start:
            return json.loads(output[start : end + 1])
        return []


def _detect_wp_path(*, deployer, site_domain: str) -> str:
    """
    Determine the WordPress root path on the remote server.
    """
    # Ask remote for $HOME to avoid guessing server usernames.
    home = (deployer.execute_command("echo $HOME 2>/dev/null") or "").strip()
    if not home:
        home = "/home"

    candidates: list[str] = []

    remote_path = getattr(deployer, "remote_path", "") or ""
    if remote_path:
        candidates.append(f"{home.rstrip('/')}/{remote_path}".rstrip("/"))

    candidates.extend(
        [
            f"{home.rstrip('/')}/domains/{site_domain}/public_html",
            f"{home.rstrip('/')}/public_html",
        ]
    )

    for base in candidates:
        chk = deployer.execute_command(f"test -f {base}/wp-config.php && echo OK 2>/dev/null")
        if (chk or "").strip() == "OK":
            return base

    # As a last resort, return best guess (helps error messages).
    return candidates[0] if candidates else ""


def _wp(*, deployer, wp_path: str, cmd: str) -> str:
    # Always run inside WP root so WP-CLI finds config.
    return deployer.execute_command(f"cd {wp_path} && {cmd} 2>&1")


def _find_posts(*, deployer, wp_path: str, post_type: str, term: str) -> list[FoundPost]:
    out = _wp(
        deployer=deployer,
        wp_path=wp_path,
        cmd=(
            "wp post list "
            f"--post_type={post_type} "
            f"--search={json.dumps(term)} "
            "--fields=ID,post_title,post_name,post_type,post_status "
            "--format=json"
        ),
    )
    rows = _json_from_wpcli(out)
    found: list[FoundPost] = []
    for r in rows:
        try:
            found.append(
                FoundPost(
                    id=int(r.get("ID")),
                    post_title=str(r.get("post_title") or ""),
                    post_name=str(r.get("post_name") or ""),
                    post_type=str(r.get("post_type") or ""),
                    post_status=str(r.get("post_status") or ""),
                )
            )
        except Exception:
            continue
    return found


def _menu_items(*, deployer, wp_path: str) -> list[dict]:
    menus_out = _wp(deployer=deployer, wp_path=wp_path, cmd="wp menu list --format=json")
    menus = _json_from_wpcli(menus_out)
    items: list[dict] = []
    for m in menus:
        term_id = m.get("term_id") or m.get("term_id ")
        if not term_id:
            continue
        items_out = _wp(
            deployer=deployer,
            wp_path=wp_path,
            cmd=f"wp menu item list {term_id} --format=json --fields=db_id,title,url",
        )
        for it in _json_from_wpcli(items_out):
            it["_menu_term_id"] = term_id
            items.append(it)
    return items


def main(argv: list[str]) -> int:
    import argparse

    _load_dotenv_if_available()

    parser = argparse.ArgumentParser(description="Cleanup ariajet.site content (Irish cafe + About).")
    parser.add_argument("--apply", action="store_true", help="Actually apply changes (default is dry-run).")
    parser.add_argument("--force-delete", action="store_true", help="Permanently delete matched posts (instead of trash).")
    parser.add_argument(
        "--terms",
        nargs="*",
        default=["irish cafe", "irish", "cafe"],
        help="Search terms to remove (default: irish cafe irish cafe).",
    )
    args = parser.parse_args(argv)

    try:
        from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except Exception as e:
        print(f"❌ Could not import deployer: {e}")
        return 2

    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(SITE, site_configs)
    if not deployer.connect():
        return 1

    try:
        wp_path = _detect_wp_path(deployer=deployer, site_domain=SITE)
        if not wp_path:
            print("❌ Could not determine WordPress path on server.")
            return 1

        print(f"🔎 Target site: {SITE}")
        print(f"📂 WordPress path: {wp_path}")
        print(f"🧪 Mode: {'APPLY' if args.apply else 'DRY RUN'}")

        # 1) About page: blank content + close comments
        about_ids_raw = _wp(deployer=deployer, wp_path=wp_path, cmd="wp post list --post_type=page --name=about --format=ids")
        about_ids = [p for p in (about_ids_raw or "").strip().split() if p.isdigit()]
        if about_ids:
            print(f"\n🧹 About page IDs: {', '.join(about_ids)}")
            if args.apply:
                for pid in about_ids:
                    _wp(deployer=deployer, wp_path=wp_path, cmd=f"wp post update {pid} --post_content='' --post_excerpt='' --comment_status=closed")
                    # Some themes force page templates by slug, but this ensures WP itself isn't injecting extras.
                    _wp(deployer=deployer, wp_path=wp_path, cmd=f"wp post meta delete {pid} _wp_page_template")
                print("✅ About page content cleared and comments closed.")
        else:
            print("\nℹ️  No About page found with slug 'about'.")

        # 2) Find matching posts/pages
        matched: dict[int, FoundPost] = {}
        for term in args.terms:
            for pt in ("page", "post"):
                for fp in _find_posts(deployer=deployer, wp_path=wp_path, post_type=pt, term=term):
                    matched[fp.id] = fp

        if matched:
            print(f"\n🧽 Matched posts/pages ({len(matched)}):")
            for fp in sorted(matched.values(), key=lambda x: (x.post_type, x.id)):
                print(f" - [{fp.post_type}] #{fp.id} ({fp.post_status}) {fp.post_title} (/{fp.post_name})")
        else:
            print("\n✅ No posts/pages matched those terms.")

        # 3) Backup + delete/trash
        if args.apply and matched:
            RUNTIME_DIR.mkdir(parents=True, exist_ok=True)
            stamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            backup_path = RUNTIME_DIR / f"ariajet_cleanup_backup_{stamp}.json"
            backup: dict[str, object] = {
                "site": SITE,
                "timestamp": datetime.now().isoformat(),
                "terms": list(args.terms),
                "matched": [fp.__dict__ for fp in matched.values()],
            }
            backup_path.write_text(json.dumps(backup, indent=2), encoding="utf-8")
            print(f"\n💾 Backup written: {backup_path}")

            for fp in matched.values():
                force = " --force" if args.force_delete else ""
                _wp(deployer=deployer, wp_path=wp_path, cmd=f"wp post delete {fp.id}{force}")
            print(f"✅ {'Deleted' if args.force_delete else 'Trashed'} matched posts/pages.")

        # 4) Menu items containing terms
        items = _menu_items(deployer=deployer, wp_path=wp_path)
        terms_lc = [t.lower() for t in args.terms]
        bad_items: list[dict] = []
        for it in items:
            title = str(it.get("title") or "").lower()
            url = str(it.get("url") or "").lower()
            if any(t in title or t in url for t in terms_lc):
                bad_items.append(it)

        if bad_items:
            print(f"\n🧭 Matched menu items ({len(bad_items)}):")
            for it in bad_items:
                print(f" - #{it.get('db_id')} title={it.get('title')!r} url={it.get('url')!r}")
            if args.apply:
                for it in bad_items:
                    db_id = it.get("db_id")
                    if db_id:
                        _wp(deployer=deployer, wp_path=wp_path, cmd=f"wp menu item delete {db_id} --force")
                print("✅ Removed matched menu items.")
        else:
            print("\n✅ No menu items matched those terms.")

        # 5) Flush cache/rewrite
        if args.apply:
            _wp(deployer=deployer, wp_path=wp_path, cmd="wp cache flush")
            _wp(deployer=deployer, wp_path=wp_path, cmd="wp rewrite flush")
            _wp(deployer=deployer, wp_path=wp_path, cmd="wp transient delete --all")
            print("\n🧹 Cache + rewrites flushed.")

        return 0
    finally:
        try:
            deployer.disconnect()
        except Exception:
            pass


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))

