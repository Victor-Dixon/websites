#!/usr/bin/env python3
"""Migrate dadudekc WordPress Spark users into MaskZero first-party auth."""

from __future__ import annotations

import json
import secrets
import sys
import urllib.error
import urllib.request
from pathlib import Path

try:
    import paramiko
except ImportError:
    print("Install paramiko: pip install paramiko")
    sys.exit(1)

ROOT = Path(__file__).resolve().parents[2]
CONFIG = ROOT / "config" / "site_configs.json"
EXPORT_SCRIPT = ROOT / "ops" / "migration" / "export_wp_users_for_maskzero.php"
AUTH_PHP = ROOT / "runtime" / "content" / "maskzero.site" / "api" / "spark-auth.php"
PHPASS = ROOT / "runtime" / "content" / "maskzero.site" / "api" / "class-phpass.php"


def load_site(domain: str) -> dict:
    data = json.loads(CONFIG.read_text(encoding="utf-8"))
    return data[domain]


def sftp_client(site: dict) -> tuple[paramiko.SFTPClient, paramiko.Transport]:
    sftp_cfg = site["sftp"]
    transport = paramiko.Transport((sftp_cfg["host"], sftp_cfg["port"]))
    transport.connect(username=sftp_cfg["username"], password=sftp_cfg["password"])
    return paramiko.SFTPClient.from_transport(transport), transport


def upload_bytes(sftp: paramiko.SFTPClient, remote_path: str, payload: bytes) -> None:
    with sftp.open(remote_path, "wb") as handle:
        handle.write(payload)


def delete_remote(sftp: paramiko.SFTPClient, remote_path: str) -> None:
    try:
        sftp.remove(remote_path)
    except OSError:
        pass


def http_json(url: str, method: str = "GET", data: dict | None = None, timeout: int = 60) -> tuple[int, dict]:
    body = None
    headers = {"Accept": "application/json"}
    if data is not None:
        body = json.dumps(data).encode("utf-8")
        headers["Content-Type"] = "application/json"
    req = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=timeout) as resp:
            raw = resp.read().decode("utf-8", "replace")
            return resp.status, json.loads(raw) if raw else {}
    except urllib.error.HTTPError as err:
        raw = err.read().decode("utf-8", "replace")
        try:
            payload = json.loads(raw) if raw else {}
        except json.JSONDecodeError:
            payload = {"message": raw[:500]}
        return err.code, payload


def main() -> int:
    export_token = secrets.token_hex(24)
    import_token = secrets.token_hex(24)

    dadudekc = load_site("dadudekc.site")
    maskzero = load_site("maskzero.site")
    dad_remote = dadudekc["sftp"]["remote_path"]
    mz_remote = maskzero["sftp"]["remote_path"]

    print("1) Deploy updated MaskZero auth files")
    mz_sftp, mz_transport = sftp_client(maskzero)
    upload_bytes(mz_sftp, f"{mz_remote}/api/spark-auth.php", AUTH_PHP.read_bytes())
    upload_bytes(mz_sftp, f"{mz_remote}/api/class-phpass.php", PHPASS.read_bytes())
    upload_bytes(
        mz_sftp,
        f"{mz_remote}/.spark-auth/migrate.token",
        import_token.encode("utf-8"),
    )
    mz_sftp.close()
    mz_transport.close()

    print("2) Upload temporary dadudekc export endpoint")
    dd_sftp, dd_transport = sftp_client(dadudekc)
    export_name = "spark-migrate-export.php"
    export_remote = f"{dad_remote}/{export_name}"
    token_remote = f"{dad_remote}/.spark-migrate-export.token"
    upload_bytes(dd_sftp, export_remote, EXPORT_SCRIPT.read_bytes())
    upload_bytes(dd_sftp, token_remote, export_token.encode("utf-8"))
    dd_sftp.close()
    dd_transport.close()

    export_url = f"https://dadudekc.site/{export_name}?token={export_token}"
    print(f"3) Export WordPress users from {export_url.split('?')[0]}")
    status, export_payload = http_json(export_url)
    if status != 200 or not export_payload.get("ok"):
        print(f"Export failed ({status}): {export_payload}")
        return 1
    users = export_payload.get("users") or []
    print(f"   exported {len(users)} users")

    import_url = f"https://maskzero.site/api/spark-auth.php?action=import&token={import_token}"
    print("4) Import users into MaskZero .spark-auth/users.json")
    status, import_payload = http_json(import_url, method="POST", data={"users": users, "token": import_token})
    if status != 200 or not import_payload.get("ok"):
        print(f"Import failed ({status}): {import_payload}")
        return 1
    print(
        "   imported={imported} skipped={skipped} total={total}".format(
            imported=import_payload.get("imported"),
            skipped=import_payload.get("skipped"),
            total=import_payload.get("total"),
        )
    )

    print("5) Cleanup temporary export files on dadudekc")
    dd_sftp, dd_transport = sftp_client(dadudekc)
    delete_remote(dd_sftp, export_remote)
    delete_remote(dd_sftp, token_remote)
    dd_sftp.close()
    dd_transport.close()

    print("6) Remove migrate.token from MaskZero (import complete)")
    mz_sftp, mz_transport = sftp_client(maskzero)
    delete_remote(mz_sftp, f"{mz_remote}/.spark-auth/migrate.token")
    mz_sftp.close()
    mz_transport.close()

    print("Migration complete. Migrated users can log in with existing dadudekc passwords.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
