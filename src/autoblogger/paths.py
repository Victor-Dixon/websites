from __future__ import annotations

from pathlib import Path


def repo_root() -> Path:
    # /workspace/src/autoblogger/paths.py -> parents: autoblogger, src, repo
    return Path(__file__).resolve().parents[2]


def content_dir() -> Path:
    return repo_root() / "content"


def runtime_dir() -> Path:
    return repo_root() / "runtime"


def drafts_dir() -> Path:
    return content_dir() / "drafts"
