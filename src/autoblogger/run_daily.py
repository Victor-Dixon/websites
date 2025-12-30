from __future__ import annotations

import json
import re
from datetime import datetime
from pathlib import Path
from zoneinfo import ZoneInfo

import yaml

from .llm_client import generate_markdown, load_llm_config
from .examples_loader import load_example_snippets
from .models import BrandProfile
from .paths import drafts_dir, runtime_dir
from .prompt_builder import build_prompt
from .site_config import load_site_config
from .selector import (
    get_backlog_item,
    load_backlog,
    load_state,
    mark_backlog_used,
    pick_post_id,
)
from .validator import validate_markdown
from .wp_publisher import load_wp_env, publish_wordpress_post
from .html_formatter import markdown_to_beautiful_html


def _slugify(s: str) -> str:
    s = s.lower().strip()
    s = re.sub(r"[^a-z0-9\s-]", "", s)
    s = re.sub(r"\s+", "-", s)
    s = re.sub(r"-+", "-", s)
    return s[:80].strip("-") or "post"


def _read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8")


def _load_yaml(path: Path) -> dict:
    if not path.exists():
        return {}
    return yaml.safe_load(_read_text(path)) or {}


def _ensure_dirs() -> None:
    drafts_dir().mkdir(parents=True, exist_ok=True)
    runtime_dir().mkdir(parents=True, exist_ok=True)


def _state_path(site_id: str) -> Path:
    return runtime_dir() / f"autoblogger_state__{site_id}.json"


def _write_state(site_id: str, state: dict) -> None:
    _state_path(site_id).write_text(json.dumps(state, indent=2, ensure_ascii=False), encoding="utf-8")


def _extract_frontmatter_title(md: str) -> str | None:
    # Optional: if the model emits frontmatter, parse title.
    if not md.startswith("---"):
        return None
    parts = md.split("---", 2)
    if len(parts) < 3:
        return None
    fm = yaml.safe_load(parts[1]) or {}
    title = fm.get("title")
    return str(title).strip() if title else None


def run_daily_for_site(
    *,
    site: str,
    date_override: str | None,
    timezone: str,
    auto_publish: bool,
    wp_status: str,
    dry_run: bool,
) -> int:
    site_cfg = load_site_config(site)

    tz = ZoneInfo(timezone)
    now = datetime.now(tz)
    today = now.date() if not date_override else datetime.fromisoformat(date_override).date()

    _ensure_dirs()

    voice_path = site_cfg.voice_profile_path
    brand_path = site_cfg.brand_profile_path
    backlog_path = site_cfg.backlog_path
    calendar_path = site_cfg.calendar_path

    voice_md = _read_text(voice_path)
    brand_yaml = _read_text(brand_path)
    example_snippets = load_example_snippets(site_cfg.examples_glob)

    brand = BrandProfile.from_dict(_load_yaml(brand_path))

    backlog = load_backlog(backlog_path)
    state = load_state(_state_path(site_cfg.site_id))

    post_id = pick_post_id(today, calendar_path, backlog, state)
    item = get_backlog_item(backlog, post_id)

    prompt = build_prompt(
        voice_profile_md=voice_md,
        brand_profile_yaml=brand_yaml,
        example_snippets=example_snippets,
        item=item,
    )

    # Draft path
    draft_name = f"{today.isoformat()}--{_slugify(item.title)}.md"
    draft_path = drafts_dir() / site_cfg.site_id / draft_name
    draft_path.parent.mkdir(parents=True, exist_ok=True)

    history_entry = {
        "ts": now.isoformat(),
        "site_id": site_cfg.site_id,
        "date": today.isoformat(),
        "post_id": post_id,
        "title": item.title,
        "draft_path": str(draft_path),
        "mode": "publish" if auto_publish else "queue",
    }

    try:
        if dry_run:
            # Save prompt only (so you can hand it to an agent)
            draft_path.write_text(
                "---\n"
                f"title: {item.title!r}\n"
                f"date: {today.isoformat()}\n"
                f"pillar: {item.pillar}\n"
                f"audience: {item.audience}\n"
                f"cta: {item.cta}\n"
                f"site_id: {site_cfg.site_id}\n"
                "---\n\n"
                "# DRAFT NOT GENERATED (dry-run)\n\n"
                "Below is the exact prompt that would be sent.\n\n"
                "```\n"
                + prompt.system
                + "\n\n"
                + prompt.user
                + "\n```\n",
                encoding="utf-8",
            )
            history_entry["status"] = "queued_prompt_only"
        else:
            cfg = load_llm_config()
            md = generate_markdown(prompt, cfg=cfg)

            # Enforce frontmatter (even if model didn't include it)
            existing_title = _extract_frontmatter_title(md)
            if not md.startswith("---"):
                md = (
                    "---\n"
                    f"title: {item.title!r}\n"
                    f"date: {today.isoformat()}\n"
                    f"pillar: {item.pillar}\n"
                    f"audience: {item.audience}\n"
                    f"cta: {item.cta}\n"
                    f"site_id: {site_cfg.site_id}\n"
                    "---\n\n"
                    + md
                )

            # Prefer per-site defaults; fall back to brand_profile.yaml
            wc_min = site_cfg.word_count_min or brand.word_count_min
            wc_max = site_cfg.word_count_max or brand.word_count_max

            result = validate_markdown(md, word_count_min=wc_min, word_count_max=wc_max, cta_type=item.cta)
            if not result.ok:
                raise RuntimeError("Validation failed: " + "; ".join(result.errors))

            draft_path.write_text(md, encoding="utf-8")
            history_entry["status"] = "draft_saved"
            history_entry["word_count"] = result.word_count

            if auto_publish:
                if site_cfg.publish.provider != "wordpress":
                    raise RuntimeError(f"Unsupported publish provider: {site_cfg.publish.provider}")
                if not (site_cfg.publish.wp_base_url_env and site_cfg.publish.wp_user_env and site_cfg.publish.wp_app_password_env):
                    raise RuntimeError("WordPress publish config missing env var names in site config")

                wp_env = load_wp_env(
                    base_url_env=site_cfg.publish.wp_base_url_env,
                    user_env=site_cfg.publish.wp_user_env,
                    app_password_env=site_cfg.publish.wp_app_password_env,
                )
                # Convert markdown to beautiful HTML for the Digital Dreamscape template
                html_content = markdown_to_beautiful_html(md)
                
                publish_result = publish_wordpress_post(
                    cfg=wp_env,
                    title=item.title,
                    content=html_content,
                    excerpt=item.angle,
                    status=wp_status,
                )
                history_entry["wp"] = publish_result
                history_entry["status"] = "published" if wp_status == "publish" else "wp_draft_created"

        # Update state (and optionally consume backlog)
        if not dry_run:
            mark_backlog_used(backlog_path, post_id)
            used_ids = list(dict.fromkeys((state.get("used_ids") or []) + [post_id]))
            state["used_ids"] = used_ids

        state["history"] = (state.get("history") or []) + [history_entry]
        _write_state(site_cfg.site_id, state)
        return 0

    except Exception as e:
        history_entry["status"] = "failed"
        history_entry["error"] = str(e)
        state["failures"] = (state.get("failures") or []) + [history_entry]
        state["history"] = (state.get("history") or []) + [history_entry]
        _write_state(site_cfg.site_id, state)
        raise


def main() -> int:
    import argparse

    parser = argparse.ArgumentParser(description="Autoblogger daily runner (multi-tenant)")
    parser.add_argument("--site", required=True, help="Site key (matches sites/<site>.yaml, e.g. dadudekc)")
    parser.add_argument("--date", help="Override date (YYYY-MM-DD). Default: today in America/Chicago")
    parser.add_argument("--timezone", default="America/Chicago", help="Timezone for 'today'")
    parser.add_argument("--auto-publish", action="store_true", help="Publish to WordPress (default: queue only)")
    parser.add_argument("--wp-status", default="draft", choices=["draft", "publish"], help="WP post status")
    parser.add_argument("--dry-run", action="store_true", help="Do everything except LLM + publish")

    args = parser.parse_args()

    # If --auto-publish not set, default comes from site config.
    site_cfg = load_site_config(args.site)
    auto_publish = bool(args.auto_publish) if args.auto_publish else bool(site_cfg.auto_publish_default)

    return run_daily_for_site(
        site=args.site,
        date_override=args.date,
        timezone=args.timezone,
        auto_publish=auto_publish,
        wp_status=args.wp_status,
        dry_run=args.dry_run,
    )


if __name__ == "__main__":
    raise SystemExit(main())
