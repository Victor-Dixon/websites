#!/usr/bin/env python3
"""
Test the improved autoblogger Ollama integration
"""

import sys
import os
from pathlib import Path

# Add current directory to path
sys.path.insert(0, '.')

from src.autoblogger.llm_client import load_llm_config, generate_markdown, OLLAMA_AVAILABLE
from src.autoblogger.prompt_builder import Prompt

def main():
    print('🔍 Testing Improved Autoblogger Ollama Integration')
    print('=' * 60)
    print(f'Ollama available: {OLLAMA_AVAILABLE}')

    if OLLAMA_AVAILABLE:
        config = load_llm_config()
        print(f'Config: use_local_llm={config.use_local_llm}, model={config.model}')

        # Test a simple generation
        prompt = Prompt(
            system='You are a helpful assistant.',
            user='Say hello in exactly 3 words.'
        )

        try:
            result = generate_markdown(prompt, cfg=config)
            print('✅ Generation successful!')
            print(f'Result: "{result}"')
        except Exception as e:
            print(f'❌ Generation failed: {e}')
    else:
        print('❌ Ollama not available - check imports')

if __name__ == "__main__":
    main()