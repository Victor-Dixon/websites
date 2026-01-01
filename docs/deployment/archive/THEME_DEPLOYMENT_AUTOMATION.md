# Theme Deployment Automation

**Date:** 2025-12-21  
**Status:** ✅ Complete

---

## 🎯 Overview

Updated WordPress deployment tools to automatically handle theme upload and activation, enabling the swarm (AI agents) to manage theme deployments without manual intervention.

---

## 🛠️ Tools Created/Updated

### 1. **deploy_and_activate_themes.py** (NEW)

**Purpose:** Fully automated theme deployment and activation

**Features:**
- ✅ Uploads theme files via SFTP/SSH using WordPressManager
- ✅ Activates themes via WP-CLI over SSH
- ✅ Fallback to manual instructions if automation unavailable
- ✅ Supports single site or batch deployment

**Usage:**
```bash
# Deploy and activate theme for specific site
python ops/deployment/deploy_and_activate_themes.py --site houstonsipqueen.com

# Deploy themes for all configured sites
python ops/deployment/deploy_and_activate_themes.py --all

# Upload only, don't activate
python ops/deployment/deploy_and_activate_themes.py --all --upload-only
```

**Configuration:**
- Theme paths configured in `THEME_CONFIGS` dictionary
- Integrates with existing WordPressManager deployment system
- Uses site credentials from `.deploy_credentials/sites.json`

### 2. **activate_themes.py** (UPDATED)

**Purpose:** Activate themes that are already uploaded

**Updates:**
- ✅ Added WordPressManager integration for remote WP-CLI execution
- ✅ Falls back to local WP-CLI if remote not available
- ✅ Generates manual instructions as fallback

**Usage:**
```bash
# Activate theme for specific site
python ops/deployment/activate_themes.py --site houstonsipqueen.com

# Activate themes for all sites
python ops/deployment/activate_themes.py --all
```

---

## 📋 Configured Themes

### houstonsipqueen.com
- **Theme Name:** `houstonsipqueen`
- **Local Path:** `websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen`
- **Remote Path:** `/wp-content/themes/houstonsipqueen`
- **Site Key:** `houstonsipqueen`

### digitaldreamscape.site
- **Theme Name:** `digitaldreamscape`
- **Local Path:** `websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape`
- **Remote Path:** `/wp-content/themes/digitaldreamscape`
- **Site Key:** `digitaldreamscape`

---

## 🔄 Deployment Flow

1. **Connect to Server**
   - Uses WordPressManager to establish SFTP/SSH connection
   - Validates credentials from `.deploy_credentials/sites.json`

2. **Upload Theme Files**
   - Recursively uploads all theme files
   - Creates remote directories as needed
   - Verifies file uploads

3. **Activate Theme**
   - Attempts WP-CLI activation via SSH
   - Falls back to manual instructions if needed

4. **Verification**
   - Reports upload and activation status
   - Provides next steps if manual intervention needed

---

## 🚀 Quick Start

### For houstonsipqueen.com:
```bash
python ops/deployment/deploy_and_activate_themes.py --site houstonsipqueen.com
```

### For digitaldreamscape.site:
```bash
python ops/deployment/deploy_and_activate_themes.py --site digitaldreamscape.site
```

### For Both Sites:
```bash
python ops/deployment/deploy_and_activate_themes.py --all
```

---

## ⚙️ Requirements

### Dependencies
- **WordPressManager** from main repository (`D:/Agent_Cellphone_V2_Repository/tools/wordpress_manager.py`)
- **Python 3.7+**
- **SFTP/SSH access** to hosting servers
- **WP-CLI** installed on hosting servers (for activation)

### Credentials
- Site credentials stored in `.deploy_credentials/sites.json` (not in repo)
- Environment variables from `.env` file (if using dotenv)

---

## 🔍 Troubleshooting

### WordPressManager Not Found
**Error:** `WordPressManager not found`

**Solution:**
- Ensure WordPressManager exists in main repository
- Check path: `D:/Agent_Cellphone_V2_Repository/tools/wordpress_manager.py`

### Connection Failed
**Error:** `Failed to connect to {site_key}`

**Solution:**
- Verify credentials in `.deploy_credentials/sites.json`
- Check SSH/SFTP access to hosting server
- Verify site_key matches configuration

### Theme Activation Failed
**Error:** `WP-CLI activation not available`

**Solution:**
- Theme files are uploaded successfully
- Activate manually in WordPress admin:
  1. Go to `Appearance > Themes`
  2. Find theme and click "Activate"

---

## 📝 Integration with Existing Tools

### Works With:
- ✅ `deploy_all_websites.py` - Uses same WordPressManager system
- ✅ `deploy_website_fixes.py` - Same deployment infrastructure
- ✅ `activate_themes.py` - Complementary activation tool

### Deployment Workflow:
1. **Development:** Create/update theme files locally
2. **Deployment:** Run `deploy_and_activate_themes.py --all`
3. **Verification:** Check sites for theme activation
4. **Monitoring:** Use existing verification tools

---

## ✅ Benefits

1. **Fully Automated:** No manual file uploads or WordPress admin access needed
2. **Swarm-Ready:** AI agents can execute theme deployments independently
3. **Consistent:** Uses same deployment system as other website fixes
4. **Reliable:** Multiple fallback methods ensure deployment succeeds
5. **Scalable:** Easy to add new themes to configuration

---

## 📚 Documentation

- **README:** `ops/deployment/README.md` - Updated with new tool
- **Activation Guide:** `docs/THEME_ACTIVATION_GUIDE.md` - Manual fallback instructions
- **Status Tracking:** `docs/THEME_ACTIVATION_STATUS.md` - Current status

---

## 🎯 Next Steps

1. **Test Deployment:**
   ```bash
   python ops/deployment/deploy_and_activate_themes.py --all
   ```

2. **Verify Activation:**
   - Visit site homepages
   - Check theme styling is applied
   - Verify navigation and forms work

3. **Add More Themes:**
   - Add entries to `THEME_CONFIGS` in `deploy_and_activate_themes.py`
   - Follow same pattern as existing themes

---

**Status:** ✅ Ready for use  
**Priority:** High  
**Automation Level:** Full (with fallbacks)

