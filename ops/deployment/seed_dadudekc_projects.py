#!/usr/bin/env python3
"""
Seed Projects for dadudekc.com
==============================

Creates initial project CPT posts so /projects/ is not empty.
Uses WP-CLI over SSH (same pattern as publish_post_wpcli).

Usage:
    python ops/deployment/seed_dadudekc_projects.py
"""

import json
import os
import sys
from pathlib import Path
from typing import Tuple

sys.path.insert(0, str(Path(__file__).parent))
try:
    import paramiko
    from dotenv import load_dotenv
except ImportError:
    print("pip install paramiko python-dotenv")
    sys.exit(1)


def load_credentials(site_domain: str):
    """Load SSH credentials; prefer sites.json, then .env + site_configs."""
    load_dotenv(Path("D:/Agent_Cellphone_V2_Repository/.env"))
    load_dotenv(Path(".env"))
    host = os.getenv("HOSTINGER_HOST")
    user = os.getenv("HOSTINGER_USER")
    pw = os.getenv("HOSTINGER_PASS")
    port = int(os.getenv("HOSTINGER_PORT", "65002"))
    remote = f"domains/{site_domain}/public_html"

    sites_json = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if sites_json.exists():
        try:
            with open(sites_json) as f:
                s = json.load(f).get(site_domain, {})
            return {
                "host": s.get("host") or host,
                "username": s.get("username") or user,
                "password": s.get("password") or pw,
                "port": s.get("port", port),
                "remote_path": s.get("remote_path", remote),
            }
        except Exception:
            pass

    config_path = Path(__file__).resolve().parents[2] / "config" / "site_configs.json"
    if config_path.exists():
        try:
            with open(config_path) as f:
                c = json.load(f).get(site_domain, {})
            sftp = c.get("sftp", {})
            return {
                "host": sftp.get("host") or host,
                "username": sftp.get("username") or user,
                "password": sftp.get("password") or pw,
                "port": sftp.get("port", port),
                "remote_path": sftp.get("remote_path", remote),
            }
        except Exception:
            pass

    if all([host, user, pw]):
        return {"host": host, "username": user, "password": pw, "port": port, "remote_path": remote}
    return None


def sh(s: str) -> str:
    """Escape for shell (single-quoted)."""
    return s.replace("'", "'\"'\"'")


def run_wp(ssh, wp_path: str, cmd: str) -> Tuple[bool, str]:
    full = f"cd {wp_path} && wp {cmd} --allow-root 2>&1"
    stdin, stdout, stderr = ssh.exec_command(full, timeout=30)
    out = stdout.read().decode().strip()
    err = stderr.read().decode().strip()
    raw = out or err
    ok = "Error" not in raw and "Fatal" not in raw
    return ok, raw


SEED_PROJECTS = [
    {
        "title": "Project Scanner",
        "content": "A Python tool that scans codebases and transforms repositories into structured JSON data with a PyQt5 GUI interface.",
        "meta": {
            "_project_one_line_summary": "A Python tool that scans codebases and transforms repositories into structured JSON data with a PyQt5 GUI interface.",
            "_project_tech_stack": "Python, PyQt5, AST, Tree-sitter, JSON",
            "_project_status": "mvp",
        },
    },
    {
        "title": "Websites Deployment System",
        "content": "Registry-driven WordPress deployment for multiple production sites. SSOT site registry, deploy-stamp verification, SFTP + CI.",
        "meta": {
            "_project_one_line_summary": "Registry-driven WordPress deployment with deploy-stamp verification across multiple production sites.",
            "_project_tech_stack": "Python, SFTP, GitHub Actions, YAML",
            "_project_status": "active",
        },
    },
    {
        "title": "HC Shinobi",
        "content": "Discord bot RPG: training, missions, jutsu, clans, economy. Built with discord.py.",
        "meta": {
            "_project_one_line_summary": "Discord bot RPG with training, missions, jutsu, clans, and economy.",
            "_project_tech_stack": "Python, discord.py, JSON",
            "_project_status": "active",
        },
    },
]


def fix_projects_page_conflict(ssh, wp_path: str) -> None:
    """If a Page has slug 'projects', WordPress serves it at /projects/ instead of the CPT archive. Rename it."""
    # WP-CLI: --name=projects filters by post_name
    ok, out = run_wp(ssh, wp_path, "post list --post_type=page --name=projects --format=json --fields=ID")
    if not ok or not out.strip() or out.strip() == "[]":
        return
    try:
        pages = json.loads(out)
        for p in pages:
            pid = p.get("ID")
            if not pid:
                continue
            print(f"📄 Found Page with slug 'projects' (ID {pid}); renaming to projects-page so /projects/ can be the project archive.")
            run_wp(ssh, wp_path, f"post update {pid} --post_name=projects-page --post_title='Projects (Page)'")
            print("   ✅ Page slug updated.")
            break
    except json.JSONDecodeError:
        pass


def main():
    site = "dadudekc.com"
    creds = load_credentials(site)
    if not creds:
        print("❌ No credentials for dadudekc.com")
        return 1

    wp_path = f"/home/{creds['username']}/{creds['remote_path']}"
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(creds["host"], port=creds["port"], username=creds["username"], password=creds["password"], timeout=10)

    # 1. Ensure no Page uses slug "projects" (so /projects/ is the CPT archive)
    fix_projects_page_conflict(ssh, wp_path)

    # Diagnose: list any page with "projects" in slug (WordPress prefers page over CPT for same slug)
    ok, pages_out = run_wp(ssh, wp_path, "post list --post_type=page --format=json --fields=ID,post_name,post_title")
    if ok and pages_out.strip() and pages_out.strip() != "[]":
        try:
            for p in json.loads(pages_out):
                name = str(p.get("post_name") or "")
                if name == "projects":
                    print(f"⚠️  Page ID {p.get('ID')} still has slug 'projects' – updating again.")
                    run_wp(ssh, wp_path, f"post update {p.get('ID')} --post_name=projects-page --post_title='Projects (Page)'")
        except json.JSONDecodeError:
            pass

    ok, out = run_wp(ssh, wp_path, "post list --post_type=project --post_status=publish --format=json")
    if ok and out.strip() and out.strip() != "[]":
        try:
            existing = json.loads(out)
            if len(existing) >= 1:
                print(f"✅ Found {len(existing)} published project(s); skipping seed.")
                run_wp(ssh, wp_path, "rewrite flush")
                run_wp(ssh, wp_path, "cache flush")
                print("   ✅ Rewrite rules and cache flushed.")
                ssh.close()
                return 0
        except json.JSONDecodeError:
            pass

    print("🌱 Seeding projects…")
    for p in SEED_PROJECTS:
        title_esc = sh(p["title"])
        content_esc = sh(p["content"])
        ok, out = run_wp(
            ssh,
            wp_path,
            f"post create --post_type=project --post_status=publish --post_title='{title_esc}' --post_content='{content_esc}'",
        )
        if not ok:
            print(f"❌ Failed: {p['title']}\n{out[:400]}")
            continue
        pid = None
        for line in out.splitlines():
            if "Success:" in line or "Created post" in line or "post" in line.lower():
                for part in line.replace(",", " ").replace(".", " ").split():
                    if part.isdigit():
                        pid = part
                        break
            if pid:
                break
        if not pid:
            print(f"⚠️  Created {p['title']} but could not parse post ID")
            continue
        for k, v in p.get("meta", {}).items():
            v_esc = sh(str(v))
            run_wp(ssh, wp_path, f"post meta add {pid} {k} '{v_esc}'")
        print(f"   ✅ {p['title']} (ID {pid})")

    # Flush rewrite rules and any WordPress caches so /projects/ shows current data
    run_wp(ssh, wp_path, "rewrite flush")
    print("   ✅ Rewrite rules flushed.")
    run_wp(ssh, wp_path, "cache flush")
    print("   ✅ WordPress cache flushed.")

    ssh.close()
    print("✅ Done. Visit https://dadudekc.com/projects/ (try incognito or hard refresh)")
    return 0


if __name__ == "__main__":
    sys.exit(main())
