#!/usr/bin/env python3
"""
Test script for Ollama dynamic discovery system
"""

import importlib.util
import sys
from pathlib import Path

import pytest

# Add the repository path to sys.path so we can import the Ollama integration
repo_path = Path(__file__).parent / "Agent_Cellphone_V2_Repository"
sys.path.insert(0, str(repo_path))

# Also add the src directory
src_path = repo_path / "src"
sys.path.insert(0, str(src_path))

integration_spec = importlib.util.find_spec("integrations.jarvis.ollama_integration")
if integration_spec is None:
    pytest.skip(
        "Ollama integration unavailable on sys.path; skipping discovery tests.",
        allow_module_level=True,
    )

from integrations.jarvis.ollama_integration import OllamaDiscovery, OllamaClient
print("✅ Successfully imported Ollama integration")


def test_discovery():
    """Test the Ollama discovery system"""
    print("\n🔍 Testing Ollama Discovery System")
    print("=" * 50)

    # Test discovery
    discovery = OllamaDiscovery.discover()

    print(f"Available: {discovery.available}")
    print(f"API URL: {discovery.api_url}")
    print(f"CLI Path: {discovery.cli_path}")
    print(f"Models: {discovery.models}")
    if discovery.error:
        print(f"Error: {discovery.error}")

    print("\n🔧 Platform Paths Checked:")
    for path in OllamaDiscovery.get_platform_paths():
        exists = Path(path).expanduser().exists() if Path(path).expanduser().is_absolute() else "N/A (in PATH)"
        print(f"  - {path}: {exists}")

    print("\n🌐 API Endpoints Tested:")
    for endpoint in OllamaDiscovery.discover_api_endpoints():
        available, models = OllamaDiscovery.test_api_endpoint(endpoint, timeout=1)
        status = "✅ Available" if available else "❌ Not available"
        model_count = f" ({len(models)} models)" if available else ""
        print(f"  - {endpoint}: {status}{model_count}")

    return discovery


def test_client(discovery):
    """Test the Ollama client with discovered configuration"""
    print("\n🤖 Testing Ollama Client")
    print("=" * 50)

    if not discovery.available:
        print("❌ Skipping client test - Ollama not available")
        return

    try:
        client = OllamaClient()
        print(f"Client initialized with URL: {client.base_url}")

        # Test availability
        available = client.is_available()
        print(f"Client reports available: {available}")

        if available:
            # Test getting models
            models = client.get_models()
            print(f"Available models: {[m.get('name') for m in models]}")

            # Test generation with a simple prompt
            if models:
                model_name = models[0].get('name')
                print(f"\n🧪 Testing generation with model: {model_name}")

                try:
                    response = client.generate(
                        model=model_name,
                        prompt="Say hello in one word:",
                        temperature=0.1,
                        max_tokens=10
                    )
                    print(f"Response: {response.response.strip()}")
                    print("✅ Generation test successful!")
                except Exception as e:
                    print(f"❌ Generation test failed: {e}")

    except Exception as e:
        print(f"❌ Client test failed: {e}")


def main():
    """Main test function"""
    print("🧪 Ollama Dynamic Discovery Test")
    print("This script tests the new cross-platform Ollama discovery system")

    # Test discovery
    discovery = test_discovery()

    # Test client
    test_client(discovery)

    # Summary
    print("\n📊 SUMMARY")
    print("=" * 50)
    if discovery.available:
        print("✅ Ollama is available and working!")
        print(f"   API URL: {discovery.api_url}")
        print(f"   CLI Path: {discovery.cli_path}")
        print(f"   Models: {len(discovery.models)} available")
        print("\n💡 The dynamic discovery system should now work on:")
        print("   - Linux Mint (your laptop)")
        print("   - Windows (your desktop)")
        print("   - macOS (if you use it)")
    else:
        print("❌ Ollama is not available")
        print("   Make sure Ollama is installed and running")
        print("   On Linux: sudo apt install ollama && ollama serve")
        print("   On Windows: Download from https://ollama.ai/download")
        print("   Then run: ollama pull llama3.2  # or your preferred model")


if __name__ == "__main__":
    main()
