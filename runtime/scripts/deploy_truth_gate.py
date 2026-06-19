#!/usr/bin/env python3
import argparse
import hashlib
import json
import subprocess
import sys
import time
import urllib.request
from pathlib import Path

def read(path: Path) -> str:
    return path.read_text(encoding="utf-8", errors="replace")

def sha(text: str) -> str:
    return hashlib.sha256(text.encode("utf-8")).hexdigest()[:16]

def fail(code: str, data=None):
    print("VERIFY=FAIL_DEPLOY_TRUTH_GATE")
    print(f"ERROR={code}")
    if data:
        print(json.dumps(data, indent=2))
    sys.exit(1)

def ok(data):
    print("VERIFY=PASS_DEPLOY_TRUTH_GATE")
    print(json.dumps(data, indent=2))

def fetch(url: str) -> str:
    sep = "&" if "?" in url else "?"
    busted = f"{url}{sep}dreamos_truth={int(time.time())}"
    req = urllib.request.Request(
        busted,
        headers={
            "User-Agent": "DreamOS-DeployTruthGate/3.0",
            "Cache-Control": "no-cache",
            "Pragma": "no-cache",
        },
    )
    with urllib.request.urlopen(req, timeout=25) as r:
        return r.read().decode("utf-8", errors="replace")

def git_changed(path: Path) -> bool:
    result = subprocess.run(
        ["git", "status", "--short", "--", str(path)],
        text=True,
        capture_output=True,
    )
    return bool(result.stdout.strip())

def main():
    p = argparse.ArgumentParser()
    p.add_argument("--site", required=True)
    p.add_argument("--source-root", required=True)
    p.add_argument("--deploy-root", required=True)
    p.add_argument("--workflow", required=True)
    p.add_argument("--url", required=True)
    p.add_argument("--marker", action="append", default=[])
    p.add_argument("--file", action="append", default=["index.html"])
    p.add_argument("--require-identical", action="store_true")
    p.add_argument("--require-live", action="store_true")
    args = p.parse_args()

    source_root = Path(args.source_root)
    deploy_root = Path(args.deploy_root)
    workflow = Path(args.workflow)

    if not source_root.exists():
        fail("SOURCE_ROOT_MISSING", {"source_root": str(source_root)})
    if not deploy_root.exists():
        fail("DEPLOY_ROOT_MISSING", {"deploy_root": str(deploy_root)})
    if not workflow.exists():
        fail("WORKFLOW_MISSING", {"workflow": str(workflow)})

    workflow_text = read(workflow)
    if str(deploy_root) not in workflow_text:
        fail("WORKFLOW_DOES_NOT_REFERENCE_DEPLOY_ROOT", {
            "workflow": str(workflow),
            "deploy_root": str(deploy_root),
        })

    files = []
    for rel in args.file:
        src = source_root / rel
        dst = deploy_root / rel

        if not src.exists():
            fail("SOURCE_FILE_MISSING", {"file": str(src)})
        if not dst.exists():
            fail("DEPLOY_FILE_MISSING", {"file": str(dst)})

        src_text = read(src)
        dst_text = read(dst)

        missing_dst = [m for m in args.marker if m not in dst_text]
        if missing_dst:
            fail("DEPLOY_FILE_MARKERS_MISSING", {
                "file": str(dst),
                "missing": missing_dst,
            })

        identical = src_text == dst_text
        if args.require_identical and not identical:
            fail("SOURCE_DEPLOY_DRIFT", {
                "file": rel,
                "source": str(src),
                "deploy": str(dst),
                "source_sha": sha(src_text),
                "deploy_sha": sha(dst_text),
            })

        files.append({
            "file": rel,
            "source": str(src),
            "deploy": str(dst),
            "identical": identical,
            "source_changed": git_changed(src),
            "deploy_changed": git_changed(dst),
            "source_sha": sha(src_text),
            "deploy_sha": sha(dst_text),
        })

    live_result = {"checked": False}
    if args.require_live:
        live = fetch(args.url)
        missing_live = [m for m in args.marker if m not in live]
        if missing_live:
            fail("LIVE_MARKERS_MISSING", {
                "url": args.url,
                "missing": missing_live,
            })
        live_result = {
            "checked": True,
            "url": args.url,
            "bytes": len(live.encode("utf-8")),
            "sha": sha(live),
        }

    ok({
        "site": args.site,
        "source_root": str(source_root),
        "deploy_root": str(deploy_root),
        "workflow": str(workflow),
        "markers": args.marker,
        "files": files,
        "live": live_result,
    })

if __name__ == "__main__":
    main()
