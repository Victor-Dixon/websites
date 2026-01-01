# Path Migration Notes

## Hardcoded Windows Paths

Several scripts contain hardcoded Windows paths (`D:/websites/...`) that need to be updated to use relative paths.

### Files with Hardcoded Paths

**Deployment Scripts:**
- `ops/deployment/simple_wordpress_deployer.py` - `D:/websites/config/site_configs.json`
- `ops/deployment/deploy_themes_with_config.py` - `D:/websites/config/site_configs.json`
- `ops/deployment/deploy_and_activate_themes.py` - `D:/websites/config/site_configs.json`
- `ops/deployment/publish_blog_post.py` - `D:/websites/config/site_configs.json`
- `ops/deployment/deploy_themes_rest_api.py` - `D:/websites/config/site_configs.json`
- `ops/deployment/activate_theme_rest_api.py` - `D:/websites/config/site_configs.json`
- `ops/deployment/deploy_themes_direct.py` - `D:/websites/.deploy_credentials/sites.json`
- `ops/deployment/test_all_deployers.py` - Multiple `D:/websites/config/` paths
- `ops/deployment/deploy_prismblossom.py` - `D:/websites/websites/` paths

**Tools:**
- `tools/check_deployment_credentials.py` - `D:/websites/config/site_configs.json`
- `tools/deploy_freerideinvestor_index.py` - `D:/websites/FreeRideInvestor/index.php` (FIXED)
- `tools/deploy_all_websites.py` - `D:/websites` base path
- `tools/fix_freerideinvestor_500.py` - `D:/websites/config/site_configs.json`
- `tools/unified_wordpress_manager.py` - `D:/websites/config/site_configs.json`
- `tools/verify_freerideinvestor_content.py` - `D:/websites/docs/diagnostic_reports`
- `tools/verify_themes_migration.py` - `D:/websites` base path

### Recommended Fix Pattern

Replace hardcoded paths with relative paths:

```python
# OLD (hardcoded Windows path):
config_path = Path("D:/websites/config/site_configs.json")

# NEW (relative to repo root):
repo_root = Path(__file__).parent.parent.parent  # Adjust based on file location
config_path = repo_root / "config" / "site_configs.json"

# Or use a helper function:
def get_repo_root() -> Path:
    """Get repository root directory."""
    current = Path(__file__).resolve()
    # Traverse up until we find .git or specific marker
    while current.parent != current:
        if (current / ".git").exists() or (current / "config" / "site_configs.json").exists():
            return current
        current = current.parent
    return Path.cwd()  # Fallback
```

### Priority

**High Priority** (affects deployment):
- `ops/deployment/simple_wordpress_deployer.py` - Core deployer
- `ops/deployment/deploy_themes_with_config.py` - Theme deployment
- `ops/deployment/publish_blog_post.py` - Blog publishing

**Medium Priority** (tools):
- `tools/deploy_freerideinvestor_index.py` - ✅ FIXED
- `tools/check_deployment_credentials.py` - Credential checking

**Low Priority** (legacy/analysis tools):
- Analysis and verification scripts can be updated as needed

## Auto-Deploy Hook Updates

**File**: `ops/deployment/auto_deploy_hook.py`

**Changes Made**:
- Removed mappings for archived/moved directories:
  - `FreeRideInvestor` → Archived to `archive/FreeRideInvestor/`
  - `Swarm_website` → Moved to `websites/weareswarm.site/`
  - `southwestsecret.com` → Moved to `websites/southwestsecret.com/`

**Canonical Layout**: All sites now use `websites/<domain>/` structure, which is automatically detected by the hook.

## Status

- ✅ `tools/deploy_freerideinvestor_index.py` - Updated to use archive/websites paths
- ✅ `ops/deployment/auto_deploy_hook.py` - Updated site mappings
- ⚠️  Other deployment scripts still have hardcoded paths (non-blocking, but should be fixed)

## Next Steps

1. Update core deployment scripts to use relative paths
2. Test deployment after path updates
3. Update remaining tools as needed
4. Consider creating a shared `get_repo_root()` utility function

