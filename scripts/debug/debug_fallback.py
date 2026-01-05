#!/usr/bin/env python3
"""Debug the OpenAI fallback condition"""

import os
os.environ['AUTOBLOGGER_USE_LOCAL_LLM'] = 'true'
os.environ.pop('OPENAI_API_KEY', None)
os.environ.pop('AUTOBLOGGER_OPENAI_API_KEY', None)

import sys
sys.path.insert(0, 'src')

from autoblogger.llm_client import load_llm_config

config = load_llm_config()
print("🔍 Fallback Condition Debug")
print("=" * 30)
print(f"use_local_llm: {config.use_local_llm}")
print(f"api_key: {repr(config.api_key)}")

condition1 = config.use_local_llm
condition2 = (config.api_key is None or config.api_key == "local_llm")
combined = condition1 and condition2

print(f"Condition 1 (use_local_llm): {condition1}")
print(f"Condition 2 (no valid api_key): {condition2}")
print(f"Combined (should not fallback): {combined}")

if combined:
    print("✅ Should NOT fallback to OpenAI")
else:
    print("❌ Will fallback to OpenAI")

# Test the exact code from llm_client.py
if config.use_local_llm and (not config.api_key or config.api_key == "local_llm"):
    print("✅ Code condition matches: should not fallback")
else:
    print("❌ Code condition fails: will fallback")