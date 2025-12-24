# WordPress Diagnostic Report: weareswarm.site

**Generated:** 2025-12-24T00:02:08.066514
**Severity:** MEDIUM

## Summary

- **Total Issues:** 1
- **Severity:** MEDIUM

## Issues Found

### memory_limit (MEDIUM)

- **Message:** No explicit memory limit set in wp-config.php
- **Recommendation:** Add define('WP_MEMORY_LIMIT', '256M'); to wp-config.php if experiencing memory issues

## Recommendations

ℹ️  MEDIUM: 1 medium-priority issue(s) can be addressed when convenient

## Detailed Checks

### Connectivity

```json
{
  "accessible": true,
  "http_status": 200,
  "response_time": 0.84,
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
  "conflicts": []
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

