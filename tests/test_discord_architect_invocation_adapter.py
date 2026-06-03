import json
import os
import subprocess
import sys
from pathlib import Path

from discord_architect.invocation_adapter import normalize_closeout_payload


def test_normalize_closeout_payload_contains_core_fields():
    payload = normalize_closeout_payload(
        {
            "task": "unit_task_001",
            "status": "PASS",
            "lane": "repo_fleet_self_healing_001",
            "actions_taken": "did the thing",
            "verification": "pytest pass",
            "commit_message": "Unit commit",
            "next_task": "runtime/tasks/next.yaml",
        }
    )

    assert payload["content"] == "PASS: unit_task_001"
    assert payload["dreamos_meta"]["task"] == "unit_task_001"
    fields = payload["embeds"][0]["fields"]
    values = {field["name"]: field["value"] for field in fields}
    assert values["Task"] == "unit_task_001"
    assert values["Status"] == "PASS"
    assert values["Actions Taken"] == "did the thing"
    assert values["Verification"] == "pytest pass"
    assert values["Commit"] == "Unit commit"
    assert values["Next Task"] == "runtime/tasks/next.yaml"


def test_adapter_cli_writes_payload(tmp_path):
    fixture = tmp_path / "closeout.json"
    output = tmp_path / "latest_paper_trade_payload.json"
    fixture.write_text(
        json.dumps(
            {
                "task": "cli_task_001",
                "status": "PASS",
                "actions_taken": "adapter cli wrote payload",
                "verification": "output exists",
            }
        ),
        encoding="utf-8",
    )

    result = subprocess.run(
        [
            sys.executable,
            "discord_architect/invocation_adapter.py",
            "--input",
            str(fixture),
            "--output",
            str(output),
        ],
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        check=False,
    )

    assert result.returncode == 0, result.stderr
    assert "ADAPTER_WRITE=PASS" in result.stdout
    data = json.loads(output.read_text(encoding="utf-8"))
    assert data["content"] == "PASS: cli_task_001"
    assert data["embeds"][0]["title"] == "DreamOS Closeout: cli_task_001 [PASS]"


def test_adapter_invoke_dry_run_without_webhook(tmp_path):
    selected = Path.home() / "projects/DreamVault/runtime/scripts/send_discord_paper_trade_payload.py"
    if not selected.exists():
        import pytest
        pytest.skip(f"selected sender missing: {selected}")

    fixture = tmp_path / "closeout.json"
    output = Path("runtime/trading/discord/latest_paper_trade_payload.json")
    fixture.write_text(
        json.dumps(
            {
                "task": "dry_run_task_001",
                "status": "PASS",
                "actions_taken": "dry run only",
                "verification": "no webhook env",
            }
        ),
        encoding="utf-8",
    )

    env = os.environ.copy()
    env.pop("DISCORD_WEBHOOK_URL", None)

    result = subprocess.run(
        [
            sys.executable,
            "discord_architect/invocation_adapter.py",
            "--input",
            str(fixture),
            "--output",
            str(output),
            "--sender",
            str(selected),
            "--invoke",
        ],
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        env=env,
        check=False,
    )

    assert result.returncode == 0, result.stderr
    assert "ADAPTER_WRITE=PASS" in result.stdout
    assert "DISCORD_SEND=DRY_RUN" in result.stdout
    assert "ADAPTER_INVOKE=PASS" in result.stdout
