#!/usr/bin/env python3
"""
Publish a blog post using site configs as SSOT.

Credential priority:
1) config/site_configs.json (rest_api block)
2) .deploy_credentials/blogging_api.json
3) .deploy_credentials/sites.json
"""

import argparse
import json
from pathlib import Path
from typing import Dict, Any, Optional, Tuple

from tools.blog.unified_blogging_automation import (
    WordPressBlogClient,
    ContentAdapter,
    HAS_REQUESTS,
    logger,
)

REPO_ROOT = Path(__file__).resolve().parents[2]
SITE_CONFIGS_JSON = REPO_ROOT / "config" / "site_configs.json"
BLOGGING_API_JSON = REPO_ROOT / ".deploy_credentials" / "blogging_api.json"
SITES_JSON = REPO_ROOT / ".deploy_credentials" / "sites.json"


def load_json(path: Path) -> Dict[str, Any]:
    if not path.exists():
        return {}
    return json.loads(path.read_text(encoding="utf-8"))


def resolve_credentials(site_key: str) -> Optional[Tuple[str, str, str]]:
    site_configs = load_json(SITE_CONFIGS_JSON)
    site_config = site_configs.get(site_key)
    if site_config and site_config.get("rest_api"):
        rest_api = site_config["rest_api"]
        site_url = rest_api.get("site_url") or site_config.get("site_url")
        username = rest_api.get("username")
        app_password = rest_api.get("app_password")
        if site_url and username and app_password:
            return site_url, username, app_password

    blogging_api = load_json(BLOGGING_API_JSON)
    blog_config = blogging_api.get(site_key)
    if blog_config:
        site_url = blog_config.get("site_url")
        username = blog_config.get("username")
        app_password = blog_config.get("app_password")
        if site_url and username and app_password:
            return site_url, username, app_password

    sites_json = load_json(SITES_JSON)
    sites_config = sites_json.get(site_key)
    if sites_config:
        site_url = sites_config.get("site_url")
        username = sites_config.get("username")
        app_password = sites_config.get("app_password")
        if site_url and username and app_password:
            return site_url, username, app_password

    return None


def load_content(content_arg: str) -> str:
    content_path = Path(content_arg)
    if content_path.exists():
        return content_path.read_text(encoding="utf-8")
    return content_arg


def main() -> int:
    parser = argparse.ArgumentParser(description="Publish a blog post using site_configs.json as SSOT")
    parser.add_argument("--site", required=True, help="Site key (e.g., trading)")
    parser.add_argument("--title", required=True, help="Post title")
    parser.add_argument("--content", required=True, help="Post content or file path")
    parser.add_argument("--purpose", help="Site purpose for content adaptation")
    parser.add_argument("--status", default="draft", choices=["draft", "publish"], help="Post status")
    parser.add_argument("--dry-run", action="store_true", help="Dry run (no actual posting)")
    args = parser.parse_args()

    if not HAS_REQUESTS:
        logger.error("❌ requests library required. Install with: pip install requests")
        return 1

    credentials = resolve_credentials(args.site)
    if not credentials:
        logger.error(
            "❌ No credentials found for site '%s'. Expected in config/site_configs.json, "
            ".deploy_credentials/blogging_api.json, or .deploy_credentials/sites.json.",
            args.site,
        )
        return 1

    site_url, username, app_password = credentials
    client = WordPressBlogClient(site_url, username, app_password)

    content = load_content(args.content)
    categories = []
    tags = []
    if args.purpose:
        adapted = ContentAdapter.adapt_content(content, args.purpose)
        categories = adapted["categories"]
        tags = adapted["tags"]

    if args.dry_run:
        logger.info("✅ Dry run: would publish '%s' to %s", args.title, args.site)
        logger.info("Categories: %s", categories)
        logger.info("Tags: %s", tags)
        return 0

    result = client.create_post(
        title=args.title,
        content=content,
        categories=categories,
        tags=tags,
        status=args.status,
    )

    if result.get("success"):
        logger.info("✅ Published '%s' to %s", args.title, args.site)
        logger.info("Post ID: %s", result.get("post_id"))
        logger.info("Link: %s", result.get("link"))
        return 0

    logger.error("❌ Publish failed: %s", result.get("error"))
    return 1


if __name__ == "__main__":
    raise SystemExit(main())
