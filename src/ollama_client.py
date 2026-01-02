"""
Ollama LLM Client for Dream.OS Tools Integration

Provides a local LLM interface using Ollama that can use tools from the project.
Compatible with the existing llm_client.py interface.
"""

from __future__ import annotations

import os
from dataclasses import dataclass
from typing import Any

import requests

from .autoblogger.prompt_builder import Prompt


@dataclass(frozen=True)
class OllamaConfig:
    base_url: str
    model: str
    timeout_s: int


def load_ollama_config() -> OllamaConfig:
    """Load Ollama configuration from environment variables."""
    base_url = os.environ.get("OLLAMA_BASE_URL") or "http://localhost:11434"
    model = os.environ.get("OLLAMA_MODEL") or "llama3.2"
    timeout_s = int(os.environ.get("OLLAMA_TIMEOUT_S") or "120")
    
    return OllamaConfig(
        base_url=base_url.rstrip("/"),
        model=model,
        timeout_s=timeout_s
    )


def generate_markdown(prompt: Prompt, *, cfg: OllamaConfig | None = None) -> str:
    """
    Generate markdown using Ollama.
    
    Compatible with the OpenAI-style interface used by autoblogger.
    """
    if cfg is None:
        cfg = load_ollama_config()
    
    url = f"{cfg.base_url}/api/chat"
    
    # Convert Prompt to Ollama format
    messages = [
        {"role": "system", "content": prompt.system},
        {"role": "user", "content": prompt.user},
    ]
    
    payload: dict[str, Any] = {
        "model": cfg.model,
        "messages": messages,
        "stream": False,
        "options": {
            "temperature": 0.7,
        }
    }
    
    try:
        resp = requests.post(url, json=payload, timeout=cfg.timeout_s)
        resp.raise_for_status()
        
        data = resp.json()
        content = data.get("message", {}).get("content", "")
        
        if not content or not isinstance(content, str):
            raise RuntimeError("Ollama response missing message.content")
        
        return content.strip()
    
    except requests.exceptions.RequestException as e:
        raise RuntimeError(f"Ollama request failed: {e}")


def check_ollama_connection(cfg: OllamaConfig | None = None) -> bool:
    """Check if Ollama is running and accessible."""
    if cfg is None:
        cfg = load_ollama_config()
    
    try:
        url = f"{cfg.base_url}/api/tags"
        resp = requests.get(url, timeout=5)
        return resp.status_code == 200
    except:
        return False


def list_available_models(cfg: OllamaConfig | None = None) -> list[str]:
    """List all available Ollama models."""
    if cfg is None:
        cfg = load_ollama_config()
    
    try:
        url = f"{cfg.base_url}/api/tags"
        resp = requests.get(url, timeout=5)
        resp.raise_for_status()
        
        data = resp.json()
        models = [model.get("name", "") for model in data.get("models", [])]
        return [m for m in models if m]
    except:
        return []


if __name__ == "__main__":
    # Test connection
    print("Testing Ollama connection...")
    if check_ollama_connection():
        print("✅ Ollama is running")
        models = list_available_models()
        print(f"Available models: {', '.join(models) if models else 'None'}")
    else:
        print("❌ Ollama is not running. Start it with: ollama serve")

