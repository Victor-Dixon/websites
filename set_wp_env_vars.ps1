# Set WordPress Environment Variables for Digital Dreamscape
# Run this script to configure WordPress API access

$env:DREAM_WP_URL = "https://digitaldreamscape.site/wp-json/wp/v2"
$env:DREAM_WP_USER = "DadudeKC@Gmail.com"
$env:DREAM_WP_APP_PASS = "KHtl XOwZ FNgJ WTzF HUqc mUvP"

Write-Host "✅ WordPress environment variables set!"
Write-Host "📍 URL: $env:DREAM_WP_URL"
Write-Host "👤 User: $env:DREAM_WP_USER"
Write-Host "🔑 Password: [HIDDEN]"
Write-Host ""
Write-Host "These variables are set for the current PowerShell session."
Write-Host "To make them permanent, add them to your system environment variables."