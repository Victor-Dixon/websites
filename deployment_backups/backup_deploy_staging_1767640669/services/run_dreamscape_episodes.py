#!/usr/bin/env python3
"""
Easy script to run the dreamscape autoblogger and generate new episodes
"""

import sys
import os
from pathlib import Path

def run_dreamscape_episodes(auto_publish: bool = False, dry_run: bool = False):
    """Run the dreamscape autoblogger to generate new episodes"""

    websites_dir = Path(__file__).parent
    os.chdir(websites_dir)

    # Add src to path
    sys.path.insert(0, str(websites_dir / "src"))

    print("🎭 Digital Dreamscape Episode Generator")
    print("=" * 50)

    if dry_run:
        print("🔍 DRY RUN MODE - No actual content will be generated or published")
    elif auto_publish:
        print("🚀 AUTO-PUBLISH MODE - Episodes will be published live!")
    else:
        print("📝 DRAFT MODE - Episodes will be generated but not published")

    print()

    # Import and run
    try:
        from autoblogger.run_daily import run_daily_for_site

        result = run_daily_for_site(
            site='dream',
            date_override=None,
            timezone='America/Chicago',
            auto_publish=auto_publish,
            wp_status='publish' if auto_publish else 'draft',
            dry_run=dry_run
        )

        if result == 0:
            if dry_run:
                print("✅ Dry run completed successfully!")
                print("💡 Remove --dry-run to actually generate episodes")
            elif auto_publish:
                print("✅ Episodes generated and published successfully!")
                print("🌐 Check https://digitaldreamscape.site/blog/ for new episodes")
            else:
                print("✅ Episodes generated successfully!")
                print("📝 Episodes saved as drafts - review and publish manually")
                print("🌐 Check WordPress admin at https://digitaldreamscape.site/wp-admin/")
        else:
            print(f"⚠️  Completed with warnings (exit code: {result})")

    except Exception as e:
        print(f"❌ Error running dreamscape autoblogger: {e}")
        import traceback
        traceback.print_exc()
        return 1

    return 0

def main():
    """Main entry point"""
    import argparse

    parser = argparse.ArgumentParser(description="Run Digital Dreamscape episode generator")
    parser.add_argument("--publish", action="store_true", help="Auto-publish episodes live")
    parser.add_argument("--dry-run", action="store_true", help="Test run without generating content")

    args = parser.parse_args()

    if args.dry_run and args.publish:
        print("❌ Cannot use both --dry-run and --publish")
        return 1

    return run_dreamscape_episodes(auto_publish=args.publish, dry_run=args.dry_run)

if __name__ == "__main__":
    sys.exit(main())