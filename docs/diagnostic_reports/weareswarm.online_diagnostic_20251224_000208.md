# WordPress Diagnostic Report: weareswarm.online

**Generated:** 2025-12-24T00:00:26.606889
**Severity:** MEDIUM

## Summary

- **Total Issues:** 2
- **Severity:** MEDIUM

## Issues Found

### plugin_structure (MEDIUM)

- **Message:** Plugin wpforms-lite missing main file
- **Recommendation:** Check plugin structure for wpforms-lite

### memory_limit (MEDIUM)

- **Message:** No explicit memory limit set in wp-config.php
- **Recommendation:** Add define('WP_MEMORY_LIMIT', '256M'); to wp-config.php if experiencing memory issues

## Recommendations

ℹ️  MEDIUM: 2 medium-priority issue(s) can be addressed when convenient

## Detailed Checks

### Connectivity

```json
{
  "accessible": true,
  "http_status": 200,
  "response_time": 1.65,
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
      "plugin": "wpforms-lite",
      "message": "Plugin wpforms-lite missing main file",
      "recommendation": "Check plugin structure for wpforms-lite"
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
  "errors": []
}
```

