#!/usr/bin/env python3
"""
Setup and test the dreamscape autoblogger with Ollama integration
"""

import os
import sys
from pathlib import Path

def setup_dreamscape_autoblogger():
    """Setup and test the dreamscape autoblogger configuration"""

    print("🚀 Setting up Dreamscape Autoblogger with Ollama Integration")
    print("=" * 70)

    # Ensure we're in the right directory
    websites_dir = Path(__file__).parent
    os.chdir(websites_dir)

    # Load environment variables
    try:
        from dotenv import load_dotenv
        load_dotenv()
        print("✅ Loaded environment variables from .env")
    except ImportError:
        print("⚠️  python-dotenv not installed, environment variables may not load")

    # Test environment variables
    print("\n🔧 Testing Environment Configuration:")
    print("-" * 40)

    # Dreamscape WordPress vars
    dream_vars = {
        'DREAM_WP_URL': 'WordPress site URL',
        'DREAM_WP_USER': 'WordPress username',
        'DREAM_WP_APP_PASS': 'WordPress application password'
    }

    dream_configured = True
    for var, desc in dream_vars.items():
        value = os.environ.get(var, '')
        if value:
            masked = value[:10] + '...' + value[-5:] if len(value) > 15 else value
            print(f"✅ {var}: {masked} ({desc})")
        else:
            print(f"❌ {var}: NOT SET - {desc}")
            dream_configured = False

    # Autoblogger vars
    autoblogger_vars = {
        'AUTOBLOGGER_USE_LOCAL_LLM': 'Use local LLM (true/false)',
        'AUTOBLOGGER_OLLAMA_MODEL': 'Preferred Ollama model',
        'AUTOBLOGGER_OPENAI_API_KEY': 'OpenAI API key (fallback)'
    }

    print("\n🤖 Autoblogger Configuration:")
    for var, desc in autoblogger_vars.items():
        value = os.environ.get(var, '')
        if 'API_KEY' in var and value:
            masked = value[:10] + '...' + value[-5:] if len(value) > 15 else 'SET'
            print(f"🔑 {var}: {masked} ({desc})")
        elif value:
            print(f"✅ {var}: {value} ({desc})")
        else:
            print(f"⚠️  {var}: NOT SET - {desc}")

    # Test Ollama discovery
    print("\n🧪 Testing Ollama Integration:")
    print("-" * 40)

    try:
        # Import Ollama discovery
        sys.path.insert(0, str(websites_dir))
        from ollama_discovery_standalone import OllamaDiscovery

        discovery = OllamaDiscovery.discover()
        if discovery.available:
            print("✅ Ollama Available!")
            print(f"   📍 API URL: {discovery.api_url}")
            print(f"   🤖 Models: {', '.join(discovery.models[:3])}{'...' if len(discovery.models) > 3 else ''}")
        else:
            print("❌ Ollama Not Available")
            if discovery.error:
                print(f"   Error: {discovery.error}")

    except ImportError as e:
        print(f"❌ Ollama discovery import failed: {e}")

    # Test LLM client configuration
    print("\n🧠 Testing LLM Client Configuration:")
    print("-" * 40)

    try:
        sys.path.insert(0, str(websites_dir / "src"))
        from autoblogger.llm_client import load_llm_config, OLLAMA_AVAILABLE

        print(f"Ollama integration available: {OLLAMA_AVAILABLE}")

        config = load_llm_config()
        print(f"✅ LLM Config loaded successfully!")
        print(f"   🔄 Local LLM: {config.use_local_llm}")
        print(f"   🤖 Model: {config.model}")
        print(f"   ⏱️  Timeout: {config.timeout_s}s")
        if not config.use_local_llm:
            api_key_set = bool(config.api_key and config.api_key != "local_llm")
            print(f"   🔑 API Key: {'SET' if api_key_set else 'NOT SET'}")

    except Exception as e:
        print(f"❌ LLM client configuration failed: {e}")

    # Test WordPress connection
    print("\n🌐 Testing WordPress Connection:")
    print("-" * 40)

    if dream_configured:
        try:
            from autoblogger.wp_publisher import load_wp_env

            wp_config = load_wp_env(
                base_url_env='DREAM_WP_URL',
                user_env='DREAM_WP_USER',
                app_password_env='DREAM_WP_APP_PASS'
            )

            print("✅ WordPress config loaded!")
            print(f"   🌐 URL: {wp_config.base_url}")
            print(f"   👤 User: {wp_config.username}")
            print(f"   🔐 App Password: {'SET' if wp_config.app_password else 'NOT SET'}")

            # Test actual connection
            import requests
            from requests.auth import HTTPBasicAuth

            api_url = f"{wp_config.base_url}/wp-json/wp/v2/posts?per_page=1"
            auth = HTTPBasicAuth(wp_config.username, wp_config.app_password)

            response = requests.get(api_url, auth=auth, timeout=10)
            if response.status_code == 200:
                posts = response.json()
                print(f"✅ WordPress API connection successful! Found {len(posts)} posts.")
            else:
                print(f"❌ WordPress API connection failed: HTTP {response.status_code}")

        except Exception as e:
            print(f"❌ WordPress connection test failed: {e}")
    else:
        print("❌ WordPress not configured - skipping connection test")

    # Summary and recommendations
    print("\n📊 SETUP SUMMARY:")
    print("=" * 70)

    issues = []
    recommendations = []

    if not dream_configured:
        issues.append("WordPress credentials not configured")
        recommendations.append("Set DREAM_WP_URL, DREAM_WP_USER, DREAM_WP_APP_PASS in .env")

    if not OLLAMA_AVAILABLE:
        issues.append("Ollama integration not available")
        recommendations.append("Install Ollama or ensure ollama_discovery_standalone.py is available")

    try:
        config = load_llm_config()
        if config.use_local_llm and not discovery.available:
            issues.append("Local LLM preferred but Ollama not available")
            recommendations.append("Start Ollama service or set AUTOBLOGGER_USE_LOCAL_LLM=false")
    except:
        issues.append("LLM configuration failed")
        recommendations.append("Check LLM client setup")

    if issues:
        print("❌ ISSUES FOUND:")
        for issue in issues:
            print(f"   - {issue}")
        print("\n💡 RECOMMENDATIONS:")
        for rec in recommendations:
            print(f"   - {rec}")
    else:
        print("✅ ALL SYSTEMS GO!")
        print("   🎯 Dreamscape autoblogger is ready to run!")
        print("   🚀 Try: python -m autoblogger.run_daily --site dream")

    print("\n🎭 Dreamscape Autoblogger Status:")
    print("=" * 70)

    # Check if dreamscape content exists
    backlog_path = websites_dir / "content" / "backlogs" / "dream.yaml"
    calendar_path = websites_dir / "content" / "calendars" / "dream.yaml"

    if backlog_path.exists():
        print(f"✅ Backlog exists: {backlog_path}")
    else:
        print(f"❌ Backlog missing: {backlog_path}")

    if calendar_path.exists():
        print(f"✅ Calendar exists: {calendar_path}")
    else:
        print(f"❌ Calendar missing: {calendar_path}")

    # Count available episodes
    try:
        import yaml
        with open(backlog_path, 'r') as f:
            backlog = yaml.safe_load(f)

        ready_episodes = [ep for ep in backlog if ep.get('status') == 'ready']
        print(f"📚 Ready episodes: {len(ready_episodes)}")
        print(f"📚 Total episodes in backlog: {len(backlog)}")
    except Exception as e:
        print(f"❌ Could not read backlog: {e}")

    print("\n🎯 Next Steps:")
    print("1. Fix any issues listed above")
    print("2. Run: python -m autoblogger.run_daily --site dream --auto-publish")
    print("3. Check digitaldreamscape.site for new episodes!")

if __name__ == "__main__":
    setup_dreamscape_autoblogger()