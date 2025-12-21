from __future__ import annotations

import os
from dataclasses import dataclass
from typing import Any

import requests

from .prompt_builder import Prompt


@dataclass(frozen=True)
class LlmConfig:
    api_key: str
    base_url: str
    model: str
    timeout_s: int


def load_llm_config() -> LlmConfig:
    api_key = os.environ.get("AUTOBLOGGER_OPENAI_API_KEY") or os.environ.get("OPENAI_API_KEY") or ""
    base_url = os.environ.get("AUTOBLOGGER_OPENAI_BASE_URL") or "https://api.openai.com/v1"
    model = os.environ.get("AUTOBLOGGER_OPENAI_MODEL") or "gpt-4o-mini"
    timeout_s = int(os.environ.get("AUTOBLOGGER_OPENAI_TIMEOUT_S") or "60")

    if not api_key:
        raise RuntimeError(
            "Missing API key. Set AUTOBLOGGER_OPENAI_API_KEY (or OPENAI_API_KEY) to enable generation."
        )

    return LlmConfig(api_key=api_key, base_url=base_url.rstrip("/"), model=model, timeout_s=timeout_s)


def generate_markdown(prompt: Prompt, *, cfg: LlmConfig) -> str:
    url = f"{cfg.base_url}/chat/completions"
    headers = {"Authorization": f"Bearer {cfg.api_key}"}

    payload: dict[str, Any] = {
        "model": cfg.model,
        "messages": [
            {"role": "system", "content": prompt.system},
            {"role": "user", "content": prompt.user},
        ],
        "temperature": 0.7,
    }

    resp = requests.post(url, headers=headers, json=payload, timeout=cfg.timeout_s)
    if resp.status_code >= 400:
        raise RuntimeError(f"LLM request failed: HTTP {resp.status_code}: {resp.text[:500]}")

    data = resp.json()
    content = (
        (data.get("choices") or [{}])[0]
        .get("message", {})
        .get("content")
    )
    if not content or not isinstance(content, str):
        raise RuntimeError("LLM response missing message.content")

    return content.strip()
