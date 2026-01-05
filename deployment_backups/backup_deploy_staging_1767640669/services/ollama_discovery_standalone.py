#!/usr/bin/env python3
"""
Standalone Ollama Dynamic Discovery System
Works across Linux Mint, Windows, and macOS
"""

import os
import platform
import subprocess
import requests
from typing import List, Dict, Any, Optional, Tuple
from pathlib import Path
from dataclasses import dataclass
import json


@dataclass
class OllamaDiscoveryResult:
    """Result of Ollama discovery attempt"""

    available: bool
    api_url: Optional[str] = None
    cli_path: Optional[str] = None
    models: List[str] = None
    error: Optional[str] = None

    def __post_init__(self):
        if self.models is None:
            self.models = []


class OllamaDiscovery:
    """Dynamic discovery of Ollama installations across platforms"""

    @staticmethod
    def get_platform_paths() -> List[str]:
        """Get platform-specific paths where Ollama might be installed"""
        system = platform.system().lower()

        if system == "windows":
            return [
                "C:\\Program Files\\Ollama\\ollama.exe",
                "C:\\Program Files (x86)\\Ollama\\ollama.exe",
                "C:\\Users\\%USERNAME%\\AppData\\Local\\Ollama\\ollama.exe",
                "ollama.exe"  # In PATH
            ]
        elif system == "linux":
            return [
                "/usr/bin/ollama",
                "/usr/local/bin/ollama",
                "/snap/bin/ollama",
                "/opt/ollama/bin/ollama",
                "~/.local/bin/ollama",
                str(Path.home() / ".ollama" / "ollama"),
                "ollama"  # In PATH
            ]
        elif system == "darwin":  # macOS
            return [
                "/usr/local/bin/ollama",
                "/opt/homebrew/bin/ollama",
                "/usr/bin/ollama",
                str(Path.home() / ".ollama" / "ollama"),
                "ollama"  # In PATH
            ]
        else:
            return ["ollama"]  # Fallback to PATH

    @staticmethod
    def find_cli_path() -> Optional[str]:
        """Find the Ollama CLI executable"""
        paths = OllamaDiscovery.get_platform_paths()

        for path in paths:
            expanded_path = Path(path).expanduser()
            if expanded_path.exists() and expanded_path.is_file():
                return str(expanded_path)

            # Try running as command
            try:
                result = subprocess.run(
                    [path, "--version"],
                    capture_output=True,
                    text=True,
                    timeout=5
                )
                if result.returncode == 0 and "ollama" in result.stdout.lower():
                    return path
            except (subprocess.TimeoutExpired, FileNotFoundError, subprocess.SubprocessError):
                continue

        return None

    @staticmethod
    def discover_api_endpoints() -> List[str]:
        """Discover possible Ollama API endpoints"""
        endpoints = [
            "http://localhost:11434",
            "http://127.0.0.1:11434",
            "http://localhost:8080",  # Alternative port
            "http://127.0.0.1:8080",
        ]

        # Check environment variables
        env_url = os.environ.get("OLLAMA_HOST")
        if env_url:
            endpoints.insert(0, env_url)

        return endpoints

    @staticmethod
    def test_api_endpoint(url: str, timeout: int = 2) -> Tuple[bool, List[str]]:
        """Test if an Ollama API endpoint is accessible"""
        try:
            response = requests.get(f"{url}/api/tags", timeout=timeout)
            if response.status_code == 200:
                data = response.json()
                models = [model.get("name", "") for model in data.get("models", [])]
                return True, models
        except Exception:
            pass
        return False, []

    @classmethod
    def discover(cls) -> OllamaDiscoveryResult:
        """Discover Ollama installation and API endpoint"""
        result = OllamaDiscoveryResult(available=False)

        # First, find CLI path
        result.cli_path = cls.find_cli_path()

        # Then, find working API endpoint
        endpoints = cls.discover_api_endpoints()

        for endpoint in endpoints:
            available, models = cls.test_api_endpoint(endpoint)
            if available:
                result.available = True
                result.api_url = endpoint
                result.models = models
                break

        if not result.available:
            result.error = "No accessible Ollama API endpoint found. Make sure Ollama is running."

        return result

    @classmethod
    def get_recommended_config(cls) -> Dict[str, Any]:
        """Get recommended Ollama configuration for current system"""
        discovery = cls.discover()

        config = {
            "available": discovery.available,
            "api_url": discovery.api_url,
            "cli_path": discovery.cli_path,
            "models": discovery.models,
            "error": discovery.error
        }

        return config


class OllamaClient:
    """Client for interacting with Ollama API using dynamic discovery"""

    def __init__(self, base_url: Optional[str] = None, auto_discover: bool = True):
        """
        Initialize Ollama client with dynamic discovery

        Args:
            base_url: Explicit base URL to use, or None to auto-discover
            auto_discover: Whether to auto-discover Ollama if base_url not provided
        """
        if base_url:
            self.base_url = base_url.rstrip("/")
            self.discovery_result = None
        elif auto_discover:
            discovery = OllamaDiscovery.discover()
            if discovery.available and discovery.api_url:
                self.base_url = discovery.api_url.rstrip("/")
                self.discovery_result = discovery
                print(f"🤖 Auto-discovered Ollama at: {self.base_url}")
                if discovery.models:
                    print(f"📚 Available models: {', '.join(discovery.models)}")
            else:
                # Fallback to default
                self.base_url = "http://localhost:11434"
                self.discovery_result = discovery
                print(f"⚠️  Could not auto-discover Ollama, using fallback: {self.base_url}")
                if discovery.error:
                    print(f"   Error: {discovery.error}")
        else:
            self.base_url = "http://localhost:11434"
            self.discovery_result = None

        self.session = requests.Session()

    def is_available(self) -> bool:
        """Check if Ollama is running and available"""
        try:
            response = self.session.get(f"{self.base_url}/api/tags", timeout=5)
            return response.status_code == 200
        except Exception as e:
            print(f"⚠️  Ollama not available at {self.base_url}: {e}")
            return False

    def get_models(self) -> List[Dict[str, Any]]:
        """Get list of available models"""
        try:
            response = self.session.get(f"{self.base_url}/api/tags")
            if response.status_code == 200:
                return response.json().get("models", [])
            return []
        except Exception as e:
            print(f"⚠️  Error getting models: {e}")
            return []

    def generate(self, model: str, prompt: str, **kwargs) -> Dict[str, Any]:
        """Generate text using Ollama"""
        try:
            payload = {"model": model, "prompt": prompt, "stream": False, **kwargs}

            response = self.session.post(f"{self.base_url}/api/generate", json=payload, timeout=60)

            if response.status_code == 200:
                return response.json()
            else:
                raise Exception(f"Ollama API error: {response.status_code}")

        except Exception as e:
            print(f"⚠️  Error generating text: {e}")
            raise


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
        try:
            exists = Path(path).expanduser().exists() if Path(path).expanduser().is_absolute() else "N/A (in PATH)"
        except:
            exists = "Error checking"
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
                    print(f"Response: {response.get('response', '').strip()}")
                    print("✅ Generation test successful!")
                except Exception as e:
                    print(f"❌ Generation test failed: {e}")

    except Exception as e:
        print(f"❌ Client test failed: {e}")


def main():
    """Main test function"""
    print("🧪 Ollama Dynamic Discovery Test")
    print("This script tests cross-platform Ollama discovery")
    print(f"Running on: {platform.system()} {platform.release()}")
    print("=" * 60)

    # Test discovery
    discovery = test_discovery()

    # Test client
    test_client(discovery)

    # Summary and instructions
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
        print("\n🚀 You can now use Ollama in your applications without hardcoded paths!")
    else:
        print("❌ Ollama is not available")
        print("\n📋 To install Ollama:")

        system = platform.system().lower()
        if system == "linux":
            print("   # Ubuntu/Debian/Linux Mint:")
            print("   curl -fsSL https://ollama.ai/install.sh | sh")
            print("   ollama serve &")
            print("   ollama pull llama3.2  # or your preferred model")
        elif system == "windows":
            print("   # Windows:")
            print("   1. Download from: https://ollama.ai/download")
            print("   2. Install and run Ollama")
            print("   3. Run: ollama pull llama3.2")
        elif system == "darwin":
            print("   # macOS:")
            print("   brew install ollama")
            print("   ollama serve &")
            print("   ollama pull llama3.2")

        print("\n🔄 After installation, run this script again to test!")


if __name__ == "__main__":
    main()