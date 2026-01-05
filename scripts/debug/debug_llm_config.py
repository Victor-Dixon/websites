#!/usr/bin/env python3
"""Debug LLM configuration"""

import os
import sys

# Force local LLM
os.environ['AUTOBLOGGER_USE_LOCAL_LLM'] = 'true'
os.environ.pop('OPENAI_API_KEY', None)
os.environ.pop('AUTOBLOGGER_OPENAI_API_KEY', None)

sys.path.insert(0, 'src')
from autoblogger.llm_client import load_llm_config

print("🔍 Debugging LLM Configuration")
print("=" * 40)

config = load_llm_config()
print(f"Local LLM: {config.use_local_llm}")
print(f"API key: '{config.api_key}'")
print(f"Model: {config.model}")

has_valid_api_key = bool(config.api_key and config.api_key != "local_llm")
print(f"Has valid API key: {has_valid_api_key}")

should_fallback = not config.use_local_llm or has_valid_api_key
print(f"Should fallback to OpenAI: {should_fallback}")

if config.use_local_llm and not has_valid_api_key:
    print("✅ Configuration is correct for Ollama-only operation")
else:
    print("❌ Configuration will attempt OpenAI fallback")