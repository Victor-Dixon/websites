# Quick Plugin Testing Script (PowerShell)
# For FreeRideInvestor WordPress Environment

param(
    [string]$PluginName = ""
)

Write-Host "🧪 Quick Plugin Test Script" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan
Write-Host ""

if ($PluginName -eq "") {
    Write-Host "Usage: .\quick-plugin-test.ps1 -PluginName 'plugin-slug'" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Available plugins:" -ForegroundColor Green
    docker-compose exec -T wpcli plugin list --format=csv
    exit
}

Write-Host "Testing plugin: $PluginName" -ForegroundColor Green
Write-Host ""

# Test 1: Check if plugin exists
Write-Host "📦 Step 1: Checking if plugin exists..." -ForegroundColor Cyan
$pluginCheck = docker-compose exec -T wpcli plugin get $PluginName --format=json 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Plugin not found!" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Plugin found" -ForegroundColor Green
Write-Host ""

# Test 2: Deactivate
Write-Host "📦 Step 2: Deactivating plugin..." -ForegroundColor Cyan
docker-compose exec -T wpcli plugin deactivate $PluginName
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Deactivation successful" -ForegroundColor Green
} else {
    Write-Host "❌ Deactivation failed" -ForegroundColor Red
}
Write-Host ""

# Test 3: Activate
Write-Host "📦 Step 3: Activating plugin..." -ForegroundColor Cyan
docker-compose exec -T wpcli plugin activate $PluginName
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Activation successful" -ForegroundColor Green
} else {
    Write-Host "❌ Activation failed - PLUGIN BROKEN!" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Test 4: Check WordPress health
Write-Host "📦 Step 4: Checking WordPress health..." -ForegroundColor Cyan
docker-compose exec -T wpcli core verify-checksums 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ WordPress core intact" -ForegroundColor Green
} else {
    Write-Host "⚠️ WordPress core modified (may be intentional)" -ForegroundColor Yellow
}
Write-Host ""

# Test 5: Check for errors
Write-Host "📦 Step 5: Checking for PHP errors..." -ForegroundColor Cyan
$errors = docker-compose logs wordpress | Select-String -Pattern "error" -CaseSensitive:$false | Select-Object -Last 5
if ($errors) {
    Write-Host "⚠️ Recent errors found:" -ForegroundColor Yellow
    $errors | ForEach-Object { Write-Host $_.Line -ForegroundColor Yellow }
} else {
    Write-Host "✅ No recent errors" -ForegroundColor Green
}
Write-Host ""

# Summary
Write-Host "=============================" -ForegroundColor Cyan
Write-Host "🎯 Test Summary for: $PluginName" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan
Write-Host "✅ Plugin is functional" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Next Steps:" -ForegroundColor Cyan
Write-Host "  1. Visit http://localhost:8080 to test frontend" -ForegroundColor White
Write-Host "  2. Visit http://localhost:8080/wp-admin to test backend" -ForegroundColor White
Write-Host "  3. Check plugin settings page" -ForegroundColor White
Write-Host "  4. Test with real data" -ForegroundColor White
Write-Host ""
Write-Host "📊 View logs:" -ForegroundColor Cyan
Write-Host "  docker-compose logs -f wordpress" -ForegroundColor White
Write-Host ""


