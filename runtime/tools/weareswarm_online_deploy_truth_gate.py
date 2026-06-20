#!/usr/bin/env python3
import json
import os
import re
import subprocess
import sys
import time
from pathlib import Path
from urllib.request import urlopen, Request

ROOT = Path.cwd()
DOMAIN = os.environ.get("DOMAIN", "weareswarm.online")

FOCUS = ROOT / "_deploy/weareswarm.online/focus/index.html"
WF = ROOT / ".github/workflows/deploy-weareswarm-online.yml"
REPORT_DIR = ROOT / "data/reports/weareswarm_online"
REPORT_DIR.mkdir(parents=True, exist_ok=True)

result = {
    "gate": "weareswarm_online_deploy_truth_gate",
    "domain": DOMAIN,
    "generated": time.strftime("%Y-%m-%dT%H:%M:%S%z"),
    "checks": {},
    "latest_deploy": None,
}

def check(name, ok, detail=""):
    result["checks"][name] = {"ok": bool(ok), "detail": detail}
    print(f"VERIFY={'PASS' if ok else 'FAIL'}_{name}" + (f" {detail}" if detail else ""))
    if not ok:
        result["failed"] = name

def read(p):
    return p.read_text(errors="ignore") if p.exists() else ""

focus = read(FOCUS)
wf = read(WF)

check("SOURCE_FOCUS_EXISTS", FOCUS.exists(), str(FOCUS))
check("SOURCE_FOCUS_GATE", "AUTONOMY_LEVEL_GATE_START" in focus)
check("SOURCE_FOCUS_LEVEL_TEXT", "Current Verified Level" in focus)
check("SOURCE_OLD_LEVEL4_COPY_REMOVED", "Level 4 → Level 5 is the proper lane" not in focus)

check("WORKFLOW_EXISTS", WF.exists(), str(WF))
check("WORKFLOW_UPLOADS_FOCUS", "_deploy/weareswarm.online/focus/index.html" in wf)
check("WORKFLOW_HOSTINGER_TARGET", "157.173.214.121" in wf and "65002" in wf and "u996867598" in wf)
check("WORKFLOW_EMPTY_ASSETS_SAFE", "SKIP_DEPLOY_ASSETS_EMPTY_OR_MISSING" in wf)

latest_ok = False
skip_latest = os.environ.get("DEPLOY_TRUTH_GATE_SKIP_LATEST", "").strip() in {"1", "true", "TRUE", "yes", "YES"}

if skip_latest:
    result["latest_deploy"] = {"skipped": True, "reason": "running inside current deploy workflow"}
    check("LATEST_DEPLOY_SUCCESS", True, "skipped_inside_running_workflow")
else:
    try:
        raw = subprocess.check_output(
            [
                "gh", "run", "list",
                "--workflow", "deploy-weareswarm-online.yml",
                "--limit", "1",
                "--json", "databaseId,status,conclusion,headSha,createdAt,url",
            ],
            text=True,
            stderr=subprocess.STDOUT,
        )
        runs = json.loads(raw)
        latest = runs[0] if runs else {}
        result["latest_deploy"] = latest
        latest_ok = latest.get("status") == "completed" and latest.get("conclusion") == "success"
        check("LATEST_DEPLOY_SUCCESS", latest_ok, f"RUN_ID={latest.get('databaseId')} CONCLUSION={latest.get('conclusion')}")
    except Exception as e:
        check("LATEST_DEPLOY_SUCCESS", False, f"gh_error={e}")

live_ok = False
try:
    url = f"https://www.{DOMAIN}/focus/?cache={int(time.time())}"
    req = Request(url, headers={"User-Agent": "DreamOSDeployTruthGate/1.0"})
    body = urlopen(req, timeout=25).read().decode("utf-8", errors="ignore")
    check("LIVE_FOCUS_GATE", "AUTONOMY_LEVEL_GATE_START" in body)
    check("LIVE_CURRENT_VERIFIED_LEVEL", "Current Verified Level" in body)
    check("LIVE_OLD_LEVEL4_COPY_REMOVED", "Level 4 → Level 5 is the proper lane" not in body)
except Exception as e:
    check("LIVE_FOCUS_GATE", False, f"live_error={e}")
    check("LIVE_CURRENT_VERIFIED_LEVEL", False, "not_checked")
    check("LIVE_OLD_LEVEL4_COPY_REMOVED", False, "not_checked")

all_ok = all(v["ok"] for v in result["checks"].values())
result["ok"] = all_ok

latest_json = REPORT_DIR / "deploy_truth_gate_latest.json"
latest_md = REPORT_DIR / "deploy_truth_gate_latest.md"

latest_json.write_text(json.dumps(result, indent=2) + "\n")

lines = [
    "# WeAreSwarm Online Deploy Truth Gate",
    "",
    f"- domain: `{DOMAIN}`",
    f"- ok: `{all_ok}`",
    f"- generated: `{result['generated']}`",
    "",
    "## Checks",
]
for k, v in result["checks"].items():
    mark = "PASS" if v["ok"] else "FAIL"
    lines.append(f"- `{mark}` `{k}` {v.get('detail','')}")
latest_md.write_text("\n".join(lines) + "\n")

print(f"REPORT_JSON={latest_json}")
print(f"REPORT_MD={latest_md}")

if all_ok:
    print("CLOSEOUT=PASS_WEARESWARM_ONLINE_DEPLOY_TRUTH_GATE")
    sys.exit(0)

print("CLOSEOUT=FAIL_WEARESWARM_ONLINE_DEPLOY_TRUTH_GATE")
sys.exit(1)
