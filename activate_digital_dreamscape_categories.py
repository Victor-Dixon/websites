#!/usr/bin/env python3
"""
Activate Digital Dreamscape Categories
====================================

Complete activation script for Digital Dreamscape episode categories.
Configures WordPress API and creates all required categories.
"""

import os
import sys
from pathlib import Path

def check_wordpress_credentials():
    """Check if WordPress credentials are configured"""

    print("🔐 Checking WordPress API Configuration...")

    required_vars = {
        'DREAM_WP_URL': 'WordPress site URL (e.g., https://digitaldreamscape.site/wp-json/wp/v2)',
        'DREAM_WP_USER': 'WordPress username',
        'DREAM_WP_APP_PASS': 'WordPress application password'
    }

    missing = []
    for var, desc in required_vars.items():
        value = os.environ.get(var, '')
        if value:
            # Mask sensitive info
            if 'PASS' in var:
                masked = value[:4] + '****' + value[-4:] if len(value) > 8 else '****'
            else:
                masked = value
            print(f"✅ {var}: {masked}")
        else:
            print(f"❌ {var}: NOT SET - {desc}")
            missing.append(var)

    return len(missing) == 0, missing

def create_credentials_file():
    """Help create the credentials file"""

    print("\n📝 WordPress Credentials Setup:")
    print("=" * 40)

    print("You need to create a .env file in the config/ directory with:")
    print()
    print("DREAM_WP_URL=https://digitaldreamscape.site/wp-json/wp/v2")
    print("DREAM_WP_USER=your_wordpress_username")
    print("DREAM_WP_APP_PASS=your_application_password")
    print()

    print("To get the application password:")
    print("1. Go to WordPress Admin → Users → Your Profile")
    print("2. Scroll to 'Application Passwords' section")
    print("3. Create new application password")
    print("4. Use the generated password above")
    print()

    # Check if .env file exists
    env_file = Path(__file__).parent / "config" / ".env"
    if env_file.exists():
        print(f"⚠️  .env file already exists at: {env_file}")
        print("   Edit it to add the WordPress credentials above.")
    else:
        print(f"💡 Create .env file at: {env_file}")

def activate_categories():
    """Activate the category system"""

    print("🚀 Activating Digital Dreamscape Categories...")
    print("=" * 50)

    # Check credentials
    creds_ok, missing = check_wordpress_credentials()
    if not creds_ok:
        print(f"\n❌ Missing credentials: {', '.join(missing)}")
        create_credentials_file()
        return False

    print("\n✅ Credentials configured!")

    # Import and run category manager
    try:
        scripts_path = Path(__file__).parent / "scripts" / "services"
        sys.path.insert(0, str(scripts_path))
        from episode_category_manager import EpisodeCategoryManager

        print("\n🏗️ Creating Digital Dreamscape categories...")

        manager = EpisodeCategoryManager()
        manager.ensure_digital_dreamscape_categories()

        print("\n🔧 Fixing existing episode categories...")

        from fix_episode_categories import EpisodeCategoryFixer
        fixer = EpisodeCategoryFixer()
        fixer.fix_all_episodes()

        print("\n📊 Final status report:")
        fixer.generate_category_report()

        print("\n✅ Digital Dreamscape categories activated!")
        print("🌐 Check https://digitaldreamscape.site/blog/ to see proper categories!")

        return True

    except Exception as e:
        print(f"❌ Activation failed: {e}")
        return False

def test_category_system():
    """Test the category system without making changes"""

    print("🧪 Testing Category System...")

    try:
        scripts_path = Path(__file__).parent / "scripts" / "services"
        sys.path.insert(0, str(scripts_path))
        from episode_category_manager import EpisodeCategoryManager
        manager = EpisodeCategoryManager()

        print("✅ Category manager loaded")

        # Test questline mapping
        test_questlines = [
            'infrastructure-architecture',
            'agent-coordination',
            'digitaldreamscape-chronicles'
        ]

        for questline in test_questlines:
            category = manager.questline_categories.get(questline, 'Unknown')
            print(f"🎯 {questline} → '{category}'")

        print("✅ Category mapping working")

        return True

    except Exception as e:
        print(f"❌ Test failed: {e}")
        return False

def main():
    """Main activation function"""

    if len(sys.argv) < 2:
        print("Usage: python activate_digital_dreamscape_categories.py <command>")
        print("Commands:")
        print("  activate    - Full activation (requires WP credentials)")
        print("  test        - Test system without making changes")
        print("  setup       - Show setup instructions")
        return

    command = sys.argv[1]

    if command == 'activate':
        success = activate_categories()
        if not success:
            print("\n💡 Run 'setup' command for configuration help")
    elif command == 'test':
        test_category_system()
    elif command == 'setup':
        create_credentials_file()
    else:
        print(f"❌ Unknown command: {command}")

if __name__ == "__main__":
    main()