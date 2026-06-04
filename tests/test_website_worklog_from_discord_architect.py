import json
import subprocess
import sys
from pathlib import Path

from runtime.scripts.build_website_worklog_from_discord_architect_001 import build_worklog


def test_build_worklog_uses_existing_reports_only(tmp_path):
    report_dir = tmp_path / "reports"
    report_dir.mkdir()

    (report_dir / "verify_discord_architect_clean_worktree_001.json").write_text(
        json.dumps(
            {
                "task": "verify_discord_architect_clean_worktree_001",
                "status": "PASS",
                "verification": {"dry_run_bridge": "PASS"},
                "next_lane": "connect_discord_architect_to_website_worklog_001",
            }
        ),
        encoding="utf-8",
    )

    worklog = build_worklog(report_dir)

    assert worklog["status"] == "PASS"
    assert worklog["entry_count"] == 1
    assert worklog["integrity"]["fabricated_entries"] is False
    assert worklog["entries"][0]["task"] == "verify_discord_architect_clean_worktree_001"


def test_worklog_cli_writes_json_and_md(tmp_path):
    report_dir = tmp_path / "reports"
    out_json = tmp_path / "worklog.json"
    out_md = tmp_path / "worklog.md"
    report_dir.mkdir()

    (report_dir / "build_discord_architect_invocation_adapter_001.json").write_text(
        json.dumps(
            {
                "task": "build_discord_architect_invocation_adapter_001",
                "status": "PASS",
                "verification": {"pytest": "PASS"},
            }
        ),
        encoding="utf-8",
    )

    result = subprocess.run(
        [
            sys.executable,
            "runtime/scripts/build_website_worklog_from_discord_architect_001.py",
            "--report-dir",
            str(report_dir),
            "--out-json",
            str(out_json),
            "--out-md",
            str(out_md),
        ],
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        check=False,
    )

    assert result.returncode == 0, result.stderr
    assert "WORKLOG_STATUS=PASS" in result.stdout
    assert out_json.exists()
    assert out_md.exists()
    data = json.loads(out_json.read_text(encoding="utf-8"))
    assert data["entry_count"] == 1

def test_build_worklog_recovers_first_json_object_with_trailing_data(tmp_path):
    report_dir = tmp_path / "reports"
    report_dir.mkdir()

    report = report_dir / "add_discord_architect_live_dispatch_gate_001.json"
    report.write_text(
        '{"task":"add_discord_architect_live_dispatch_gate_001","status":"PASS"}\\n{"extra":true}\\n',
        encoding="utf-8",
    )

    worklog = build_worklog(report_dir)

    assert worklog["status"] == "PASS"
    assert worklog["entry_count"] == 1
    assert worklog["entries"][0]["task"] == "add_discord_architect_live_dispatch_gate_001"
    assert "JSONDecodeError recovered" in worklog["entries"][0]["parse_warning"]
