# WordPress Diagnostic Report: freerideinvestor.com

**Generated:** 2025-12-22T11:29:53.478960
**Severity:** HIGH

## Summary

- **Total Issues:** 2
- **Severity:** HIGH

## Issues Found

### syntax_error (HIGH)

- **Message:** Unknown error
- **File:** find: ‘domains/freerideinvestor.com/public_html/wp-content/plugins’: No such file or directory
- **Recommendation:** Fix PHP syntax error in find: ‘domains/freerideinvestor.com/public_html/wp-content/plugins’: No such file or directory. Check line unknown

### memory_limit (MEDIUM)

- **Message:** No explicit memory limit set in wp-config.php
- **Recommendation:** Add define('WP_MEMORY_LIMIT', '256M'); to wp-config.php if experiencing memory issues

## Recommendations

⚠️  HIGH: 1 high-priority issue(s) should be addressed soon
   - Unknown error: Fix PHP syntax error in find: ‘domains/freerideinvestor.com/public_html/wp-content/plugins’: No such file or directory. Check line unknown
ℹ️  MEDIUM: 1 medium-priority issue(s) can be addressed when convenient

## Detailed Checks

### Connectivity

```json
{
  "accessible": true,
  "http_status": 200,
  "response_time": 12.64,
  "error": null
}
```

### Syntax Errors

```json
{
  "errors": [
    {
      "type": "syntax_error",
      "severity": "HIGH",
      "file": "find: \u2018domains/freerideinvestor.com/public_html/wp-content/plugins\u2019: No such file or directory",
      "message": "Unknown error",
      "line": null,
      "recommendation": "Fix PHP syntax error in find: \u2018domains/freerideinvestor.com/public_html/wp-content/plugins\u2019: No such file or directory. Check line unknown"
    }
  ],
  "files_checked": 12
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

