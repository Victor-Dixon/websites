#!/usr/bin/env bash
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"
python3 runtime/tools/websites_deploy_truth_gate_index.py
