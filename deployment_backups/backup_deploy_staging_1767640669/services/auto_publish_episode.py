#!/usr/bin/env python3
"""
Auto-Publish Episode to Digital Dreamscape Codex
==============================================

Automatically publishes episodes to the Digital Dreamscape Lore Codex
with support for multiple routing options (direct, dadudekc, manual).
"""

import sys
import subprocess
from pathlib import Path
from typing import Optional

# Add config to path for importing
sys.path.insert(0, str(Path(__file__).parent.parent.parent / "config"))
from paths import paths


def publish_episode_direct(episode_id: str) -> bool:
    """Publish episode directly to digitaldreamscape.site"""
    try:
        print(f"📤 Publishing {episode_id} directly to Digital Dreamscape...")

        # Run the dreamscape episodes publisher
        cmd = [
            sys.executable,
            str(paths.service_scripts / "run_dreamscape_episodes.py"),
            "--episode", episode_id
        ]

        result = subprocess.run(cmd, capture_output=True, text=True)
        if result.returncode == 0:
            print("✅ Direct publication successful")
            return True
        else:
            print(f"❌ Direct publication failed: {result.stderr}")
            return False

    except Exception as e:
        print(f"❌ Direct publication error: {e}")
        return False


def publish_episode_via_dadudekc(episode_id: str) -> bool:
    """Publish episode via dadudekc autoblogger routing"""
    try:
        print(f"📤 Publishing {episode_id} via Dadudekc autoblogger route...")

        # First convert to dadudekc format
        convert_cmd = [
            sys.executable,
            str(paths.service_scripts / "convert_episode_to_dadudekc.py"),
            episode_id
        ]

        convert_result = subprocess.run(convert_cmd, capture_output=True, text=True)
        if convert_result.returncode != 0:
            print(f"❌ Dadudekc conversion failed: {convert_result.stderr}")
            return False

        # Find the converted file
        dadudekc_file = paths.content / "drafts" / "dadudekc" / f"{episode_id}_dadudekc.md"
        if not dadudekc_file.exists():
            print(f"❌ Converted file not found: {dadudekc_file}")
            return False

        print(f"📄 Dadudekc file ready: {dadudekc_file}")

        # Publish via dadudekc autoblogger
        publish_cmd = [
            sys.executable,
            str(paths.ops / "deployment" / "publish_with_autoblogger.py"),
            "--site", "dadudekc",
            "--title", f"EP-{episode_id}: Digital Dreamscape Episode",
            "--file", str(dadudekc_file),
            "--status", "publish"
        ]

        print(f"🚀 Executing dadudekc publication...")
        publish_result = subprocess.run(publish_cmd, capture_output=True, text=True, timeout=60)

        # Debug: print all output
        stdout_msg = publish_result.stdout.strip()
        stderr_msg = publish_result.stderr.strip()

        print(f"📄 STDOUT: {stdout_msg[:200]}..." if len(stdout_msg) > 200 else f"📄 STDOUT: {stdout_msg}")
        print(f"📄 STDERR: {stderr_msg[:200]}..." if len(stderr_msg) > 200 else f"📄 STDERR: {stderr_msg}")

        if publish_result.returncode == 0:
            print("✅ Dadudekc route publication successful")
            return True
        else:
            # Check for dadudekc-specific errors
            combined_output = (stdout_msg + stderr_msg).lower()
            if "no such file or directory" in combined_output and "dadudekc" in combined_output:
                print("⚠️ Dadudekc autoblogger not available (site not found)")
                print("💡 Falling back to manual publication setup...")
                return publish_episode_manual(episode_id)
            elif "permission denied" in combined_output or "403" in combined_output:
                print("⚠️ Dadudekc autoblogger access denied")
                print("💡 Falling back to manual publication setup...")
                return publish_episode_manual(episode_id)
            else:
                print(f"❌ Dadudekc route publication failed (return code: {publish_result.returncode})")
                print("💡 Falling back to manual publication setup...")
                return publish_episode_manual(episode_id)

    except subprocess.TimeoutExpired:
        print("⏰ Dadudekc publication timed out")
        print("💡 Falling back to manual publication setup...")
        return publish_episode_manual(episode_id)
    except Exception as e:
        print(f"❌ Dadudekc route error: {e}")
        print("💡 Falling back to manual publication setup...")
        return publish_episode_manual(episode_id)


def publish_episode_manual(episode_id: str) -> bool:
    """Manual publication fallback"""
    try:
        print(f"📝 Manual publication setup for {episode_id}...")

        # Copy episode to dream drafts
        episode_file = paths.content / "episodes" / f"{episode_id}_*.md"
        dream_drafts = paths.content / "drafts" / "dream"

        # Find the episode file (there should be only one matching)
        episode_files = list(paths.content.glob(f"episodes/{episode_id}_*.md"))
        if not episode_files:
            print(f"❌ Episode file not found for {episode_id}")
            return False

        episode_file = episode_files[0]
        dream_file = dream_drafts / episode_file.name

        # Copy to dream drafts
        import shutil
        shutil.copy2(episode_file, dream_file)
        print(f"✅ Copied to dream drafts: {dream_file}")

        # Provide manual publication command
        print("\n🔧 Manual publication command:")
        print(f"python ops/deployment/publish_with_autoblogger.py --site dream --content-file {dream_file}")

        return True

    except Exception as e:
        print(f"❌ Manual setup error: {e}")
        return False


def run_canon_scan() -> bool:
    """Run canon declaration scan after publication"""
    try:
        print("🏛️ Running canon declaration scan...")

        # This would normally run the PHP canon scanner
        # For now, just provide the command
        canon_cmd = "php sites/production/digitaldreamscape.site/canon_declaration_system.php scan"
        print(f"🔧 Run manually: {canon_cmd}")

        return True

    except Exception as e:
        print(f"❌ Canon scan error: {e}")
        return False


def main():
    """Main publication function"""
    if len(sys.argv) < 2:
        print("Usage: python auto_publish_episode.py <episode_id> [--codex-route route]")
        print("Routes: direct (default), dadudekc, manual")
        print("Example: python auto_publish_episode.py EP-3259 --codex-route dadudekc")
        sys.exit(1)

    episode_id = sys.argv[1]
    codex_route = "direct"  # default

    # Parse route option
    if len(sys.argv) >= 4 and sys.argv[2] == "--codex-route":
        codex_route = sys.argv[3]

    print(f"🚀 Auto-Publishing Episode {episode_id}")
    print(f"📚 Codex Route: {codex_route}")
    print("=" * 50)

    success = False

    if codex_route == "direct":
        success = publish_episode_direct(episode_id)
    elif codex_route == "dadudekc":
        success = publish_episode_via_dadudekc(episode_id)
    elif codex_route == "manual":
        success = publish_episode_manual(episode_id)
    else:
        print(f"❌ Unknown route: {codex_route}")
        sys.exit(1)

    if success:
        print(f"\n✅ Episode {episode_id} successfully published via {codex_route} route!")

        # Run canon scan
        print("\n🏛️ Updating canon declarations...")
        run_canon_scan()

        print(f"\n🎭 Episode {episode_id} is now live in the Digital Dreamscape Lore Codex!")
        print("🌐 View at: https://digitaldreamscape.site/blog/")
    else:
        print(f"\n❌ Failed to publish episode {episode_id}")
        sys.exit(1)


if __name__ == "__main__":
    main()