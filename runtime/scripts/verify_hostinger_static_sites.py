#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import subprocess
import sys
from pathlib import Path
from urllib.request import urlopen, Request
from urllib.error import URLError, HTTPError


MANIFEST = Path("runtime/deploy/hostinger_sites_manifest.yaml")


def parse_manifest(path: Path) -> list[dict[str, object]]:
    text = path.read_text(encoding="utf-8")
    sites: list[dict[str, object]] = []
    current: dict[str, object] | None = None
    in_markers = False

    for raw in text.splitlines():
        line = raw.rstrip()
        stripped = line.strip()

        if stripped.startswith("- domain:"):
            if current:
                sites.append(current)
            current = {"domain": stripped.split(":", 1)[1].strip(), "verify_markers": []}
            in_markers = False
            continue

        if current is None:
            continue

        if stripped.startswith("verify_markers:"):
            in_markers = True
            continue

        if in_markers and stripped.startswith("- "):
            marker = stripped[2:].strip().strip('"')
            current.setdefault("verify_markers", []).append(marker)
            continue

        in_markers = False
        if ":" in stripped:
            key, value = stripped.split(":", 1)
            current[key.strip()] = value.strip()

    if current:
        sites.append(current)

    return sites


def fetch(url: str) -> tuple[int, str, str]:
    req = Request(url, headers={"User-Agent": "DreamOS-WebsiteVerifier/1.0"})
    try:
        with urlopen(req, timeout=25) as resp:
            body = resp.read().decode("utf-8", errors="replace")
            return resp.status, resp.headers.get("content-type", ""), body
    except HTTPError as exc:
        body = exc.read().decode("utf-8", errors="replace")
        return exc.code, exc.headers.get("content-type", ""), body
    except URLError as exc:
        return 0, "", str(exc)


def main() -> int:
    sites = parse_manifest(MANIFEST)
    if not sites:
        print("VERIFY=FAIL no sites parsed")
        return 2

    failures = []
    print(f"SITE_COUNT={len(sites)}")

    for site in sites:
        domain = str(site["domain"])
        url = str(site.get("url") or f"https://{domain}/")
        markers = list(site.get("verify_markers", []))

        status, content_type, body = fetch(url)
        print(f"--- {domain} ---")
        print(f"URL={url}")
        print(f"HTTP_STATUS={status}")
        print(f"CONTENT_TYPE={content_type}")

        if status != 200:
            failures.append(f"{domain}: HTTP {status}")

        for marker in markers:
            ok = marker in body
            print(f"MARKER {'PASS' if ok else 'FAIL'} {marker}")
            if not ok:
                failures.append(f"{domain}: missing marker {marker}")

        if 'href="#' in body:
            failures.append(f"{domain}: href hash links remain")
            print("HASH_LINKS=FAIL")
        else:
            print("HASH_LINKS=PASS")

    if failures:
        print("VERIFY=FAIL")
        for failure in failures:
            print(f"FAILURE={failure}")
        return 1

    print("VERIFY=PASS")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
