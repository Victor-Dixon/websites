import json
import subprocess
import sys
from pathlib import Path

from runtime.scripts.render_discord_architect_worklog_surface_001 import load_worklog, write_rendered


def test_renderer_rejects_fabricated_entries(tmp_path):
    worklog = tmp_path / "bad.json"
    worklog.write_text(
        json.dumps(
            {
                "integrity": {"fabricated_entries": True},
                "entries": [],
            }
        ),
        encoding="utf-8",
    )

    try:
        load_worklog(worklog)
    except ValueError as exc:
        assert "fabricated_entries" in str(exc)
    else:
        raise AssertionError("expected ValueError")


def test_renderer_writes_html(tmp_path):
    worklog = tmp_path / "worklog.json"
    out = tmp_path / "discord-architect.html"
    worklog.write_text(
        json.dumps(
            {
                "worklog": "discord_architect",
                "status": "PASS",
                "entry_count": 1,
                "integrity": {"fabricated_entries": False},
                "entries": [
                    {
                        "task": "unit_task_001",
                        "status": "PASS",
                        "summary": "unit summary",
                        "verification": "tests=PASS",
                        "report": "data/reports/unit.json",
                    }
                ],
            }
        ),
        encoding="utf-8",
    )

    result = write_rendered(worklog, out)

    assert result["status"] == "PASS"
    assert result["entries"] == 1
    html = out.read_text(encoding="utf-8")
    assert "Discord Architect Worklog" in html
    assert "unit_task_001" in html
    assert "No fabricated entries" in html


def test_renderer_cli(tmp_path):
    worklog = tmp_path / "worklog.json"
    out = tmp_path / "page.html"
    worklog.write_text(
        json.dumps(
            {
                "worklog": "discord_architect",
                "status": "PASS",
                "entry_count": 1,
                "integrity": {"fabricated_entries": False},
                "entries": [
                    {
                        "task": "cli_task_001",
                        "status": "PASS",
                        "summary": "cli summary",
                        "verification": "cli=PASS",
                        "report": "data/reports/cli.json",
                    }
                ],
            }
        ),
        encoding="utf-8",
    )

    result = subprocess.run(
        [
            sys.executable,
            "runtime/scripts/render_discord_architect_worklog_surface_001.py",
            "--input",
            str(worklog),
            "--output",
            str(out),
        ],
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        check=False,
    )

    assert result.returncode == 0, result.stderr
    assert "RENDER_STATUS=PASS" in result.stdout
    assert out.exists()
