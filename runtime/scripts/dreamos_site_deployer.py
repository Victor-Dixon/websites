#!/usr/bin/env python3
"""
runtime/scripts/dreamos_site_deployer.py

One Dream.OS website deployer for Hostinger-style sites.

Modes:
- auto: detect WordPress by wp-config.php; use WP page if present, static otherwise
- static: upload HTML to public_html/<remote-rel>
- wp-page: strip standalone wrappers and upsert WordPress page through wp-cli

Required env keys:
- HOSTINGER_SSH_PRIVATE_KEY_FILE
- HOSTINGER_USER
- HOSTINGER_HOST
- HOSTINGER_PORT
Optional:
- DOMAIN
- HOSTINGER_WP_ROOT
"""

from __future__ import annotations

import argparse
import html
import os
import re
import shlex
import subprocess
import sys
from pathlib import Path


REQUIRED = [
    "HOSTINGER_SSH_PRIVATE_KEY_FILE",
    "HOSTINGER_USER",
    "HOSTINGER_HOST",
    "HOSTINGER_PORT",
]


def load_env(path: Path) -> dict[str, str]:
    if not path.exists():
        raise SystemExit(f"ENV_MISSING={path}")

    env: dict[str, str] = {}
    for raw in path.read_text().splitlines():
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        env[key.strip()] = value.strip().strip("'").strip('"')
    return env


def require_env(env: dict[str, str]) -> None:
    missing = [k for k in REQUIRED if not env.get(k)]
    if missing:
        raise SystemExit("MISSING_ENV=" + ",".join(missing))


def expand_home(value: str) -> str:
    return value.replace("$HOME", str(Path.home()))


def run(cmd: list[str], input_text: str | None = None, check: bool = True) -> subprocess.CompletedProcess:
    proc = subprocess.run(
        cmd,
        input=input_text,
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.STDOUT,
    )
    print(proc.stdout, end="")
    if check and proc.returncode != 0:
        raise SystemExit(proc.returncode)
    return proc


def ssh_base(env: dict[str, str]) -> list[str]:
    return [
        "ssh",
        "-o", "LogLevel=ERROR",
        "-i", expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
        "-p", env["HOSTINGER_PORT"],
        f'{env["HOSTINGER_USER"]}@{env["HOSTINGER_HOST"]}',
    ]


def scp_base(env: dict[str, str]) -> list[str]:
    return [
        "scp",
        "-q",
        "-P", env["HOSTINGER_PORT"],
        "-i", expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
    ]


def public_root(env: dict[str, str], domain: str) -> str:
    if env.get("HOSTINGER_WP_ROOT"):
        root = env["HOSTINGER_WP_ROOT"]
        # If the env root obviously points to another domain, prefer explicit domain root.
        if f"/domains/{domain}/" in root:
            return root
    return f"/home/{env['HOSTINGER_USER']}/domains/{domain}/public_html"


def remote_shell(env: dict[str, str], shell: str, check: bool = True) -> subprocess.CompletedProcess:
    return run(ssh_base(env) + [shell], check=check)


def remote_exists(env: dict[str, str], remote_path: str) -> bool:
    proc = remote_shell(env, "test -e " + shlex.quote(remote_path), check=False)
    return proc.returncode == 0


def is_wordpress(env: dict[str, str], root: str) -> bool:
    return remote_exists(env, f"{root}/wp-config.php")


def extract_wp_fragment(source: Path, dest: Path) -> None:
    text = source.read_text()

    style = re.search(r"<style[^>]*>(.*?)</style>", text, re.S | re.I)
    body = re.search(r"<body[^>]*>(.*?)</body>", text, re.S | re.I)

    if not style:
        raise SystemExit("STYLE_PARSE=FAIL")
    if not body:
        raise SystemExit("BODY_PARSE=FAIL")

    content = f"""<style>
{style.group(1).strip()}
</style>

{body.group(1).strip()}
"""
    content = re.sub(r"(?is)<!doctype[^>]*>", "", content)
    content = re.sub(r"(?is)</?html[^>]*>", "", content)
    content = re.sub(r"(?is)<head[^>]*>.*?</head>", "", content)
    content = re.sub(r"(?is)</?body[^>]*>", "", content)

    bad_patterns = {
        "DOCTYPE": r"(?is)<!doctype\b",
        "HTML_OPEN": r"(?is)<html\b",
        "HTML_CLOSE": r"(?is)</html>",
        "HEAD_OPEN": r"(?is)<head\b",
        "HEAD_CLOSE": r"(?is)</head>",
        "BODY_OPEN": r"(?is)<body\b",
        "BODY_CLOSE": r"(?is)</body>",
    }
    bad = [name for name, pat in bad_patterns.items() if re.search(pat, content)]
    if bad:
        raise SystemExit("WP_FRAGMENT_WRAPPER_SCAN=FAIL " + ",".join(bad))

    dest.parent.mkdir(parents=True, exist_ok=True)
    dest.write_text(content.strip() + "\n")


def static_deploy(env: dict[str, str], source: Path, root: str, remote_rel: str) -> None:
    remote_file = f"{root}/{remote_rel}".replace("//", "/")
    remote_dir = str(Path(remote_file).parent)
    print(f"STATIC_REMOTE_FILE={remote_file}")
    remote_shell(env, "mkdir -p " + shlex.quote(remote_dir))
    run(scp_base(env) + [str(source), f'{env["HOSTINGER_USER"]}@{env["HOSTINGER_HOST"]}:{remote_file}'])
    remote_shell(
        env,
        "test -s "
        + shlex.quote(remote_file)
        + " && grep -q 'The Emergence' "
        + shlex.quote(remote_file),
    )
    print("STATIC_DEPLOY=PASS")


def wp_upsert(env: dict[str, str], fragment: Path, root: str, title: str, slug: str, status: str) -> None:
    content = fragment.read_text()
    remote_file = f"/tmp/dreamos_wp_content_{slug}.html"

    # Find page ID.
    list_cmd = (
        "cd "
        + shlex.quote(root)
        + " && wp post list --post_type=page "
        + shlex.quote(f"--name={slug}")
        + " --field=ID --format=ids --skip-plugins --skip-themes"
    )
    proc = remote_shell(env, list_cmd, check=False)
    ids = re.findall(r"\b\d+\b", proc.stdout)
    page_id = ids[-1] if ids else ""

    if page_id:
        print(f"PAGE_EXISTS=PASS id={page_id}")
        wp_cmd = (
            "cat > "
            + shlex.quote(remote_file)
            + " && cd "
            + shlex.quote(root)
            + " && wp post update "
            + shlex.quote(page_id)
            + " "
            + shlex.quote(f"--post_title={title}")
            + " "
            + shlex.quote(f"--post_name={slug}")
            + " "
            + shlex.quote(f"--post_status={status}")
            + " "
            + '"--post_content=$(cat '
            + shlex.quote(remote_file)
            + ')"'
            + " --skip-plugins --skip-themes"
            + " ; rc=$?; rm -f "
            + shlex.quote(remote_file)
            + " ; exit $rc"
        )
    else:
        print("PAGE_EXISTS=NO")
        wp_cmd = (
            "cat > "
            + shlex.quote(remote_file)
            + " && cd "
            + shlex.quote(root)
            + " && wp post create --post_type=page "
            + shlex.quote(f"--post_title={title}")
            + " "
            + shlex.quote(f"--post_name={slug}")
            + " "
            + shlex.quote(f"--post_status={status}")
            + " "
            + '"--post_content=$(cat '
            + shlex.quote(remote_file)
            + ')"'
            + " --porcelain --skip-plugins --skip-themes"
            + " ; rc=$?; rm -f "
            + shlex.quote(remote_file)
            + " ; exit $rc"
        )

    run(ssh_base(env) + [wp_cmd], input_text=content)
    print("WP_UPSERT=PASS")


def http_verify(url: str, needles: list[str]) -> None:
    if not url:
        print("HTTP_VERIFY=SKIPPED_NO_URL")
        return

    try:
        import urllib.request

        with urllib.request.urlopen(url, timeout=20) as res:
            status = res.status
            body = res.read().decode("utf-8", errors="replace")
        print(f"HTTP_STATUS={status}")
        if status != 200:
            raise SystemExit(f"HTTP_NON_200={status}")
        for needle in needles:
            if needle not in body:
                raise SystemExit(f"HTTP_MISSING_NEEDLE={needle}")
        print("HTTP_VERIFY=PASS")
    except Exception as exc:
        raise SystemExit(f"HTTP_VERIFY=FAIL {exc}")


def main() -> int:
    ap = argparse.ArgumentParser(description="Dream.OS streamlined Hostinger website deployer")
    ap.add_argument("--env", required=True)
    ap.add_argument("--domain", required=True)
    ap.add_argument("--source", required=True)
    ap.add_argument("--mode", choices=["auto", "static", "wp-page"], default="auto")
    ap.add_argument("--remote-rel", default="emergence-preview/index.html")
    ap.add_argument("--title", default="The Emergence")
    ap.add_argument("--slug", default="emergence-preview")
    ap.add_argument("--status", default="publish")
    ap.add_argument("--verify-url", default="")
    args = ap.parse_args()

    env = load_env(Path(args.env))
    require_env(env)

    source = Path(args.source)
    if not source.exists():
        raise SystemExit(f"SOURCE_MISSING={source}")

    root = public_root(env, args.domain)
    print(f"DOMAIN={args.domain}")
    print(f"REMOTE_ROOT={root}")

    print("== SSH CHECK ==")
    remote_shell(env, "echo HOSTINGER_SSH_LOGIN=PASS && pwd")

    wp_present = is_wordpress(env, root)
    print(f"WORDPRESS_DETECTED={'PASS' if wp_present else 'NO'}")

    mode = args.mode
    if mode == "auto":
        mode = "wp-page" if wp_present else "static"

    print(f"DEPLOY_MODE={mode}")

    if mode == "wp-page":
        if not wp_present:
            raise SystemExit("WP_MODE_REQUESTED_BUT_WP_CONFIG_MISSING")
        fragment = Path("_hostinger_build/dreamos_site_deployer/fragments") / f"{args.slug}.wp.html"
        extract_wp_fragment(source, fragment)
        print(f"WP_FRAGMENT={fragment}")
        wp_upsert(env, fragment, root, args.title, args.slug, args.status)
    else:
        static_deploy(env, source, root, args.remote_rel)

    if args.verify_url:
        http_verify(args.verify_url, ["The Emergence", "What-If Arena", "Generate Your Spark"])

    print("STATUS=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
