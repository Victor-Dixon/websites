#!/usr/bin/env python3
"""
Auto-Deployment Hook Script
===========================

Delegates to the SSOT implementation in ops/deployment/auto_deploy_hook.py.
"""

from __future__ import annotations

import runpy
import sys
from pathlib import Path


def main() -> int:
    # True shim: delegate to SSOT, but never brick commits if deploy deps are absent.
    repo_root = Path(__file__).resolve().parents[1]
    if str(repo_root) not in sys.path:
        sys.path.insert(0, str(repo_root))

    try:
        runpy.run_module("ops.deployment.auto_deploy_hook", run_name="__main__")
        return 0
    except ModuleNotFoundError as e:
        print(f"⚠️  auto-deploy SSOT module missing: {e}. Skipping deploy.")
        return 0
    except SystemExit as e:
        # Preserve explicit exits from SSOT (e.g., real deploy failures).
        code = int(getattr(e, "code", 0) or 0)
        return code
    except Exception as e:
        print(f"⚠️  auto-deploy hook errored; skipping deploy to avoid blocking commits: {e}")
        return 0


if __name__ == "__main__":
    raise SystemExit(main())
