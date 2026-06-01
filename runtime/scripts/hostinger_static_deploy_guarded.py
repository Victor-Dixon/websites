#!/usr/bin/env python3
from __future__ import annotations

import argparse
import os
import shlex
import subprocess
from pathlib import Path


def load_env(path: Path) -> dict[str, str]:
    env: dict[str, str] = {}
    for line in path.read_text().splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, v = line.split("=", 1)
        env[k.strip()] = v.strip().strip('"').strip("'")
    return env


def run(cmd: list[str], *, check: bool = True) -> subprocess.CompletedProcess[str]:
    return subprocess.run(cmd, check=check, capture_output=True, text=True)


def ssh(env: dict[str, str], remote: str) -> str:
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
        remote,
    ]
    return run(cmd).stdout


def scp(env: dict[str, str], local: Path, remote_path: str) -> None:
    cmd = [
        "scp",
        "-i",
        env["HOSTINGER_SSH_PRIVATE_KEY_FILE"],
        "-P",
        env["HOSTINGER_PORT"],
        "-o",
        "StrictHostKeyChecking=accept-new",
        "-o",
        "BatchMode=yes",
        str(local),
        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}:{remote_path}",
    ]
    run(cmd)


def validate_mode(domain: str, env_file: str) -> str:
    cmd = [
        "python",
        "runtime/scripts/validate_website_deploy_modes.py",
        "--env",
        env_file,
        "--domain",
        domain,
    ]
    proc = run(cmd)
    if f"TARGET_GUARD=PASS domain={domain} type=static" not in proc.stdout:
        raise SystemExit(proc.stdout + "\nSTATIC_TARGET_GUARD=FAIL")
    return proc.stdout


def remote_root_for(domain: str, user: str) -> str:
    return f"/home/{user}/domains/{domain}/public_html"


def normalize_permissions(env: dict[str, str], remote_root: str) -> None:
    remote = f"""
set -e
chmod 755 {shlex.quote(remote_root)}
find {shlex.quote(remote_root)} -type d -exec chmod 755 {{}} \\;
find {shlex.quote(remote_root)} -type f \\( -name '*.html' -o -name '*.htm' -o -name '*.css' -o -name '*.js' -o -name '*.json' -o -name '*.png' -o -name '*.jpg' -o -name '*.jpeg' -o -name '*.webp' -o -name '*.svg' -o -name '*.ico' -o -name '*.txt' -o -name '*.pdf' \\) -exec chmod 644 {{}} \\;
"""
    ssh(env, remote)


def deploy_file(env: dict[str, str], local_root: Path, local_file: Path, remote_root: str) -> str:
    rel = local_file.relative_to(local_root)
    remote_path = f"{remote_root}/{str(rel)}"
    remote_dir = str(Path(remote_path).parent)
    ssh(env, f"mkdir -p {shlex.quote(remote_dir)}")
    scp(env, local_file, remote_path)
    return remote_path


def main() -> None:
    parser = argparse.ArgumentParser(description="Guarded static Hostinger deploy")
    parser.add_argument("--env", default=str(Path.home() / ".config/dreamos/hostinger_freeride_github_secrets.env"))
    parser.add_argument("--domain", required=True)
    parser.add_argument("--local-root", required=True)
    parser.add_argument("--file", action="append", required=True, help="File path under local-root; repeatable")
    parser.add_argument("--verify-url", required=True)
    parser.add_argument("--dry-run", action="store_true")
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

    print(validate_mode(args.domain, args.env).strip())

    local_root = Path(args.local_root).resolve()
    if not local_root.exists():
        raise SystemExit(f"LOCAL_ROOT_MISSING={local_root}")

    files = [Path(f).resolve() for f in args.file]
    for f in files:
        if not f.exists():
            raise SystemExit(f"LOCAL_FILE_MISSING={f}")
        if local_root not in f.parents and f != local_root:
            raise SystemExit(f"FILE_OUTSIDE_LOCAL_ROOT={f}")

    remote_root = remote_root_for(args.domain, env["HOSTINGER_USER"])

    precheck = ssh(
        env,
        f"test -d {shlex.quote(remote_root)} && test ! -f {shlex.quote(remote_root + '/wp-config.php')} && echo REMOTE_STATIC_ROOT=PASS",
    )
    print(precheck.strip())

    if args.dry_run:
        print(f"DRY_RUN=PASS domain={args.domain} remote_root={remote_root}")
        for f in files:
            print(f"WOULD_UPLOAD={f} -> {remote_root}/{f.relative_to(local_root)}")
        return

    uploaded: list[str] = []
    for f in files:
        uploaded.append(deploy_file(env, local_root, f, remote_root))

    normalize_permissions(env, remote_root)
    print("STATIC_PERMISSIONS_NORMALIZED=PASS")

    curl = ssh(env, f"curl -I -L --max-time 20 {shlex.quote(args.verify_url)} 2>/dev/null | sed -n '1,20p'")
    print(curl.strip())
    if " 200 " not in curl and "HTTP/2 200" not in curl:
        raise SystemExit("HTTP_200_VERIFY=FAIL")

    print("HTTP_200_VERIFY=PASS")
    for path in uploaded:
        print(f"UPLOADED={path}")
    print("GUARDED_STATIC_HOSTINGER_DEPLOY=PASS")


if __name__ == "__main__":
    main()
