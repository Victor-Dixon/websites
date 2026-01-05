from __future__ import annotations

import os
import sys
from dataclasses import dataclass
from typing import Any, Optional
from pathlib import Path

import requests

from .prompt_builder import Prompt

# Load environment variables first
try:
    from dotenv import load_dotenv
    load_dotenv()
    print("✅ Loaded environment variables from .env")
except ImportError:
    print("⚠️  dotenv not available, using system environment only")

# Try to import Ollama support with dynamic discovery
try:
    # First try the integrated version
    sys.path.insert(0, str(Path(__file__).parent.parent.parent / "Agent_Cellphone_V2_Repository" / "src" / "integrations" / "jarvis"))
    from ollama_integration import OllamaClient
    OLLAMA_AVAILABLE = True
except ImportError:
    # Fallback to standalone discovery version
    try:
        sys.path.insert(0, str(Path(__file__).parent.parent.parent))
        from ollama_discovery_standalone import OllamaClient
        OLLAMA_AVAILABLE = True
    except ImportError:
        OLLAMA_AVAILABLE = False

# Try subprocess Ollama
try:
    import subprocess
    SUBPROCESS_AVAILABLE = True
except ImportError:
    SUBPROCESS_AVAILABLE = False


@dataclass(frozen=True)
class LlmConfig:
    api_key: str
    base_url: str
    model: str
    timeout_s: int
    use_local_llm: bool = True  # Prefer Ollama over API


def load_llm_config() -> LlmConfig:
    """
    Load LLM configuration with Ollama as primary, OpenAI as fallback.
    Environment variables can override defaults.
    """
    # Default to Ollama (local LLM)
    use_local_llm = os.environ.get("AUTOBLOGGER_USE_LOCAL_LLM", "true").lower() == "true"

    # Get preferred model
    model = os.environ.get("AUTOBLOGGER_OLLAMA_MODEL") or os.environ.get("AUTOBLOGGER_OPENAI_MODEL")

    # Auto-select best available model if none specified
    if not model:
        if use_local_llm and OLLAMA_AVAILABLE:
            try:
                client = OllamaClient()
                if client.is_available():
                    available_models = client.get_models()
                    model_names = [m.get("name") for m in available_models]

                    # Prefer larger, more capable models in order
                    preferred_order = [
                        "qwen2.5:7b", "qwen2.5", "llama3.2:3b", "llama3.2",
                        "mistral:latest", "dolphin-mistral:latest", "codellama"
                    ]

                    for preferred in preferred_order:
                        for available in model_names:
                            if preferred in available:
                                model = available
                                print(f"🤖 Auto-selected Ollama model: {model}")
                                break
                        if model:
                            break

                    # Ultimate fallback
                    if not model and model_names:
                        model = model_names[0]
                        print(f"🤖 Using first available Ollama model: {model}")

            except Exception as e:
                print(f"⚠️  Ollama auto-selection failed: {e}")

        # Final fallback if Ollama not available or not preferred
        if not model:
            model = "gpt-4o-mini"  # Good OpenAI fallback
            use_local_llm = False
            print(f"🔄 Falling back to OpenAI model: {model}")

    # API key configuration
    api_key = os.environ.get("AUTOBLOGGER_OPENAI_API_KEY") or os.environ.get("OPENAI_API_KEY") or ""
    base_url = os.environ.get("AUTOBLOGGER_OPENAI_BASE_URL") or "https://api.openai.com/v1"
    timeout_s = int(os.environ.get("AUTOBLOGGER_OPENAI_TIMEOUT_S") or "120")

    # For local LLM, force API key to placeholder to prevent OpenAI fallback
    if use_local_llm:
        api_key = "local_llm"  # Always use placeholder for local LLM to prevent fallback
        return LlmConfig(
            api_key=api_key,
            base_url=base_url,
            model=model,
            timeout_s=timeout_s,
            use_local_llm=True
        )

    # For API-only mode, require API key
    if not api_key:
        raise RuntimeError(
            "Missing OpenAI API key. Either:\n"
            "1. Set AUTOBLOGGER_OPENAI_API_KEY (or OPENAI_API_KEY) for OpenAI API\n"
            "2. Set AUTOBLOGGER_USE_LOCAL_LLM=true to use local Ollama\n"
            "3. Ensure Ollama is running with compatible models"
        )

    return LlmConfig(
        api_key=api_key,
        base_url=base_url,
        model=model,
        timeout_s=timeout_s,
        use_local_llm=False
    )

    # API key only needed for OpenAI fallback
    api_key = os.environ.get("AUTOBLOGGER_OPENAI_API_KEY") or os.environ.get("OPENAI_API_KEY") or ""
    base_url = os.environ.get("AUTOBLOGGER_OPENAI_BASE_URL") or "https://api.openai.com/v1"
    timeout_s = int(os.environ.get("AUTOBLOGGER_OPENAI_TIMEOUT_S") or "90")  # Reasonable timeout for local LLM

    # For local LLM, API key is not required
    if use_local_llm:
        if not api_key:
            api_key = "local_llm"  # Placeholder for local LLM
        return LlmConfig(api_key=api_key, base_url=base_url.rstrip("/"), model=model, timeout_s=timeout_s, use_local_llm=True)

    # For API-only mode, require API key
    if not api_key:
        raise RuntimeError(
            "Missing API key. Set AUTOBLOGGER_OPENAI_API_KEY (or OPENAI_API_KEY) to enable generation, or set AUTOBLOGGER_USE_LOCAL_LLM=true to use local Ollama."
        )

    return LlmConfig(api_key=api_key, base_url=base_url.rstrip("/"), model=model, timeout_s=timeout_s, use_local_llm=False)


def generate_markdown(prompt: Prompt, *, cfg: LlmConfig) -> str:
    """Generate markdown using Ollama (preferred) or OpenAI API (fallback)"""

    # Try Ollama subprocess first (more reliable for local LLM)
    if cfg.use_local_llm and SUBPROCESS_AVAILABLE:
        try:
            print(f"🤖 Using subprocess Ollama: {cfg.model}")

            # Combine prompts for subprocess
            full_prompt = f"{prompt.system}\n\n{prompt.user}"

            # Use subprocess to call Ollama with generous timeout
            ollama_timeout = min(cfg.timeout_s, 180)  # Allow up to 3 minutes for long content
            result = subprocess.run(
                ['ollama', 'run', cfg.model],
                input=full_prompt,
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                encoding="utf-8",
                timeout=ollama_timeout,
                check=True
            )

            content = result.stdout.strip()
            if content:
                # Handle JSON response format from ollama
                try:
                    import json
                    data = json.loads(content)
                    if isinstance(data, dict) and 'response' in data:
                        content = data['response'].strip()
                    elif isinstance(data, dict) and 'content' in data:
                        content = data['content'].strip()
                except json.JSONDecodeError:
                    # Not JSON, use as-is
                    pass

                if content:
                    print("✅ Content generated successfully (subprocess Ollama)")
                    return content

            print("⚠️  Ollama subprocess returned empty response")

        except subprocess.TimeoutExpired:
            print(f"⚠️  Ollama subprocess timed out after {ollama_timeout}s")
        except subprocess.CalledProcessError as e:
            error_msg = e.stderr.strip() if e.stderr else str(e)
            print(f"⚠️  Ollama subprocess error: {error_msg}")
            if "model" in error_msg.lower() and "not found" in error_msg.lower():
                print(f"💡 Try: ollama pull {cfg.model}")
        except FileNotFoundError:
            print("⚠️  Ollama command not found. Is Ollama installed?")
        except Exception as e:
            print(f"⚠️  Ollama subprocess unexpected error: {e}")

    # Try Ollama API as fallback if subprocess failed
    if cfg.use_local_llm and OLLAMA_AVAILABLE:
        try:
            client = OllamaClient()
            if client.is_available():
                print(f"🤖 Using Ollama API model: {cfg.model}")

                # Combine system and user prompts for Ollama
                full_prompt = f"{prompt.system}\n\n{prompt.user}"

                response = client.generate(
                    model=cfg.model,
                    prompt=full_prompt,
                    temperature=0.7,
                    max_tokens=4000  # Allow longer responses for blog posts
                )

                if response and hasattr(response, 'response') and response.response:
                    content = response.response.strip()
                    if content:
                        print("✅ Content generated successfully (Ollama API)")
                        return content

                print("⚠️  Ollama API returned empty response")

        except Exception as e:
            print(f"⚠️  Ollama API error: {e}")

    # Try subprocess Ollama (works even without API)
    if cfg.use_local_llm and SUBPROCESS_AVAILABLE:
        try:
            print(f"🤖 Using subprocess Ollama: {cfg.model}")

            # Combine prompts for subprocess
            full_prompt = f"{prompt.system}\n\n{prompt.user}"

            # Use subprocess to call Ollama with better error handling
            # First check if model is available
            try:
                check_result = subprocess.run(
                    ['ollama', 'show', cfg.model],
                    stdout=subprocess.PIPE,
                    stderr=subprocess.PIPE,
                    text=True,
                    timeout=10
                )
                if check_result.returncode != 0:
                    print(f"⚠️  Model '{cfg.model}' not found locally. Available models: check with 'ollama list'")
                    raise FileNotFoundError(f"Model {cfg.model} not available")
            except subprocess.TimeoutExpired:
                print("⚠️  Model check timed out")
            except FileNotFoundError:
                raise
            except Exception as e:
                print(f"⚠️  Could not verify model availability: {e}")

            # Run the generation (plain text output) with reasonable timeout
            ollama_timeout = min(cfg.timeout_s, 120)  # Allow up to 120 seconds for subprocess
            result = subprocess.run(
                ['ollama', 'run', cfg.model],
                input=full_prompt,
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                encoding="utf-8",
                timeout=ollama_timeout,
                check=True
            )

            content = result.stdout.strip()
            if content:
                # Handle JSON response format from ollama
                try:
                    import json
                    data = json.loads(content)
                    if isinstance(data, dict) and 'response' in data:
                        content = data['response'].strip()
                    elif isinstance(data, dict) and 'content' in data:
                        content = data['content'].strip()
                except json.JSONDecodeError:
                    # Not JSON, use as-is
                    pass

                if content:
                    print("✅ Content generated successfully (subprocess Ollama)")
                    return content

            print("⚠️  Ollama subprocess returned empty response")

        except subprocess.TimeoutExpired:
            print(f"⚠️  Ollama subprocess timed out after {cfg.timeout_s}s")
        except subprocess.CalledProcessError as e:
            error_msg = e.stderr.strip() if e.stderr else str(e)
            print(f"⚠️  Ollama subprocess error: {error_msg}")
            if "model" in error_msg.lower() and "not found" in error_msg.lower():
                print(f"💡 Try: ollama pull {cfg.model}")
        except FileNotFoundError:
            print("⚠️  Ollama command not found. Is Ollama installed?")
        except Exception as e:
            print(f"⚠️  Ollama subprocess unexpected error: {e}")

    # Only fallback to OpenAI if user allows it AND we have a valid API key
    if cfg.use_local_llm and (not cfg.api_key or cfg.api_key == "local_llm"):
        print("❌ Ollama failed and no OpenAI API key configured. Cannot generate content.")
        raise RuntimeError("All LLM options failed. Ollama is unavailable and no valid OpenAI API key provided.")

    print(f"🔄 Falling back to OpenAI API: {cfg.model}")
    url = f"{cfg.base_url}/chat/completions"
    headers = {"Authorization": f"Bearer {cfg.api_key}"}

    payload: dict[str, Any] = {
        "model": cfg.model if not cfg.use_local_llm else "gpt-4o-mini",  # Use GPT model for API
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

    print("✅ Content generated successfully (OpenAI API)")
    return content.strip()
