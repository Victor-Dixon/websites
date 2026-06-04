#!/usr/bin/env python3
"""Render Discord Architect worklog JSON into a static website surface."""

from __future__ import annotations

import argparse
import html
import json
from pathlib import Path
from typing import Any


DEFAULT_IN = Path("data/worklog/discord_architect_worklog.json")
DEFAULT_OUT = Path("public/worklog/discord-architect.html")


def load_worklog(path: Path) -> dict[str, Any]:
    data = json.loads(path.read_text(encoding="utf-8"))
    if not isinstance(data, dict):
        raise ValueError(f"Expected worklog object: {path}")
    if data.get("integrity", {}).get("fabricated_entries") is not False:
        raise ValueError("Worklog integrity check failed: fabricated_entries must be false")
    entries = data.get("entries")
    if not isinstance(entries, list):
        raise ValueError("Worklog entries must be a list")
    return data


def render_entry(entry: dict[str, Any]) -> str:
    task = html.escape(str(entry.get("task", "")))
    status = html.escape(str(entry.get("status", "")))
    summary = html.escape(str(entry.get("summary", "")))
    report = html.escape(str(entry.get("report", "")))
    verification = html.escape(str(entry.get("verification", "")))
    parse_warning = html.escape(str(entry.get("parse_warning", "")))

    warning_html = ""
    if parse_warning:
        warning_html = f'<p class="warning"><strong>Parse warning:</strong> {parse_warning}</p>'

    return f"""
    <article class="worklog-card" data-status="{status}">
      <div class="card-topline">
        <span class="status">{status}</span>
        <code>{task}</code>
      </div>
      <p>{summary}</p>
      <dl>
        <dt>Verification</dt>
        <dd>{verification}</dd>
        <dt>Report</dt>
        <dd><code>{report}</code></dd>
      </dl>
      {warning_html}
    </article>
    """


def render_html(worklog: dict[str, Any], source_path: Path) -> str:
    entries = worklog["entries"]
    cards = "\n".join(render_entry(entry) for entry in entries)

    return f"""<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Discord Architect Worklog</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {{
      color-scheme: dark;
      --bg: #121212;
      --panel: #1a1a1a;
      --text: #ededed;
      --muted: #bbbbbb;
      --border: #333333;
      --accent: #218838;
      --warn: #d6a84f;
    }}
    body {{
      margin: 0;
      font-family: Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.5;
    }}
    main {{
      max-width: 1080px;
      margin: 0 auto;
      padding: 40px 20px;
    }}
    .hero {{
      border: 1px solid var(--border);
      background: linear-gradient(135deg, #1a1a1a, #101010);
      border-radius: 18px;
      padding: 28px;
      margin-bottom: 24px;
    }}
    h1 {{
      margin: 0 0 8px;
      font-size: clamp(2rem, 5vw, 4rem);
    }}
    .meta {{
      color: var(--muted);
      margin: 0;
    }}
    .grid {{
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 16px;
    }}
    .worklog-card {{
      border: 1px solid var(--border);
      border-left: 5px solid var(--accent);
      background: var(--panel);
      border-radius: 14px;
      padding: 18px;
    }}
    .card-topline {{
      display: flex;
      gap: 10px;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }}
    .status {{
      background: var(--accent);
      color: white;
      border-radius: 999px;
      padding: 3px 10px;
      font-size: .85rem;
      font-weight: 700;
    }}
    code {{
      color: var(--muted);
      overflow-wrap: anywhere;
    }}
    dt {{
      color: var(--muted);
      font-size: .85rem;
      margin-top: 12px;
    }}
    dd {{
      margin: 0;
    }}
    .warning {{
      color: var(--warn);
    }}
  </style>
</head>
<body>
  <main>
    <section class="hero">
      <h1>Discord Architect Worklog</h1>
      <p class="meta">Report-backed build log. No fabricated entries.</p>
      <p class="meta">Entries: {worklog.get("entry_count", len(entries))} · Source: <code>{html.escape(str(source_path))}</code></p>
    </section>
    <section class="grid">
      {cards}
    </section>
  </main>
</body>
</html>
"""


def write_rendered(worklog_path: Path, out_path: Path) -> dict[str, Any]:
    worklog = load_worklog(worklog_path)
    html_text = render_html(worklog, worklog_path)
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(html_text, encoding="utf-8")
    return {
        "status": "PASS",
        "entries": len(worklog["entries"]),
        "out": str(out_path),
        "source": str(worklog_path),
    }


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Render Discord Architect worklog HTML.")
    parser.add_argument("--input", type=Path, default=DEFAULT_IN)
    parser.add_argument("--output", type=Path, default=DEFAULT_OUT)
    return parser


def main(argv: list[str] | None = None) -> int:
    args = build_parser().parse_args(argv)
    result = write_rendered(args.input, args.output)
    print(f"RENDER_STATUS={result['status']}")
    print(f"RENDER_ENTRIES={result['entries']}")
    print(f"RENDER_OUT={result['out']}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
