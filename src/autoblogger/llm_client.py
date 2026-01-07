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

    # For API-only mode, warn but don't require API key (graceful fallback available)
    if not use_local_llm and not api_key:
        print("⚠️  OpenAI API mode selected but no API key configured. System will use graceful fallback if LLM calls fail.")
        api_key = "no_key_configured"  # Placeholder to prevent errors

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
    """Generate markdown using Ollama models (preferred) or OpenAI API (fallback)"""

    # Try multiple Ollama models in fallback order
    if cfg.use_local_llm and SUBPROCESS_AVAILABLE:
        # Get available models for fallback
        available_models = []
        try:
            # Check what models are available locally
            list_result = subprocess.run(
                ['ollama', 'list'],
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                timeout=10
            )
            if list_result.returncode == 0:
                lines = list_result.stdout.strip().split('\n')
                if len(lines) > 1:  # Skip header line
                    for line in lines[1:]:
                        model_name = line.split()[0] if line.strip() else None
                        if model_name:
                            available_models.append(model_name)
        except Exception as e:
            print(f"⚠️  Could not check available Ollama models: {e}")

        # Priority order: Qwen → Mistral → other available models
        model_priority = [
            cfg.model,  # User's preferred model first
            "qwen2.5:7b", "qwen2.5", "qwen:7b", "qwen",  # Qwen models
            "mistral:latest", "mistral:7b", "mistral",    # Mistral models
            "llama3.2:3b", "llama3.2", "llama3:8b",        # Llama models
            "codellama:latest", "codellama"                # Code models
        ]

        # Add any other available models not in priority list
        for model in available_models:
            if model not in model_priority:
                model_priority.append(model)

        print(f"🤖 Trying Ollama models in priority order: {model_priority[:5]}...")

        for attempt_model in model_priority:
            if attempt_model in available_models or attempt_model == cfg.model:
                try:
                    print(f"🤖 Trying Ollama model: {attempt_model}")

                    # Combine prompts for subprocess
                    full_prompt = f"{prompt.system}\n\n{prompt.user}"

                    # Use subprocess to call Ollama with generous timeout
                    ollama_timeout = min(cfg.timeout_s, 180)  # Allow up to 3 minutes for long content
                    result = subprocess.run(
                        ['ollama', 'run', attempt_model],
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
                            print(f"✅ Content generated successfully (Ollama: {attempt_model})")
                            return content

                    print(f"⚠️  {attempt_model} returned empty response")

                except subprocess.TimeoutExpired:
                    print(f"⚠️  {attempt_model} timed out after {ollama_timeout}s")
                    continue  # Try next model
                except subprocess.CalledProcessError as e:
                    error_msg = e.stderr.strip() if e.stderr else str(e)
                    print(f"⚠️  {attempt_model} error: {error_msg}")
                    if "model" in error_msg.lower() and "not found" in error_msg.lower():
                        print(f"💡 Model {attempt_model} not available locally")
                    continue  # Try next model
                except FileNotFoundError:
                    print("⚠️  Ollama command not found. Is Ollama installed?")
                    break  # No point trying more models if Ollama isn't installed
                except Exception as e:
                    print(f"⚠️  {attempt_model} unexpected error: {e}")
                    continue  # Try next model

        print("⚠️  All Ollama models failed or unavailable")

    # Ollama models tried above - if we get here, all local models failed
    print("🔄 Local Ollama models exhausted, trying external APIs...")

    # Fallback Priority: Ollama (Qwen/Mistral) → OpenAI → Graceful Failure
    print("🔄 Local Ollama models failed, checking external API fallback...")

    has_valid_openai_key = cfg.api_key and cfg.api_key not in ["local_llm", ""] and cfg.api_key.startswith("sk-")

    if cfg.use_local_llm and not has_valid_openai_key:
        # No external API fallback available - provide graceful degradation
        print("⚠️  All LLM services unavailable. Providing fallback content.")
        fallback_content = f"""# Content Generation Unavailable

**Status:** All LLM services are currently unavailable

**Attempted (in priority order):**
1. ❌ Local Ollama LLM (Qwen, Mistral, Llama models)
2. ❌ OpenAI API (no valid API key configured)

**Solutions:**
1. **Start Ollama:** `ollama serve`
2. **Pull models:** `ollama pull qwen2.5:7b` or `ollama pull mistral:latest`
3. **Check models:** `ollama list`
4. **Add OpenAI key:** Set `AUTOBLOGGER_OPENAI_API_KEY` in `.env` file

**System Status:** Content generation is temporarily disabled, but all other website functions continue normally.

---
*Fallback generated: {__import__('datetime').datetime.now().isoformat()}*
*Application continues running normally*
"""
        print("📝 Returning fallback content (all LLMs unavailable)")
        return fallback_content

    # Try OpenAI fallback if available
    if has_valid_openai_key:
        try:
            print(f"🔄 Falling back to OpenAI API: {cfg.model}")
            url = f"{cfg.base_url}/chat/completions"
            headers = {"Authorization": f"Bearer {cfg.api_key}"}

            payload: dict[str, Any] = {
                "model": cfg.model if not cfg.use_local_llm else "gpt-4o-mini",
                "messages": [
                    {"role": "system", "content": prompt.system},
                    {"role": "user", "content": prompt.user},
                ],
                "temperature": 0.7,
                "max_tokens": 2000,  # Reasonable limit for content generation
            }

            resp = requests.post(url, headers=headers, json=payload, timeout=cfg.timeout_s)
            if resp.status_code >= 400:
                error_msg = f"OpenAI API failed: HTTP {resp.status_code}"
                print(f"⚠️  {error_msg}")
                # Don't raise error, fall back to graceful failure
            else:
                data = resp.json()
                content = (
                    (data.get("choices") or [{}])[0]
                    .get("message", {})
                    .get("content")
                )
                if content and isinstance(content, str):
                    print("✅ Content generated successfully (OpenAI API)")
                    return content.strip()

        except Exception as e:
            print(f"⚠️  OpenAI API error: {e}")

    # Ultimate fallback - graceful failure without breaking the app
    print("⚠️  All LLM services failed. Providing fallback content.")
    fallback_content = f"""# Automated Content Generation

**Status:** Content generation services are temporarily unavailable

**Attempted Methods:**
- Local Ollama LLM: {'✅ Available' if OLLAMA_AVAILABLE else '❌ Unavailable'}
- OpenAI API: {'✅ Configured' if has_valid_openai_key else '❌ Not configured'}

**System Status:** The application continues to function normally. Content generation will resume when LLM services become available.

**Next Steps:**
1. Check Ollama: `ollama list` and `ollama serve`
2. Verify OpenAI API key in `.env` file
3. Restart services if needed

---
*Fallback generated: {__import__('datetime').datetime.now().isoformat()}*
*Application continues running normally*
"""
    print("📝 Returning fallback content (all LLMs failed)")
    return fallback_content
