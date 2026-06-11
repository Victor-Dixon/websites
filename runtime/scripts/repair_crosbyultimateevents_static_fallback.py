#!/usr/bin/env python3
from __future__ import annotations

import argparse
import shlex
import subprocess
from datetime import datetime, timezone
from pathlib import Path


DOMAIN = "crosbyultimateevents.com"
FALLBACK_MARKER = "DreamOS Crosby emergency static fallback"


def repo_root() -> Path:
    return Path(__file__).resolve().parents[2]


def load_env(path: Path) -> dict[str, str]:
    if not path.exists():
        raise SystemExit(f"SECRET_ENV_MISSING={path}")

    env: dict[str, str] = {}
    for raw in path.read_text(encoding="utf-8").splitlines():
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        env[key.strip()] = value.strip().strip("'").strip('"')
    return env


def expand_home(value: str) -> str:
    return value.replace("$HOME", str(Path.home()))


def run(cmd: list[str], *, check: bool = True) -> subprocess.CompletedProcess[str]:
    proc = subprocess.run(cmd, check=False, capture_output=True, text=True)
    output = proc.stdout + proc.stderr
    print(output, end="")
    if check and proc.returncode != 0:
        raise SystemExit(proc.returncode)
    return proc


def ssh_base(env: dict[str, str]) -> list[str]:
    return [
        "ssh",
        "-i",
        expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
        "-p",
        env["HOSTINGER_PORT"],
        "-o",
        "StrictHostKeyChecking=accept-new",
        "-o",
        "BatchMode=yes",
        f"{env['HOSTINGER_USER']}@{env['HOSTINGER_HOST']}",
    ]


def ssh(env: dict[str, str], remote: str, *, check: bool = True) -> subprocess.CompletedProcess[str]:
    return run(ssh_base(env) + [remote], check=check)


def scp(env: dict[str, str], local: Path, remote_path: str) -> None:
    cmd = [
        "scp",
        "-i",
        expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"]),
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


def require_env(env: dict[str, str]) -> None:
    for key in ["HOSTINGER_HOST", "HOSTINGER_USER", "HOSTINGER_PORT", "HOSTINGER_SSH_PRIVATE_KEY_FILE"]:
        if not env.get(key):
            raise SystemExit(f"MISSING_ENV={key}")


def remote_root_for(env: dict[str, str], domain: str) -> str:
    configured = env.get("HOSTINGER_WP_ROOT", "")
    if f"/domains/{domain}/" in configured:
        return configured.rstrip("/")
    return f"/home/{env['HOSTINGER_USER']}/domains/{domain}/public_html"


def validate_local_files(local_root: Path) -> list[Path]:
    files = [
        local_root / "index.html",
        local_root / "assets" / "style.css",
    ]
    missing = [path for path in files if not path.exists()]
    if missing:
        raise SystemExit("LOCAL_FALLBACK_FILES_MISSING=" + ",".join(str(path) for path in missing))
    return files


def precheck(env: dict[str, str], remote_root: str) -> None:
    remote = f"""
set -e
root={shlex.quote(remote_root)}
test -d "$root"
echo REMOTE_ROOT=PASS "$root"
if [ -f "$root/wp-config.php" ]; then
  echo WORDPRESS_INSTALL=PASS
else
  echo WORDPRESS_INSTALL=ABSENT
fi
test -w "$root"
echo REMOTE_ROOT_WRITABLE=PASS
"""
    ssh(env, remote)


def backup_remote(env: dict[str, str], domain: str, remote_root: str, stamp: str) -> str:
    backup_dir = f"/home/{env['HOSTINGER_USER']}/domains/{domain}/dreamos_backups/static_fallback_{stamp}"
    remote = f"""
set -e
root={shlex.quote(remote_root)}
backup={shlex.quote(backup_dir)}
mkdir -p "$backup"
for item in .htaccess index.php index.html assets; do
  if [ -e "$root/$item" ]; then
    cp -a "$root/$item" "$backup/"
  fi
done
if [ -d "$root/wp-content/themes" ]; then
  mkdir -p "$backup/wp-content"
  cp -a "$root/wp-content/themes" "$backup/wp-content/"
fi
echo REMOTE_BACKUP_DIR="$backup"
"""
    ssh(env, remote)
    return backup_dir


def upload_fallback(env: dict[str, str], local_root: Path, files: list[Path], remote_root: str) -> None:
    for local in files:
        rel = local.relative_to(local_root)
        remote_path = f"{remote_root}/{rel.as_posix()}"
        remote_dir = str(Path(remote_path).parent)
        ssh(env, f"mkdir -p {shlex.quote(remote_dir)}")
        scp(env, local, remote_path)
        print(f"UPLOADED={remote_path}\n")


def patch_htaccess(env: dict[str, str], remote_root: str) -> None:
    start = f"# BEGIN {FALLBACK_MARKER}"
    end = f"# END {FALLBACK_MARKER}"
    remote = f"""
set -e
root={shlex.quote(remote_root)}
body="$root/.htaccess.dreamos-body.$$"
next="$root/.htaccess.dreamos-next.$$"
marker_start={shlex.quote(start)}
marker_end={shlex.quote(end)}
if [ -f "$root/.htaccess" ]; then
  awk -v marker_start="$marker_start" -v marker_end="$marker_end" '
    $0 == marker_start {{ skip=1; next }}
    $0 == marker_end {{ skip=0; next }}
    skip != 1 {{ print }}
  ' "$root/.htaccess" > "$body"
else
  : > "$body"
fi
cat > "$next" <<'DREAMOS_HTACCESS'
{start}
DirectoryIndex index.html index.php
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^$ /index.html [L]
RewriteCond %{{REQUEST_URI}} !^/wp-admin
RewriteCond %{{REQUEST_URI}} !^/wp-login\\.php
RewriteCond %{{REQUEST_URI}} !^/wp-json
RewriteCond %{{REQUEST_URI}} !^/wp-content
RewriteCond %{{REQUEST_URI}} !^/wp-includes
RewriteCond %{{REQUEST_FILENAME}} !-f
RewriteCond %{{REQUEST_FILENAME}} !-d
RewriteRule . /index.html [L]
</IfModule>
{end}
DREAMOS_HTACCESS
printf '\\n' >> "$next"
cat "$body" >> "$next"
mv "$next" "$root/.htaccess"
rm -f "$body"
chmod 644 "$root/.htaccess"
chmod 755 "$root"
find "$root/assets" -type d -exec chmod 755 {{}} \\; 2>/dev/null || true
find "$root/assets" -type f -exec chmod 644 {{}} \\; 2>/dev/null || true
chmod 644 "$root/index.html"
echo HTACCESS_STATIC_FALLBACK=PASS
"""
    ssh(env, remote)


def verify(env: dict[str, str], verify_url: str) -> None:
    quoted_url = shlex.quote(verify_url)
    remote = f"""
set -e
headers="$(curl -L -I --max-time 20 {quoted_url} 2>/dev/null || true)"
printf '%s\\n' "$headers"
printf '%s\\n' "$headers" | grep -Eq 'HTTP/[0-9.]+ 200|HTTP/2 200'
body="$(curl -L --max-time 20 --silent {quoted_url} || true)"
printf '%s' "$body" | grep -Fq 'Crosby Ultimate Events is online.'
echo HTTP_200_STATIC_FALLBACK_VERIFY=PASS
"""
    ssh(env, remote)


def main() -> None:
    parser = argparse.ArgumentParser(
        description="Repair crosbyultimateevents.com HTTP 500 by installing a static public fallback."
    )
    parser.add_argument("--env", default=str(Path.home() / ".config/dreamos/hostinger_freeride_github_secrets.env"))
    parser.add_argument("--domain", default=DOMAIN, choices=[DOMAIN])
    parser.add_argument(
        "--local-root",
        default=str(repo_root() / "runtime" / "content" / "parked_domains" / DOMAIN),
    )
    parser.add_argument("--verify-url", default=f"https://{DOMAIN}/")
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    env = load_env(Path(args.env))
    require_env(env)
    local_root = Path(args.local_root).resolve()
    files = validate_local_files(local_root)
    remote_root = remote_root_for(env, args.domain)
    stamp = datetime.now(timezone.utc).strftime("%Y%m%d_%H%M%S")

    print(f"DOMAIN={args.domain}")
    print(f"LOCAL_ROOT={local_root}")
    print(f"REMOTE_ROOT={remote_root}")

    precheck(env, remote_root)

    if args.dry_run:
        print(f"DRY_RUN=PASS backup=/home/{env['HOSTINGER_USER']}/domains/{args.domain}/dreamos_backups/static_fallback_{stamp}")
        for local in files:
            print(f"WOULD_UPLOAD={local} -> {remote_root}/{local.relative_to(local_root).as_posix()}")
        print("WOULD_PATCH=.htaccess")
        return

    backup_remote(env, args.domain, remote_root, stamp)
    upload_fallback(env, local_root, files, remote_root)
    patch_htaccess(env, remote_root)
    verify(env, args.verify_url)
    print("CROSBYULTIMATEEVENTS_STATIC_FALLBACK_REPAIR=PASS")


if __name__ == "__main__":
    main()
