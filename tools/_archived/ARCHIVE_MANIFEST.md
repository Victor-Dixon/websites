# Archived Website Tools - 2025-12-28

These tools have been archived as they are one-time fix scripts or replaced by MCP servers.

## Archive Structure

- `freerideinvestor-fixes/` - One-time fixes for FreeRideInvestor site
- `southwestsecret-fixes/` - One-time fixes for SouthwestSecret site
- `diagnose-scripts/` - Diagnostic scripts (no longer needed)
- `debug-scripts/` - Debug scripts (no longer needed)
- `one-time-deploys/` - One-time deployment scripts

## Why Archived?

1. **One-time fixes** - These scripts fixed specific issues and are no longer needed
2. **Replaced by MCP** - Functionality now available via centralized MCP servers:
   - `mcp_deployment-manager_*` - Deployment operations
   - `mcp_website-manager_*` - WordPress management
   - `mcp_validation-audit_*` - Site validation

## How to Use MCP Servers Instead

| Old Tool | New MCP Function |
|----------|------------------|
| `deploy_*.py` | `mcp_deployment-manager_deploy_wordpress_theme` |
| `verify_*.py` | `mcp_deployment-manager_verify_deployment` |
| `check_*.py` | `mcp_validation-audit_*` |
| `activate_*.py` | `mcp_website-manager_*` |

## Archived Files Count

| Category | Count |
|----------|-------|
| freerideinvestor-fixes | 15+ |
| southwestsecret-fixes | 21 |
| diagnose-scripts | 5+ |
| debug-scripts | TBD |
| one-time-deploys | TBD |

## If You Need a Tool Back

These tools are preserved in case they're needed for reference:
1. Navigate to the appropriate archive folder
2. Copy the tool back to the active tools directory
3. Or, reference the code when building MCP server enhancements

