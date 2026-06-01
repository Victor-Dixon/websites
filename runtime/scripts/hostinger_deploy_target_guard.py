#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
import os
import shlex
import subprocess
from dataclasses import asdict, dataclass
from pathlib import Path


@dataclass
class RemoteSite:
    domain: str
    root: str
    exists: bool
    install_type: str
    has_wp_config: bool
    has_wp_admin: bool
    has_wp_content: bool
    has_index_php: bool
    has_index_html: bool
    has_htaccess: bool


def load_env(path: Path) -> dict[str, str]:
    env: dict[str, str] = {}
    for line in path.read_text().splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, v = line.split("=", 1)
        env[k.strip()] = v.strip().strip('"').strip("'")
    return env


def ssh_cmd(env: dict[str, str], remote_script: str) -> str:
    cmd = [
        "ssh",
        "-i",
        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
        "-p",
        env["HOSTINGER_PORT"],
        "-o",
        "StrictHostKeyChecking=accept-new",
        "-o",
        "BatchMode=yes",
        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}",
        remote_script,
    ]
    proc = subprocess.run(cmd, check=True, capture_output=True, text=True)
    return proc.stdout


def classify_site(domain: str, root: str, flags: dict[str, bool]) -> str:
    if flags["has_wp_config"] and flags["has_wp_admin"] and flags["has_wp_content"]:
        return "wordpress"
    if flags["has_wp_config"] or flags["has_wp_admin"] or flags["has_wp_content"] or flags["has_index_php"]:
        return "partial_or_php"
    if flags["has_index_html"]:
        return "static"
    if flags["exists"]:
        return "empty_or_placeholder"
    return "missing"


def inspect_remote_sites(env: dict[str, str]) -> list[RemoteSite]:
    remote = r'''
set -e
BASE="$HOME/domains"
if [ ! -d "$BASE" ]; then
  exit 0
fi

for d in "$BASE"/*; do
  [ -d "$d" ] || continue
  domain="$(basename "$d")"
  root="$d/public_html"

  exists=0
  has_wp_config=0
  has_wp_admin=0
  has_wp_content=0
  has_index_php=0
  has_index_html=0
  has_htaccess=0

  [ -d "$root" ] && exists=1
  [ -f "$root/wp-config.php" ] && has_wp_config=1
  [ -d "$root/wp-admin" ] && has_wp_admin=1
  [ -d "$root/wp-content" ] && has_wp_content=1
  [ -f "$root/index.php" ] && has_index_php=1
  [ -f "$root/index.html" ] && has_index_html=1
  [ -f "$root/.htaccess" ] && has_htaccess=1

  printf '%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n' \
    "$domain" "$root" "$exists" "$has_wp_config" "$has_wp_admin" "$has_wp_content" "$has_index_php" "$has_index_html" "$has_htaccess"
done
'''
    out = ssh_cmd(env, remote)
    sites: list[RemoteSite] = []

    for line in out.splitlines():
        parts = line.split("\t")
        if len(parts) != 9:
            continue

        domain, root, exists, wp_config, wp_admin, wp_content, index_php, index_html, htaccess = parts
        flags = {
            "exists": exists == "1",
            "has_wp_config": wp_config == "1",
            "has_wp_admin": wp_admin == "1",
            "has_wp_content": wp_content == "1",
            "has_index_php": index_php == "1",
            "has_index_html": index_html == "1",
            "has_htaccess": htaccess == "1",
        }

        sites.append(
            RemoteSite(
                domain=domain,
                root=root,
                exists=flags["exists"],
                install_type=classify_site(domain, root, flags),
                has_wp_config=flags["has_wp_config"],
                has_wp_admin=flags["has_wp_admin"],
                has_wp_content=flags["has_wp_content"],
                has_index_php=flags["has_index_php"],
                has_index_html=flags["has_index_html"],
                has_htaccess=flags["has_htaccess"],
            )
        )

    return sorted(sites, key=lambda s: s.domain)


def require_exact_target(
    sites: list[RemoteSite],
    *,
    domain: str,
    expected_type: str | None,
) -> RemoteSite:
    matches = [s for s in sites if s.domain == domain]

    if not matches:
        available = ", ".join(s.domain for s in sites)
        raise SystemExit(f"TARGET_DOMAIN_NOT_FOUND={domain}\nAVAILABLE_DOMAINS={available}")

    site = matches[0]

    if expected_type and site.install_type != expected_type:
        raise SystemExit(
            f"TARGET_INSTALL_TYPE_MISMATCH domain={domain} "
            f"expected={expected_type} actual={site.install_type} root={site.root}"
        )

    return site


def main() -> None:
    parser = argparse.ArgumentParser(description="Hostinger deploy target guard")
    parser.add_argument("--env", default=str(Path.home() / ".config/dreamos/hostinger_freeride_github_secrets.env"))
    parser.add_argument("--domain", help="Exact domain to validate")
    parser.add_argument("--expect", choices=["wordpress", "static", "partial_or_php", "empty_or_placeholder", "missing"])
    parser.add_argument("--json", action="store_true")
    args = parser.parse_args()

    env = load_env(Path(args.env))
    required = [
        "HOSTINGER_HOST",
        "HOSTINGER_USER",
        "HOSTINGER_PORT",
        "HOSTINGER_SSH_PRIVATE_KEY_FILE",
    ]

    for key in required:
        if not env.get(key):
            raise SystemExit(f"MISSING_ENV={key}")

    sites = inspect_remote_sites(env)

    if args.json:
        print(json.dumps([asdict(s) for s in sites], indent=2))
    else:
        print("domain\tinstall_type\troot\twp_config\twp_admin\twp_content\tindex_php\tindex_html\thtaccess")
        for s in sites:
            print(
                f"{s.domain}\t{s.install_type}\t{s.root}\t"
                f"{int(s.has_wp_config)}\t{int(s.has_wp_admin)}\t{int(s.has_wp_content)}\t"
                f"{int(s.has_index_php)}\t{int(s.has_index_html)}\t{int(s.has_htaccess)}"
            )

    if args.domain:
        site = require_exact_target(sites, domain=args.domain, expected_type=args.expect)
        print(f"TARGET_GUARD=PASS domain={site.domain} type={site.install_type} root={site.root}")


if __name__ == "__main__":
    main()
