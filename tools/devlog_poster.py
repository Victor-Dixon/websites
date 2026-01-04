#!/usr/bin/env python3
"""
Devlog Poster Tool
Posts devlogs to Discord using webhooks
"""

import os
import sys
import json
import argparse
from pathlib import Path
import requests
from datetime import datetime

class DevlogPoster:
    """Posts devlogs to Discord webhooks"""

    def __init__(self):
        # Load Discord webhook URL from environment or config
        self.webhook_url = os.getenv('DISCORD_DEVLOG_WEBHOOK_URL')
        if not self.webhook_url:
            # Try to load from config file
            config_path = Path(__file__).parent.parent / 'config' / 'discord_webhooks.json'
            if config_path.exists():
                with open(config_path, 'r') as f:
                    config = json.load(f)
                    self.webhook_url = config.get('devlog_webhook_url')

        if not self.webhook_url:
            print("❌ No Discord webhook URL found. Set DISCORD_DEVLOG_WEBHOOK_URL environment variable or add to config/discord_webhooks.json")
            sys.exit(1)

    def post_devlog(self, agent: str, file_path: str):
        """Post a devlog to Discord"""

        devlog_path = Path(file_path)
        if not devlog_path.exists():
            print(f"❌ Devlog file not found: {file_path}")
            return False

        # Read the devlog content
        with open(devlog_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Truncate if too long (Discord limit is 2000 chars)
        if len(content) > 1900:
            content = content[:1900] + "\n\n[...truncated for Discord limit...]"
            print("⚠️ Content truncated to fit Discord's 2000 character limit")

        # Create the embed
        embed = {
            "title": f"🤖 Agent-{agent} Devlog",
            "description": content,
            "color": 0x00ff00,
            "timestamp": datetime.now().isoformat(),
            "footer": {
                "text": f"Agent-{agent} • {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}"
            }
        }

        # Prepare webhook payload
        payload = {
            "username": f"Agent-{agent} Devlog Bot",
            "embeds": [embed]
        }

        # Send to Discord
        try:
            response = requests.post(
                self.webhook_url,
                json=payload,
                headers={'Content-Type': 'application/json'},
                timeout=10
            )

            if response.status_code == 204:
                print(f"✅ Devlog posted successfully for Agent-{agent}")
                return True
            else:
                print(f"❌ Failed to post devlog. Status: {response.status_code}")
                print(f"Response: {response.text}")
                return False

        except Exception as e:
            print(f"❌ Error posting devlog: {e}")
            return False

def main():
    parser = argparse.ArgumentParser(description='Post devlogs to Discord')
    parser.add_argument('--agent', required=True, help='Agent name (e.g., Agent-4)')
    parser.add_argument('--file', required=True, help='Path to devlog file')

    args = parser.parse_args()

    poster = DevlogPoster()
    success = poster.post_devlog(args.agent, args.file)

    if success:
        print(f"🎉 Devlog posted successfully for {args.agent}")
    else:
        print(f"❌ Failed to post devlog for {args.agent}")
        sys.exit(1)

if __name__ == '__main__':
    main()