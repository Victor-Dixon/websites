# WordPress Cache Clearing Protocol
===============================

**Purpose:** Standardized protocol for clearing WordPress cache after deployments to ensure changes are immediately visible.

**Tool:** `ops/deployment/clear_wordpress_cache.py`

---

## 📋 **Protocol Steps**

### **1. Deploy Files**
```bash
python ops/deployment/unified_deployer.py <site_name> <file_path>
# OR
python ops/deployment/deploy_digitaldreamscape.py  # Site-specific deployer
```

### **2. Clear WordPress Cache**
```bash
python ops/deployment/clear_wordpress_cache.py <site_name>
```

**Example:**
```bash
python ops/deployment/clear_wordpress_cache.py digitaldreamscape.site
```

### **3. Hard Refresh Browser**
- **Windows/Linux:** `Ctrl + Shift + R`
- **Mac:** `Cmd + Shift + R`
- Or add `?nocache=1` to URL for cache bypass

### **4. Verify Changes**
- Navigate to the site
- Check that changes are visible
- Test affected pages

---

## 🧹 **What Gets Cleared**

The `clear_wordpress_cache.py` tool clears:

1. ✅ **WordPress Object Cache** (`wp cache flush`)
2. ✅ **Object Cache** (alternative method)
3. ✅ **Rewrite Rules** (forces template refresh)
4. ✅ **Transients** (temporary cached data)
5. ✅ **LiteSpeed Cache** (if installed)
6. ⚠️ **WP Super Cache** (if installed, attempts)
7. ⚠️ **W3 Total Cache** (if installed, attempts)

---

## 📝 **Usage Examples**

### **Single Site:**
```bash
# Clear cache for Digital Dreamscape
python ops/deployment/clear_wordpress_cache.py digitaldreamscape.site

# Clear cache for Prismblossom
python ops/deployment/clear_wordpress_cache.py prismblossom.online
```

### **Verbose Output (Default):**
Shows detailed results for each cache clearing operation.

### **Quiet Mode:**
```bash
python ops/deployment/clear_wordpress_cache.py digitaldreamscape.site --quiet
```

---

## 🔧 **Tool Features**

- **Automatic Detection:** Detects which caching plugins are installed
- **Multiple Cache Types:** Clears object cache, transients, rewrite rules
- **Plugin Support:** LiteSpeed Cache, WP Super Cache, W3 Total Cache
- **Error Handling:** Gracefully handles missing plugins
- **Detailed Feedback:** Shows success/failure for each operation

---

## ⚡ **Quick Reference**

| Step | Command | Purpose |
|------|---------|---------|
| 1 | `python deploy_digitaldreamscape.py` | Deploy files |
| 2 | `python clear_wordpress_cache.py digitaldreamscape.site` | Clear cache |
| 3 | `Ctrl+Shift+R` (browser) | Hard refresh |
| 4 | Visit site | Verify changes |

---

## 🚨 **Troubleshooting**

### **Changes Still Not Visible?**

1. **Check Deployment:**
   - Verify file was actually uploaded
   - Check file permissions on server

2. **Clear Browser Cache:**
   - Hard refresh: `Ctrl+Shift+R`
   - Clear browser cache manually
   - Use incognito/private window

3. **CDN Cache:**
   - If using CDN (Cloudflare, etc.), clear CDN cache separately
   - CDN cache clearing is NOT handled by this tool

4. **Plugin-Specific Cache:**
   - Some plugins maintain their own cache
   - Check plugin settings for cache clearing options

5. **Server-Level Cache:**
   - Some hosts use server-level caching (Varnish, etc.)
   - May require host control panel cache clearing

---

## 📚 **Related Tools**

- `unified_deployer.py` - Deploy files to any WordPress site
- `deploy_digitaldreamscape.py` - Site-specific deployer
- `test_all_deployers.py` - Test deployment connectivity

---

**Last Updated:** 2025-12-25  
**Author:** Agent-7 (Web Development Specialist)

