#!/usr/bin/env python3
import argparse
import re
import shlex
import subprocess
from pathlib import Path


def load_env(path: Path) -> dict:
    env = {}
    if not path.exists():
        raise SystemExit(f"SECRET_ENV_MISSING={path}")

    for raw in path.read_text().splitlines():
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        env[key.strip()] = value.strip().strip("'").strip('"')

    return env


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


def require_env(env: dict, keys: list[str]) -> None:
    for key in keys:
        if not env.get(key):
            raise SystemExit(f"MISSING_ENV={key}")


def ssh_base(env: dict) -> list[str]:
    key_file = expand_home(env["HOSTINGER_SSH_PRIVATE_KEY_FILE"])
    return [
        "ssh",
        "-o",
        "LogLevel=ERROR",
        "-i",
        key_file,
        "-p",
        env["HOSTINGER_PORT"],
        f'{env["HOSTINGER_USER"]}@{env["HOSTINGER_HOST"]}',
    ]


def wp_root(env: dict) -> str:
    if env.get("HOSTINGER_WP_ROOT"):
        return env["HOSTINGER_WP_ROOT"]

    return f"/home/{env['HOSTINGER_USER']}/domains/{env['DOMAIN']}/public_html"


def remote_shell(env: dict, shell: str, check: bool = True) -> subprocess.CompletedProcess:
    return run(ssh_base(env) + [shell], check=check)


def remote_wp(
    env: dict,
    wp_args: list[str],
    input_text: str | None = None,
    check: bool = True,
    safe_bootstrap: bool = True,
) -> subprocess.CompletedProcess:
    args = list(wp_args)

    if safe_bootstrap:
        if "--skip-plugins" not in args:
            args.append("--skip-plugins")
        if "--skip-themes" not in args:
            args.append("--skip-themes")

    remote = (
        "cd "
        + shlex.quote(wp_root(env))
        + " && wp "
        + " ".join(shlex.quote(a) for a in args)
    )
    return run(ssh_base(env) + [remote], input_text=input_text, check=check)


def check(env: dict) -> None:
    root = wp_root(env)

    print("== SSH CHECK ==")
    remote_shell(env, "echo HOSTINGER_SSH_LOGIN=PASS && pwd")

    print("\n== WP ROOT CHECK ==")
    remote_shell(env, f"test -f {shlex.quote(root)}/wp-config.php && echo WP_ROOT=PASS")

    print("\n== WP CLI CHECK ==")
    remote_wp(env, ["--info"], safe_bootstrap=False)
    print("WP_CLI=PASS")

    print("\n== WP CORE CHECK SAFE ==")
    remote_wp(env, ["core", "is-installed"])
    print("WP_CORE_INSTALLED=PASS")

    print("\n== SITE CHECK SAFE ==")
    remote_wp(env, ["option", "get", "siteurl"])
    print("WP_SITEURL=PASS")

    print("\n== HOME CHECK SAFE ==")
    remote_wp(env, ["option", "get", "home"])
    print("WP_HOME=PASS")


def page_id_by_slug(env: dict, slug: str) -> str | None:
    proc = remote_wp(
        env,
        [
            "post",
            "list",
            "--post_type=page",
            f"--name={slug}",
            "--field=ID",
            "--format=ids",
        ],
        check=False,
    )

    ids = re.findall(r"\b\d+\b", proc.stdout)
    return ids[-1] if ids else None

def remote_wp_content_update(
    env: dict,
    page_id: str,
    title: str,
    slug: str,
    status: str,
    content: str,
) -> subprocess.CompletedProcess:
    root = wp_root(env)
    remote_file = f"/tmp/dreamos_wp_content_{page_id}_{slug}.html"
    remote = (
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
        + "\"--post_content=$(cat "
        + shlex.quote(remote_file)
        + ")\""
        + " --skip-plugins --skip-themes"
        + " ; rc=$?; rm -f "
        + shlex.quote(remote_file)
        + " ; exit $rc"
    )
    return run(ssh_base(env) + [remote], input_text=content)


def remote_wp_content_create(
    env: dict,
    title: str,
    slug: str,
    status: str,
    content: str,
) -> subprocess.CompletedProcess:
    root = wp_root(env)
    remote_file = f"/tmp/dreamos_wp_content_create_{slug}.html"
    remote = (
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
        + "\"--post_content=$(cat "
        + shlex.quote(remote_file)
        + ")\""
        + " --porcelain --skip-plugins --skip-themes"
        + " ; rc=$?; rm -f "
        + shlex.quote(remote_file)
        + " ; exit $rc"
    )
    return run(ssh_base(env) + [remote], input_text=content)


def upsert_page(env: dict, title: str, slug: str, content_file: str, status: str) -> None:
    content = Path(content_file).read_text()
    existing = page_id_by_slug(env, slug)

    if existing:
        print(f"PAGE_EXISTS=PASS id={existing}")
        remote_wp_content_update(env, existing, title, slug, status, content)
        print(f"PAGE_UPDATE=PASS id={existing}")
        return

    print("PAGE_EXISTS=NO")
    proc = remote_wp_content_create(env, title, slug, status, content)
    ids = re.findall(r"\b\d+\b", proc.stdout)
    page_id = ids[-1] if ids else "UNKNOWN"
    print(f"PAGE_CREATE=PASS id={page_id}")


def set_homepage(env: dict, slug: str) -> None:
    page_id = page_id_by_slug(env, slug)
    if not page_id:
        raise SystemExit(f"HOMEPAGE_PAGE_MISSING slug={slug}")

    remote_wp(env, ["option", "update", "show_on_front", "page"])
    remote_wp(env, ["option", "update", "page_on_front", page_id])
    print(f"HOMEPAGE_SET=PASS id={page_id} slug={slug}")


def plugin_status(env: dict) -> None:
    remote_wp(env, ["plugin", "list"], safe_bootstrap=True)
    print("PLUGIN_STATUS=PASS")


def main() -> None:
    parser = argparse.ArgumentParser(description="Dream.OS Hostinger WordPress manager")
    parser.add_argument(
        "--env",
        default=str(Path.home() / ".config/dreamos/hostinger_freeride_github_secrets.env"),
    )

    sub = parser.add_subparsers(dest="cmd", required=True)
    sub.add_parser("check")
    sub.add_parser("plugin-status")

    upsert = sub.add_parser("upsert-page")
    upsert.add_argument("--title", required=True)
    upsert.add_argument("--slug", required=True)
    upsert.add_argument("--content-file", required=True)
    upsert.add_argument("--status", default="publish")

    home = sub.add_parser("set-homepage")
    home.add_argument("--slug", required=True)

    args = parser.parse_args()
    env = load_env(Path(args.env))

    require_env(
        env,
        [
            "HOSTINGER_HOST",
            "HOSTINGER_USER",
            "HOSTINGER_PORT",
            "HOSTINGER_SSH_PRIVATE_KEY_FILE",
            "DOMAIN",
        ],
    )

    if args.cmd == "check":
        check(env)
    elif args.cmd == "plugin-status":
        plugin_status(env)
    elif args.cmd == "upsert-page":
        upsert_page(env, args.title, args.slug, args.content_file, args.status)
    elif args.cmd == "set-homepage":
        set_homepage(env, args.slug)


if __name__ == "__main__":
    main()
