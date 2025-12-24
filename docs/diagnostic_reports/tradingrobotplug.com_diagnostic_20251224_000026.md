# WordPress Diagnostic Report: tradingrobotplug.com

**Generated:** 2025-12-23T23:58:57.492613
**Severity:** MEDIUM

## Summary

- **Total Issues:** 4
- **Severity:** MEDIUM

## Issues Found

### plugin_structure (MEDIUM)

- **Message:** Plugin tradingrobotplug-wordpress-plugin missing main file
- **Recommendation:** Check plugin structure for tradingrobotplug-wordpress-plugin

### plugin_structure (MEDIUM)

- **Message:** Plugin trp-tools missing main file
- **Recommendation:** Check plugin structure for trp-tools

### memory_limit (MEDIUM)

- **Message:** No explicit memory limit set in wp-config.php
- **Recommendation:** Add define('WP_MEMORY_LIMIT', '256M'); to wp-config.php if experiencing memory issues

### error_log (MEDIUM)

- **Message:** Recent errors found in domains/tradingrobotplug.com/public_html/wp-content/debug.log
- **Recommendation:** Review domains/tradingrobotplug.com/public_html/wp-content/debug.log for detailed error information

## Recommendations

ℹ️  MEDIUM: 4 medium-priority issue(s) can be addressed when convenient

## Detailed Checks

### Connectivity

```json
{
  "accessible": true,
  "http_status": 200,
  "response_time": 1.11,
  "error": null
}
```

### Syntax Errors

```json
{
  "errors": [],
  "files_checked": 21
}
```

### Plugin Conflicts

```json
{
  "conflicts": [
    {
      "type": "plugin_structure",
      "severity": "MEDIUM",
      "plugin": "tradingrobotplug-wordpress-plugin",
      "message": "Plugin tradingrobotplug-wordpress-plugin missing main file",
      "recommendation": "Check plugin structure for tradingrobotplug-wordpress-plugin"
    },
    {
      "type": "plugin_structure",
      "severity": "MEDIUM",
      "plugin": "trp-tools",
      "message": "Plugin trp-tools missing main file",
      "recommendation": "Check plugin structure for trp-tools"
    }
  ]
}
```

### Database

```json
{
  "issues": []
}
```

### Memory

```json
{
  "issues": [
    {
      "type": "memory_limit",
      "severity": "MEDIUM",
      "message": "No explicit memory limit set in wp-config.php",
      "recommendation": "Add define('WP_MEMORY_LIMIT', '256M'); to wp-config.php if experiencing memory issues"
    }
  ]
}
```

### Wordpress Core

```json
{
  "issues": []
}
```

### Error Logs

```json
{
  "errors": [
    {
      "type": "error_log",
      "severity": "MEDIUM",
      "log_file": "domains/tradingrobotplug.com/public_html/wp-content/debug.log",
      "message": "Recent errors found in domains/tradingrobotplug.com/public_html/wp-content/debug.log",
      "recent_errors": [
        "[04-Dec-2025 23:13:03 UTC] PHP Warning:  unlink(/tmp/hostinger.zip): No such file or directory in /home/u996867598/domains/tradingrobotplug.com/public_html/wp-admin/includes/class-wp-upgrader.php on line 388"
      ],
      "recommendation": "Review domains/tradingrobotplug.com/public_html/wp-content/debug.log for detailed error information"
    }
  ]
}
```

