# Cache Bypass Instructions for TradingRobotPlug.com

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-27  
**Status:** ACTIVE - Cache Bypass Guide  
**Purpose:** Instructions for bypassing cache to verify stock prices fix on live site

<!-- SSOT Domain: documentation -->
<!-- SSOT Domain: trading_robot -->

---

## Issue

Stock prices on TradingRobotPlug.com homepage may be cached, preventing verification of the dynamic JavaScript/AJAX implementation.

---

## Browser Cache Bypass Methods

### Method 1: Hard Refresh (Recommended)

**Chrome/Edge (Windows):**
- `Ctrl + Shift + R` (or `Ctrl + F5`)
- Forces reload of all resources from server

**Chrome/Edge (Mac):**
- `Cmd + Shift + R`

**Firefox (Windows):**
- `Ctrl + Shift + R` (or `Ctrl + F5`)

**Firefox (Mac):**
- `Cmd + Shift + R`

**Safari:**
- `Cmd + Option + R`

### Method 2: Developer Tools Hard Refresh

1. Open Developer Tools (`F12` or `Ctrl+Shift+I` / `Cmd+Option+I`)
2. Right-click on the refresh button
3. Select "Empty Cache and Hard Reload"

### Method 3: Incognito/Private Window

1. Open a new incognito/private window
2. Navigate to `https://tradingrobotplug.com/`
3. This bypasses all cached files

### Method 4: Clear Browser Cache (Complete)

**Chrome:**
1. `Ctrl + Shift + Delete` (Windows) or `Cmd + Shift + Delete` (Mac)
2. Select "Cached images and files"
3. Time range: "All time"
4. Click "Clear data"

**Firefox:**
1. `Ctrl + Shift + Delete` (Windows) or `Cmd + Shift + Delete` (Mac)
2. Select "Cache"
3. Time range: "Everything"
4. Click "Clear Now"

---

## WordPress Cache Clearing

### Check for Caching Plugins

Common WordPress caching plugins:
- WP Super Cache
- W3 Total Cache
- WP Rocket
- LiteSpeed Cache
- Autoptimize
- WP Fastest Cache

### Clear WordPress Cache Methods

#### Method 1: WordPress Admin Dashboard

1. Log in to WordPress admin: `https://tradingrobotplug.com/wp-admin/`
2. Look for caching plugin menu items (usually in sidebar)
3. Click "Clear Cache" or "Purge Cache" button

#### Method 2: WP-CLI (if available)

```bash
# Clear cache (if WP Super Cache)
wp cache flush

# Clear cache (if W3 Total Cache)
wp w3-total-cache flush all

# Clear cache (if WP Rocket)
wp rocket clean --all
```

#### Method 3: Direct Cache Clearing

**For WP Super Cache:**
- Visit: `https://tradingrobotplug.com/wp-admin/admin.php?page=wpsupercache`
- Click "Delete Cache" button

**For W3 Total Cache:**
- Visit: `https://tradingrobotplug.com/wp-admin/admin.php?page=w3tc_dashboard`
- Click "Empty all caches"

**For WP Rocket:**
- Visit: `https://tradingrobotplug.com/wp-admin/admin.php?page=wprocket`
- Click "Clear and Preload Cache"

---

## Server-Side Cache Clearing

### Object Cache (Redis/Memcached)

If using object cache, may need to clear:

```bash
# Redis
redis-cli FLUSHALL

# Memcached
echo "flush_all" | nc localhost 11211
```

### CDN Cache (if applicable)

If using Cloudflare, CloudFront, or other CDN:
1. Log in to CDN dashboard
2. Purge cache for domain
3. Or use API to purge specific URLs

---

## Verify Changes

### What to Look For

After clearing cache, verify:

1. **Stock Prices Section:**
   - Should show "Loading..." initially
   - Then populate with live data from REST API
   - Prices should update every 30 seconds
   - All 4 symbols should display: TSLA, QQQ, SPY, NVDA

2. **REST API Endpoint:**
   - Test directly: `https://tradingrobotplug.com/wp-json/tradingrobotplug/v1/stock-data`
   - Should return JSON with `stock_data` array
   - Each entry should have: `symbol`, `price`, `change_percent`, etc.

3. **JavaScript Console:**
   - Open Developer Tools (`F12`)
   - Go to Console tab
   - Check for JavaScript errors
   - Look for successful API calls to `/wp-json/tradingrobotplug/v1/stock-data`

### Expected Behavior

**Before Fix (Hardcoded):**
- Static prices that never change
- No loading state
- Prices remain same on refresh

**After Fix (Dynamic):**
- "Loading..." state initially
- Prices load from REST API
- Auto-refresh every 30 seconds
- "Last updated: [time]" shows current timestamp

---

## Testing Checklist

- [ ] Hard refresh browser (`Ctrl + Shift + R`)
- [ ] Check in incognito/private window
- [ ] Verify REST API endpoint returns data
- [ ] Check browser console for JavaScript errors
- [ ] Verify stock prices display (not hardcoded)
- [ ] Verify auto-refresh works (wait 30 seconds)
- [ ] Check network tab for API calls
- [ ] Clear WordPress cache if caching plugin active
- [ ] Clear CDN cache if applicable

---

## Direct API Testing

Test the REST API endpoint directly:

```bash
# Test stock data endpoint
curl https://tradingrobotplug.com/wp-json/tradingrobotplug/v1/stock-data

# Should return JSON like:
# {
#   "stock_data": [
#     {
#       "symbol": "TSLA",
#       "price": "248.50",
#       "change_percent": "3.12",
#       ...
#     },
#     ...
#   ],
#   "symbols": ["TSLA", "QQQ", "SPY", "NVDA"],
#   "timestamp": "2025-12-27 03:20:00"
# }
```

---

## Troubleshooting

### Still Seeing Hardcoded Prices

1. **Check Deployment Status:**
   - Verify `front-page.php` was deployed
   - Check file modification date
   - Confirm JavaScript code is present in source

2. **Check File Content:**
   - View page source (`Ctrl + U`)
   - Search for "market-items-container"
   - Should see JavaScript code with `fetch()` call

3. **Check JavaScript:**
   - Open Developer Tools → Console
   - Look for errors
   - Check Network tab for API calls

4. **REST API Issues:**
   - Test endpoint directly
   - Check for PHP errors in WordPress debug log
   - Verify database table `wp_trp_stock_data` exists

### API Returns Empty Data

1. **Check Database:**
   - Verify `wp_trp_stock_data` table exists
   - Check if data exists in table
   - Verify cron job is running

2. **Check Cron Job:**
   - WordPress cron may need triggering
   - Check `wp_next_scheduled('trp_collect_stock_data')`
   - Manually trigger: `wp cron event run trp_collect_stock_data`

---

## Quick Verification Steps

1. **Open Incognito Window** (easiest method)
2. Navigate to `https://tradingrobotplug.com/`
3. Open Developer Tools (`F12`)
4. Go to Network tab
5. Filter by "stock-data"
6. Refresh page
7. Should see API call to `/wp-json/tradingrobotplug/v1/stock-data`
8. Check Response - should contain stock data array
9. Verify prices update on page (not hardcoded)

---

**Last Updated:** 2025-12-27 by Agent-2  
**Status:** ✅ ACTIVE - Cache Bypass Instructions  
**Related:** `TRADINGROBOTPLUG_STOCK_PRICES_IMPLEMENTATION_REVIEW.md`

