# TRP Paper Trading Stats Plugin - Deployment Guide

## Quick Start

1. **Upload Plugin to WordPress**
   ```bash
   # Copy plugin directory to WordPress plugins folder
   cp -r trp-paper-trading-stats /var/www/html/wp-content/plugins/
   ```

2. **Activate Plugin**
   - Go to WordPress Admin → Plugins
   - Find "TRP Paper Trading Stats"
   - Click "Activate"

3. **Configure Path (if needed)**
   - Go to WordPress Admin → Settings → TRP Paper Trading Stats
   - Set "Project Root Path" to: `/var/www/html/Agent_Cellphone_V2_Repository`
   - Set "Python Command" if needed (default: `python`)

4. **Add Shortcode to Page**
   - Edit any page/post
   - Add shortcode: `[trp_trading_stats]`
   - Or use with options: `[trp_trading_stats mode="full" refresh="60"]`

## Server Requirements

- WordPress 5.0+
- Python 3.8+ installed and accessible via `python` command
- PHP `shell_exec()` enabled (for executing Python script)
- Read access to `Agent_Cellphone_V2_Repository/trading_logs/` directory

## File Permissions

Ensure proper permissions:
```bash
chmod 755 /var/www/html/wp-content/plugins/trp-paper-trading-stats
chmod 644 /var/www/html/wp-content/plugins/trp-paper-trading-stats/*.php
chmod 755 /var/www/html/Agent_Cellphone_V2_Repository/tools/get_paper_trading_stats.py
```

## Testing

1. **Test Python Script Directly**
   ```bash
   cd /var/www/html/Agent_Cellphone_V2_Repository
   python tools/get_paper_trading_stats.py
   ```
   Should return JSON with paper trading stats.

2. **Test REST API Endpoint**
   ```bash
   curl https://tradingrobotplug.com/wp-json/trp/v1/paper-trading-stats
   ```
   Should return JSON response.

3. **Test Shortcode**
   - Create a test page
   - Add `[trp_trading_stats]` shortcode
   - View page - stats should display

## Troubleshooting

### "Script not found" Error
- Check that `Agent_Cellphone_V2_Repository` is accessible from WordPress
- Configure project root path in plugin settings
- Verify Python script exists at: `{project_root}/tools/get_paper_trading_stats.py`

### "Execution failed" Error
- Check PHP `shell_exec()` is enabled
- Verify Python is accessible: `which python`
- Check file permissions on Python script
- Review WordPress debug log for detailed errors

### "Invalid JSON" Error
- Test Python script directly to see raw output
- Check for Python errors in script execution
- Verify trading logs exist in `trading_logs/` directory

### Stats Not Updating
- Check auto-refresh interval in shortcode
- Verify REST API endpoint is accessible
- Check browser console for JavaScript errors

## Switching to Live Trading

When ready to switch from paper trading to live trading:

1. **Update Python Script**
   - Modify `tools/get_paper_trading_stats.py`
   - Change data source from paper trading logs to live trading API
   - Update `mode` in response from `"paper_trading"` to `"live_trading"`

2. **Plugin Auto-Detects**
   - Plugin will automatically show "Live" badge instead of "Paper Trading"
   - No WordPress plugin changes needed

## Security Notes

- REST API endpoint is public (no authentication required)
- Consider adding rate limiting for production
- Python script should validate inputs and handle errors gracefully
- File paths should be sanitized to prevent directory traversal

## Support

For deployment issues, check:
- WordPress debug log: `wp-content/debug.log`
- PHP error log
- Python script output when run directly
- Browser console for frontend errors

