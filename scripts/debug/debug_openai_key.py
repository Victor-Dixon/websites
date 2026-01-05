#!/usr/bin/env python3
"""Debug OpenAI API key sources"""

import os
from dotenv import load_dotenv

print('🔍 Debugging OpenAI API key sources...')
print('=' * 50)

print('Before loading .env:')
print(f'  OPENAI_API_KEY: {os.environ.get("OPENAI_API_KEY", "NOT SET")}')
print(f'  AUTOBLOGGER_OPENAI_API_KEY: {os.environ.get("AUTOBLOGGER_OPENAI_API_KEY", "NOT SET")}')

load_dotenv()

print('\nAfter loading .env:')
print(f'  OPENAI_API_KEY: {os.environ.get("OPENAI_API_KEY", "NOT SET")}')
print(f'  AUTOBLOGGER_OPENAI_API_KEY: {os.environ.get("AUTOBLOGGER_OPENAI_API_KEY", "NOT SET")}')

print('\nChecking .env file content:')
try:
    with open('.env', 'r') as f:
        content = f.read()
        lines = content.split('\n')
        openai_lines = [line for line in lines if 'openai' in line.lower()]
        if openai_lines:
            print('  ❌ Found OpenAI lines in .env:')
            for line in openai_lines:
                print(f'    {line}')
        else:
            print('  ✅ No OpenAI lines in .env')
except Exception as e:
    print(f'  Error reading .env: {e}')

print('\nTesting LLM client configuration:')
try:
    import sys
    sys.path.insert(0, 'src')
    from autoblogger.llm_client import load_llm_config

    config = load_llm_config()
    print(f'  ✅ Config loaded: use_local_llm={config.use_local_llm}, model={config.model}')
    print(f'  API key status: {"SET" if config.api_key and config.api_key != "local_llm" else "NOT SET"}')

except Exception as e:
    print(f'  ❌ LLM config error: {e}')