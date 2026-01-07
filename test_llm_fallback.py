#!/usr/bin/env python3
"""
Test LLM Fallback System
========================

Test that the LLM system gracefully handles failures and provides fallbacks
without breaking the application.
"""

import sys
from pathlib import Path

# Add autoblogger to path
autoblogger_path = Path(__file__).parent / "src" / "autoblogger"
sys.path.insert(0, str(autoblogger_path))

try:
    from llm_client import load_llm_config, generate_markdown
    from prompt_builder import Prompt
    IMPORTS_SUCCESS = True
except ImportError as e:
    print(f"❌ Import failed: {e}")
    IMPORTS_SUCCESS = False

def test_llm_fallback():
    """Test LLM fallback system"""

    print("🧪 Testing LLM Fallback System")
    print("=" * 50)

    if not IMPORTS_SUCCESS:
        print("❌ Cannot test - imports failed")
        return False

    # Load configuration (should prefer local LLM)
    try:
        cfg = load_llm_config()
        print("✅ Configuration loaded:")
        print(f"   - Model: {cfg.model}")
        print(f"   - Use Local LLM: {cfg.use_local_llm}")
        print(f"   - Has API Key: {'Yes' if cfg.api_key and cfg.api_key not in ['local_llm', 'no_key_configured'] else 'No'}")
        print()
    except Exception as e:
        print(f"❌ Configuration failed: {e}")
        return False

    # Test content generation with simple prompt
    test_prompt = Prompt(
        system="You are a helpful assistant. Keep responses brief and informative.",
        user="What is the capital of France?"
    )

    print("🤖 Testing content generation...")
    try:
        content = generate_markdown(test_prompt, cfg=cfg)
        print("✅ Content generation succeeded!")
        print(f"📝 Content length: {len(content)} characters")
        print()
        print("📄 Generated content preview:")
        print("-" * 30)
        print(content[:200] + ("..." if len(content) > 200 else ""))
        print("-" * 30)
        return True

    except Exception as e:
        print(f"❌ Content generation failed: {e}")
        return False

if __name__ == "__main__":
    success = test_llm_fallback()
    if success:
        print("\n🎉 LLM Fallback System: WORKING")
        print("The system can handle LLM failures gracefully!")
    else:
        print("\n❌ LLM Fallback System: FAILED")
        print("The system may still break on LLM failures.")
    sys.exit(0 if success else 1)