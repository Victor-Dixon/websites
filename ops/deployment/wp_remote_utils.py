"""
Remote WordPress utilities (SSH/WP-CLI helpers).

These helpers are used by multiple scripts to:
- load a local repo `.env` (best-effort)
- detect the remote WordPress root path (wp-config.php)
"""

from __future__ import annotations

from pathlib import Path


def load_repo_dotenv(*, repo_root: Path) -> None:
    """Best-effort dotenv loading; no-op if python-dotenv isn't installed."""
    try:
        from dotenv import load_dotenv  # type: ignore
    except Exception:
        return
    env_path = repo_root / ".env"
    if env_path.exists():
        load_dotenv(env_path, override=False)


def detect_wp_path(*, deployer, site_domain: str) -> str:
    """
    Determine the WordPress root path on the remote server.

    Strategy:
    - Ask remote shell for $HOME (most reliable, avoids guessing usernames).
    - Try candidate paths and select one that contains wp-config.php.
    """
    home = (deployer.execute_command("echo $HOME 2>/dev/null") or "").strip()
    if not home:
        # Fallback: common Linux home root
        home = "/home"

    candidates: list[str] = []

    remote_path = getattr(deployer, "remote_path", "") or ""
    if remote_path:
        # remote_path in configs is typically "domains/<domain>/public_html"
        candidates.append(f"{home.rstrip('/')}/{remote_path}".rstrip("/"))

    candidates.extend(
        [
            f"{home.rstrip('/')}/domains/{site_domain}/public_html",
            f"{home.rstrip('/')}/public_html",
        ]
    )

    for base in candidates:
        chk = deployer.execute_command(f"test -f {base}/wp-config.php && echo OK 2>/dev/null")
        if (chk or "").strip() == "OK":
            return base

    # As a last resort, return first candidate (helps error messages).
    return candidates[0] if candidates else ""

