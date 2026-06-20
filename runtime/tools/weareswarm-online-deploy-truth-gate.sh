#!/usr/bin/env bash
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"
python3 runtime/tools/weareswarm_online_deploy_truth_gate.py
