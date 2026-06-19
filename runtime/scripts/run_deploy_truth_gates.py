#!/usr/bin/env python3
import argparse
import subprocess
import sys
from pathlib import Path

try:
    import yaml
except Exception:
    yaml = None

def load_config(path: Path):
    if yaml:
        return yaml.safe_load(path.read_text())
    raise SystemExit("PyYAML missing. Run deploy_truth_gate.py directly or install PyYAML.")

def main():
    p = argparse.ArgumentParser()
    p.add_argument("--site", action="append", default=[])
    p.add_argument("--config", default="runtime/config/deploy_truth_sites.yaml")
    args = p.parse_args()

    cfg = load_config(Path(args.config))
    sites = cfg.get("sites", {})
    selected = args.site or list(sites.keys())

    failures = 0
    for site in selected:
        item = sites[site]
        cmd = [
            sys.executable,
            "runtime/scripts/deploy_truth_gate.py",
            "--site", site,
            "--source-root", item["source_root"],
            "--deploy-root", item["deploy_root"],
            "--workflow", item["workflow"],
            "--url", item["url"],
        ]

        if item.get("require_identical"):
            cmd.append("--require-identical")
        if item.get("require_live"):
            cmd.append("--require-live")

        for f in item.get("files", ["index.html"]):
            cmd += ["--file", f]
        for m in item.get("markers", []):
            cmd += ["--marker", m]

        print(f"== DEPLOY TRUTH: {site} ==")
        result = subprocess.run(cmd, text=True)
        if result.returncode != 0:
            failures += 1

    if failures:
        print(f"VERIFY=FAIL_DEPLOY_TRUTH_GATES failures={failures}")
        sys.exit(1)

    print("VERIFY=PASS_DEPLOY_TRUTH_GATES")

if __name__ == "__main__":
    main()
