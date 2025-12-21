from __future__ import annotations

from pathlib import Path


def load_example_snippets(glob_pattern: str | None, *, max_files: int = 5, max_chars: int = 6000) -> str:
    if not glob_pattern:
        return ""

    root = Path.cwd()
    paths = sorted(root.glob(glob_pattern))[:max_files]
    if not paths:
        return ""

    chunks: list[str] = []
    total = 0
    for p in paths:
        try:
            txt = p.read_text(encoding="utf-8")
        except Exception:
            continue

        # clip
        remaining = max_chars - total
        if remaining <= 0:
            break

        snippet = txt[:remaining]
        total += len(snippet)
        chunks.append(f"# EXAMPLE: {p.as_posix()}\n\n{snippet}\n")

    return "\n\n".join(chunks).strip()
