# Chart Data Loading - Debugging Guide

**Issue:** "FAILED TO LOAD CHART DATA" error on tradingrobotplug.com  
**Date:** 2025-12-22

## Quick Diagnostic Steps

### 1. Check Browser Console
Open browser Developer Tools (F12) → Console tab and look for:
- `[DEBUG] Chart loading started`
- `[DEBUG] REST URL: ...`
- `[DEBUG] Chart data URL: ...`
- `[DEBUG] Fetch response: ...`
- Any error messages

### 2. Test REST API Endpoint Directly
Open in browser or use curl:
```
https://tradingrobotplug.com/wp-json/tradingrobotplug/v1/chart-data
```

**Expected Response:**
```json
{
  "labels": ["Dec 1", "Dec 2", ...],
  "datasets": [{
    "label": "Cumulative P&L",
    "data": [100, 150, ...],
    ...
  }]
}
```

**If you get 404:**
- Endpoint not registered → Files not deployed or rewrite rules not flushed

**If you get 500:**
- PHP error in endpoint → Check WordPress error logs

**If you get 403:**
- Permission issue → Check `permission_callback` in PHP

### 3. Verify Files Are Deployed
Check that these files exist on the live server:
- `wp/plugins/tradingrobotplug-wordpress-plugin/includes/class-trading-robot-plug.php`
- `wp/plugins/tradingrobotplug-wordpress-plugin/public/class-public.php`
- `wp/plugins/tradingrobotplug-wordpress-plugin/public/js/public.js`

### 4. Flush WordPress Rewrite Rules
1. Go to WordPress Admin → Settings → Permalinks
2. Click "Save Changes" (no need to change anything)
3. This registers the REST API endpoint

### 5. Clear Browser Cache
- Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
- Or clear browser cache completely

## Common Issues & Solutions

### Issue: 404 on REST API endpoint
**Cause:** Endpoint not registered  
**Solution:**
1. Verify files are deployed
2. Flush rewrite rules (Settings → Permalinks → Save)
3. Check if plugin is active

### Issue: 500 on REST API endpoint
**Cause:** PHP error in endpoint callback  
**Solution:**
1. Check WordPress error logs
2. Verify `Performance_Tracker` class exists
3. Check PHP version compatibility

### Issue: CORS error in console
**Cause:** Cross-origin request blocked  
**Solution:**
- Shouldn't happen for same-origin requests
- Check if site URL is correct in WordPress settings

### Issue: Chart.js not loading
**Cause:** CDN blocked or network issue  
**Solution:**
1. Check Network tab in DevTools
2. Verify Chart.js CDN is accessible
3. Check if ad-blocker is blocking CDN

### Issue: "Chart library not available"
**Cause:** Chart.js not loaded before script execution  
**Solution:**
1. Verify Chart.js is enqueued before `public.js`
2. Check script dependencies in `class-public.php`
3. Verify Chart.js CDN URL is correct

## Files Modified

1. **class-trading-robot-plug.php**
   - Added `register_rest_routes()` method
   - Added `get_chart_data()` method with error handling

2. **class-public.php**
   - Added Chart.js library enqueuing
   - Added script localization for REST API URL

3. **public.js**
   - Added chart loading functionality
   - Added comprehensive debug logging
   - Added error handling with detailed messages

## Verification Checklist

- [ ] Files deployed to live server
- [ ] WordPress rewrite rules flushed
- [ ] Browser cache cleared
- [ ] REST API endpoint accessible (test URL above)
- [ ] Chart.js library loading (check Network tab)
- [ ] No console errors
- [ ] Chart renders successfully

## Next Steps if Issue Persists

1. **Check browser console** for `[DEBUG]` messages
2. **Test REST API endpoint** directly in browser
3. **Check WordPress error logs** for PHP errors
4. **Verify plugin is active** in WordPress admin
5. **Check Network tab** to see if requests are being made

## Contact Information

If issue persists after following all steps, provide:
- Browser console output (all `[DEBUG]` messages)
- REST API endpoint test result (status code and response)
- WordPress error log entries (if any)
- Network tab screenshot showing chart data request

