#!/bin/bash
# Set WordPress Environment Variables for Digital Dreamscape
# Run this script to configure WordPress API access

export DREAM_WP_URL="https://digitaldreamscape.site/wp-json/wp/v2"
export DREAM_WP_USER="DadudeKC@Gmail.com"
export DREAM_WP_APP_PASS="KHtl XOwZ FNgJ WTzF HUqc mUvP"

echo "✅ WordPress environment variables set!"
echo "📍 URL: $DREAM_WP_URL"
echo "👤 User: $DREAM_WP_USER"
echo "🔑 Password: [HIDDEN]"
echo ""
echo "These variables are set for the current shell session."
echo "To make them permanent, add them to your shell profile (~/.bashrc, ~/.zshrc, etc.)"