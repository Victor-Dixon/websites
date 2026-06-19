#!/usr/bin/env python3
import argparse
import hashlib
import html.parser
import json
import sys
import urllib.parse
import urllib.request
from dataclasses import dataclass, field
from typing import Dict, List, Set, Tuple


@dataclass
class PageScan:
    url: str
    status: int = 0
    title: str = ""
    links: List[str] = field(default_factory=list)
    css: List[str] = field(default_factory=list)
    scripts: List[str] = field(default_factory=list)
    images: List[str] = field(default_factory=list)
    inline_style_hashes: List[str] = field(default_factory=list)
    issues: List[str] = field(default_factory=list)


class SiteParser(html.parser.HTMLParser):
    def __init__(self):
        super().__init__()
        self.links = []
        self.css = []
        self.scripts = []
        self.images = []
        self.inline_style_hashes = []
        self.title = ""
        self._in_title = False
        self._in_style = False
        self._style_buf = []

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == "title":
            self._in_title = True
        if tag == "a":
            self.links.append(attrs.get("href", ""))
        if tag == "link" and attrs.get("rel"):
            rel = " ".join(attrs.get("rel")) if isinstance(attrs.get("rel"), list) else attrs.get("rel", "")
            if "stylesheet" in rel.lower():
                self.css.append(attrs.get("href", ""))
        if tag == "script" and attrs.get("src"):
            self.scripts.append(attrs.get("src", ""))
        if tag == "img":
            self.images.append(attrs.get("src", ""))
        if tag == "style":
            self._in_style = True
            self._style_buf = []

    def handle_endtag(self, tag):
        if tag == "title":
            self._in_title = False
        if tag == "style":
            self._in_style = False
            data = "".join(self._style_buf).strip()
            if data:
                self.inline_style_hashes.append(hashlib.sha256(data.encode("utf-8")).hexdigest()[:12])

    def handle_data(self, data):
        if self._in_title:
            self.title += data.strip()
        if self._in_style:
            self._style_buf.append(data)


def fetch(url: str, timeout: int = 20) -> Tuple[int, str]:
    req = urllib.request.Request(url, headers={"User-Agent": "DreamOS-LiveVerify/1.0"})
    try:
        with urllib.request.urlopen(req, timeout=timeout) as res:
            body = res.read().decode("utf-8", errors="replace")
            return int(res.status), body
    except Exception as exc:
        return 0, f"FETCH_ERROR: {exc}"


def absolute(base: str, href: str) -> str:
    return urllib.parse.urljoin(base, href)


def same_domain(base: str, url: str) -> bool:
    return urllib.parse.urlparse(base).netloc == urllib.parse.urlparse(url).netloc


def check_asset(base_url: str, asset_url: str, label: str, issues: List[str]) -> None:
    if not asset_url or asset_url.startswith("data:"):
        return
    full = absolute(base_url, asset_url)
    if not same_domain(base_url, full):
        return
    status, _ = fetch(full)
    if status < 200 or status >= 400:
        issues.append(f"{label}_BROKEN {full} status={status}")


def scan_page(url: str, required_markers: List[str]) -> PageScan:
    status, body = fetch(url)
    scan = PageScan(url=url, status=status)

    if status != 200:
        scan.issues.append(f"PAGE_STATUS_FAIL {url} status={status}")
        return scan

    parser = SiteParser()
    parser.feed(body)

    scan.title = parser.title
    scan.links = parser.links
    scan.css = parser.css
    scan.scripts = parser.scripts
    scan.images = parser.images
    scan.inline_style_hashes = parser.inline_style_hashes

    if not scan.title:
        scan.issues.append(f"MISSING_TITLE {url}")

    for marker in required_markers:
        if marker and marker not in body:
            scan.issues.append(f"MISSING_MARKER {url} marker={marker!r}")

    for href in scan.links:
        h = (href or "").strip()
        if not h:
            scan.issues.append(f"EMPTY_LINK {url}")
        if h == "#":
            scan.issues.append(f"HASH_ONLY_LINK {url}")
        if h.lower().startswith("javascript:"):
            scan.issues.append(f"JAVASCRIPT_LINK {url}")
        if h.startswith("http://"):
            scan.issues.append(f"MIXED_OR_INSECURE_LINK {url} href={h}")

    for css in scan.css:
        check_asset(url, css, "CSS", scan.issues)

    for js in scan.scripts:
        check_asset(url, js, "SCRIPT", scan.issues)

    for img in scan.images:
        check_asset(url, img, "IMAGE", scan.issues)

    return scan


def normalize_css_set(base: str, css_links: List[str]) -> List[str]:
    out = []
    for css in css_links:
        if not css:
            continue
        full = absolute(base, css)
        parsed = urllib.parse.urlparse(full)
        normalized = parsed._replace(query="", fragment="").geturl()
        out.append(normalized)
    return sorted(set(out))


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--base", required=True)
    ap.add_argument("--paths", nargs="+", default=["/"])
    ap.add_argument("--required-marker", action="append", default=[])
    ap.add_argument("--strict", action="store_true")
    ap.add_argument("--json-out", default="")
    args = ap.parse_args()

    base = args.base.rstrip("/")
    scans: List[PageScan] = []
    all_issues: List[str] = []

    for path in args.paths:
        url = absolute(base + "/", path.lstrip("/"))
        scan = scan_page(url, args.required_marker)
        scans.append(scan)
        all_issues.extend(scan.issues)

    # Internal link checks, same-domain only.
    seen_internal: Set[str] = set()
    for scan in scans:
        for href in scan.links:
            h = (href or "").strip()
            if not h or h == "#" or h.startswith("mailto:") or h.startswith("tel:") or h.startswith("javascript:"):
                continue
            full = absolute(scan.url, h)
            if not same_domain(base, full):
                continue
            parsed = urllib.parse.urlparse(full)
            clean = parsed._replace(fragment="", query="").geturl()
            if clean in seen_internal:
                continue
            seen_internal.add(clean)
            status, _ = fetch(clean)
            if status < 200 or status >= 400:
                all_issues.append(f"INTERNAL_LINK_BROKEN source={scan.url} href={h} status={status}")

    # Style consistency: pages in same domain should share the same external CSS set.
    css_sets: Dict[str, List[str]] = {}
    for scan in scans:
        css_sets[scan.url] = normalize_css_set(scan.url, scan.css)

    unique_css_sets = {tuple(v) for v in css_sets.values()}
    if len(unique_css_sets) > 1:
        all_issues.append(f"CSS_SET_DRIFT pages_have_different_stylesheets={css_sets}")

    hard_fail = [
        i for i in all_issues
        if i.startswith("PAGE_STATUS_FAIL")
        or i.startswith("CSS_BROKEN")
        or i.startswith("SCRIPT_BROKEN")
        or i.startswith("IMAGE_BROKEN")
        or i.startswith("INTERNAL_LINK_BROKEN")
        or i.startswith("MISSING_MARKER")
    ]

    report_status = "PASS" if (not hard_fail and not args.strict) or (args.strict and not all_issues) else "FAIL"

    report = {
        "base": base,
        "paths": args.paths,
        "strict": args.strict,
        "pages": [
            {
                "url": s.url,
                "status": s.status,
                "title": s.title,
                "css": s.css,
                "links_count": len(s.links),
                "issues": s.issues,
            }
            for s in scans
        ],
        "issues": all_issues,
        "hard_failures": hard_fail,
        "status": report_status,
    }

    if args.json_out:
        with open(args.json_out, "w", encoding="utf-8") as f:
            json.dump(report, f, indent=2)

    print("TARGET: live site verification")
    print(f"BASE={base}")
    print(f"PATHS={','.join(args.paths)}")
    print(f"ISSUE_COUNT={len(all_issues)}")

    for issue in all_issues:
        print(f"ISSUE: {issue}")

    print(f"STATUS={report['status']}")

    if args.strict and all_issues:
        return 1

    return 1 if hard_fail else 0


if __name__ == "__main__":
    raise SystemExit(main())
