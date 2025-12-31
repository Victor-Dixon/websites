#!/usr/bin/env python3
"""
Auto-Deployment Hook Script
===========================

Delegates to the SSOT implementation in ops/deployment/auto_deploy_hook.py.
"""

import runpy
import sys
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parents[1]
if str(REPO_ROOT) not in sys.path:
    sys.path.insert(0, str(REPO_ROOT))


if __name__ == "__main__":
    runpy.run_module("ops.deployment.auto_deploy_hook", run_name="__main__")
