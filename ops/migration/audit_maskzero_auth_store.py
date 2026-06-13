#!/usr/bin/env python3
"""Safe audit of MaskZero auth store — no passwords accepted or logged."""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path


def load_users(path: Path) -> list:
    if not path.exists():
        raise SystemExit(f"missing_users_file={path}")
    data = json.loads(path.read_text(encoding="utf-8"))
    if isinstance(data, dict) and "users" in data:
        users = data["users"]
    elif isinstance(data, list):
        users = data
    elif isinstance(data, dict):
        users = list(data.values())
    else:
        users = []
    if not isinstance(users, list):
        raise SystemExit("unsupported_users_format")
    return users


def norm(value: object) -> str:
    return str(value or "").strip().lower()


def audit_users(users: list, identifier: str) -> int:
    ident = norm(identifier)
    matches = []
    for user in users:
        if not isinstance(user, dict):
            continue
        keys = {
            "id": user.get("id"),
            "email": user.get("email"),
            "username": user.get("username"),
            "login": user.get("login"),
            "user_login": user.get("user_login"),
            "display_name": user.get("display_name"),
        }
        searchable = {norm(v) for v in keys.values() if v}
        if ident in searchable:
            matches.append(user)

    print("== MASKZERO AUTH STORE AUDIT ==")
    print(f"total_users={len(users)}")
    print(f"identifier_supplied={bool(identifier)}")
    print(f"matches={len(matches)}")

    for idx, user in enumerate(matches, 1):
        print(f"match_{idx}:")
        print(f"  has_email={bool(user.get('email'))}")
        print(f"  has_username={bool(user.get('username'))}")
        print(f"  has_login={bool(user.get('login') or user.get('user_login'))}")
        print(
            f"  has_bcrypt_hash={bool(user.get('password_hash') or user.get('passwordHash') or user.get('hash'))}"
        )
        print(
            f"  has_wp_pass_hash={bool(user.get('wp_pass_hash') or user.get('wpPassHash') or user.get('wp_hash'))}"
        )
        print(f"  legacy_source={user.get('migrated_from') or user.get('legacy_source') or user.get('source') or ''}")
        print(f"  migrated={user.get('migrated')}")
        print(f"  raw_keys={','.join(sorted(user.keys()))}")

    if not matches:
        print("STATUS=NO_USER_MATCH")
        return 3

    missing_legacy = [
        u
        for u in matches
        if not (u.get("wp_pass_hash") or u.get("wpPassHash") or u.get("wp_hash"))
        and not (u.get("password_hash") or u.get("passwordHash") or u.get("hash"))
    ]
    if missing_legacy and all(
        not (u.get("wp_pass_hash") or u.get("wpPassHash") or u.get("wp_hash")) for u in matches
    ):
        print("STATUS=USER_MATCH_BUT_NO_LEGACY_HASH")
        return 4

    print("STATUS=USER_MATCH_WITH_LEGACY_HASH")
    return 0


def fetch_production_users() -> list:
    import json as js

    root = Path(__file__).resolve().parents[2]
    cfg = js.loads((root / "config" / "site_configs.json").read_text(encoding="utf-8"))["maskzero.site"]["sftp"]
    try:
        import paramiko
    except ImportError:
        raise SystemExit("paramiko required for --production")

    transport = paramiko.Transport((cfg["host"], cfg["port"]))
    transport.connect(username=cfg["username"], password=cfg["password"])
    sftp = paramiko.SFTPClient.from_transport(transport)
    remote = cfg["remote_path"] + "/.spark-auth/users.json"
    with sftp.open(remote, "r") as handle:
        raw = handle.read().decode("utf-8", "replace")
    sftp.close()
    transport.close()
    data = js.loads(raw)
    return data if isinstance(data, list) else data.get("users", [])


def main() -> None:
    ap = argparse.ArgumentParser(description="Audit MaskZero auth store (no passwords).")
    ap.add_argument(
        "--users",
        default="runtime/content/maskzero.site/.spark-auth/users.json",
        help="Path to users.json",
    )
    ap.add_argument(
        "--production",
        action="store_true",
        help="Read users.json from live maskzero.site via SFTP",
    )
    ap.add_argument(
        "--list-identifiers",
        action="store_true",
        help="List searchable identifiers (email, display_name, user_login) only",
    )
    ap.add_argument(
        "--identifier",
        help="Username or email to check; password is not accepted",
    )
    args = ap.parse_args()

    if args.production:
        users = fetch_production_users()
        print(f"users_source=production")
    else:
        users_path = Path(args.users)
        users = load_users(users_path)
        print(f"users_file={users_path}")

    if args.list_identifiers:
        print("== SEARCHABLE IDENTIFIERS (no secrets) ==")
        for user in users:
            if not isinstance(user, dict):
                continue
            parts = [
                str(user.get("email") or ""),
                str(user.get("display_name") or ""),
                str(user.get("user_login") or ""),
            ]
            label = " | ".join(p for p in parts if p)
            print(f"  {label}")
        raise SystemExit(0)

    if not args.identifier:
        ap.error("--identifier or --list-identifiers required")

    code = audit_users(users, args.identifier)
    raise SystemExit(code)


if __name__ == "__main__":
    main()
