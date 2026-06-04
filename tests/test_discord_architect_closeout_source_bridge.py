import json
import os
import subprocess
import sys
from pathlib import Path

from discord_architect.closeout_source_bridge import canonicalize_cpc_payload, find_latest_cpc_json


def test_find_latest_cpc_json_by_mtime(tmp_path):
    old = tmp_path / "old.json"
    new = tmp_path / "new.json"
    old.write_text("{}", encoding="utf-8")
    new.write_text("{}", encoding="utf-8")

    base_ns = 1_700_000_000_000_000_000
    os.utime(old, ns=(base_ns, base_ns))
    os.utime(new, ns=(base_ns + 1_000_000, base_ns + 1_000_000))

    assert find_latest_cpc_json(tmp_path) == new


def test_canonicalize_cpc_payload_uses_closeout_fields(tmp_path):
    source = tmp_path / "cpc.json"
    raw = {
        "lane": "repo_fleet_self_healing_001",
        "REPORT": "data/reports/cpc/cpc.txt",
        "NEXT_LANE": "repo_fleet_self_healing_001",
        "NEXT_TASK": "runtime/tasks/repo_fleet_self_healing_002.yaml",
        "closeout": {
            "Task": "wire_task_001",
            "Status": "PASS",
            "Actions Taken": "wired closeout source",
            "Verification": "dry-run pass",
            "Commit Message": "Wire Discord Architect closeout source",
        },
    }

    payload = canonicalize_cpc_payload(raw, source)

    assert payload["task"] == "wire_task_001"
    assert payload["status"] == "PASS"
    assert payload["actions_taken"] == "wired closeout source"
    assert payload["verification"] == "dry-run pass"
    assert payload["commit_message"] == "Wire Discord Architect closeout source"
    assert payload["json"] == str(source)


def test_bridge_cli_with_explicit_cpc_json(tmp_path):
    cpc = tmp_path / "cpc.json"
    bridge_payload = tmp_path / "bridge_payload.json"
    output_payload = tmp_path / "latest_paper_trade_payload.json"

    cpc.write_text(
        json.dumps(
            {
                "lane": "repo_fleet_self_healing_001",
                "closeout": {
                    "Task": "bridge_cli_task_001",
                    "Status": "PASS",
                    "Actions Taken": "bridge cli ok",
                    "Verification": "output payload ok",
                },
            }
        ),
        encoding="utf-8",
    )

    result = subprocess.run(
        [
            sys.executable,
            "discord_architect/closeout_source_bridge.py",
            "--cpc-json",
            str(cpc),
            "--bridge-payload",
            str(bridge_payload),
            "--output",
            str(output_payload),
        ],
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        check=False,
    )

    assert result.returncode == 0, result.stderr
    assert "BRIDGE_PAYLOAD=PASS" in result.stdout
    assert "ADAPTER_WRITE=PASS" in result.stdout
    assert "BRIDGE_ADAPTER=PASS" in result.stdout

    payload = json.loads(output_payload.read_text(encoding="utf-8"))
    assert payload["content"] == "PASS: bridge_cli_task_001"
