#!/usr/bin/env python3
"""Force the system to use only local LLM (Ollama)"""

import os

def setup_local_llm_only():
    """Configure environment for local LLM only"""

    print("🔧 Configuring for Local LLM Only (Ollama)...")

    # Set environment variables
    os.environ['AUTOBLOGGER_USE_LOCAL_LLM'] = 'true'

    # Remove OpenAI API keys
    os.environ.pop('OPENAI_API_KEY', None)
    os.environ.pop('AUTOBLOGGER_OPENAI_API_KEY', None)

    # Update .env file to include the local LLM preference
    env_path = 'D:\websites\.env'
    try:
        with open(env_path, 'r') as f:
            content = f.read()

        # Add the local LLM setting if not present
        if 'AUTOBLOGGER_USE_LOCAL_LLM' not in content:
            content += '\nAUTOBLOGGER_USE_LOCAL_LLM=true\n'

        # Remove any OpenAI API key lines
        lines = content.split('\n')
        filtered_lines = []
        for line in lines:
            if not (line.startswith('OPENAI_API_KEY=') or line.startswith('AUTOBLOGGER_OPENAI_API_KEY=')):
                filtered_lines.append(line)

        new_content = '\n'.join(filtered_lines)

        with open(env_path, 'w') as f:
            f.write(new_content)

        print("✅ Updated .env file")

    except Exception as e:
        print(f"⚠️  Could not update .env file: {e}")

    # Test the configuration
    print("\n🧪 Testing configuration...")
    import sys
    sys.path.insert(0, 'src')

    try:
        from autoblogger.llm_client import load_llm_config
        config = load_llm_config()
        print(f"✅ Local LLM: {config.use_local_llm}")
        print(f"✅ Model: {config.model}")
        print(f"✅ API Key: {'NOT SET' if not config.api_key or config.api_key == 'local_llm' else 'SET'}")
        print("\n🎯 System configured for Ollama-only operation!")
    except Exception as e:
        print(f"❌ Configuration test failed: {e}")

if __name__ == "__main__":
    setup_local_llm_only()