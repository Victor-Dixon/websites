#!/usr/bin/env python3
from __future__ import annotations

import argparse
import re
import subprocess
import sys
from pathlib import Path


REGISTRY_RE = re.compile(r"^  ([a-z0-9.-]+):\n    mode: ([a-z_]+)\n    deploy_policy: ([a-z0-9_]+)", re.M)


def parse_registry(path: Path) -> dict[str, dict[str, str]]:
    text = path.read_text(encoding="utf-8")
    domains: dict[str, dict[str, str]] = {}
    for domain, mode, policy in REGISTRY_RE.findall(text):
        domains[domain] = {"mode": mode, "deploy_policy": policy}
    return domains


def expected_install_type(mode: str) -> str:
    if mode == "static":
        return "static"
    if mode == "wordpress":
        return "wordpress"
    raise SystemExit(f"UNKNOWN_MODE={mode}")


def run_guard(domain: str, mode: str, env_file: str) -> str:
    guard = Path("runtime/scripts/hostinger_deploy_target_guard.py")
    if not guard.exists():
        raise SystemExit("MISSING_GUARD=runtime/scripts/hostinger_deploy_target_guard.py")

    cmd = [
        sys.executable,
        str(guard),
        "--env",
        env_file,
        "--domain",
        domain,
        "--expect",
        expected_install_type(mode),
    ]
    proc = subprocess.run(cmd, check=True, capture_output=True, text=True)
    return proc.stdout


def main() -> None:
    parser = argparse.ArgumentParser(description="Validate website deploy mode registry")
    parser.add_argument("--registry", default="runtime/config/website_deploy_modes.yaml")
    parser.add_argument("--env", default=str(Path.home() / ".config/dreamos/hostinger_freeride_github_secrets.env"))
    parser.add_argument("--domain", help="Validate one domain only")
    args = parser.parse_args()

    domains = parse_registry(Path(args.registry))
    if not domains:
        raise SystemExit("NO_DOMAINS_PARSED")

    targets = {args.domain: domains[args.domain]} if args.domain else domains

    for domain, cfg in targets.items():
        out = run_guard(domain, cfg["mode"], args.env)
        print(out.strip())
        print(f"REGISTRY_DOMAIN_VALIDATE=PASS domain={domain} mode={cfg['mode']} policy={cfg['deploy_policy']}")

    print("WEBSITE_DEPLOY_MODE_REGISTRY_VALIDATE=PASS")


if __name__ == "__main__":
    main()
