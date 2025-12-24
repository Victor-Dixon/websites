# Theme Activation Guide

**Date:** 2025-12-20  
**Purpose:** Activate custom themes on houstonsipqueen.com and digitaldreamscape.site

---

## 🎯 Quick Start

Both sites need their custom themes activated. The theme files are ready in the repository, but need to be:
1. **Uploaded to the server** (if not already)
2. **Activated in WordPress admin**

---

## 📋 Sites Requiring Activation

### 1. houstonsipqueen.com
- **Theme Name:** Houston Sip Queen
- **Theme Directory:** `houstonsipqueen`
- **Location:** `websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen/`

### 2. digitaldreamscape.site
- **Theme Name:** Digital Dreamscape
- **Theme Directory:** `digitaldreamscape`
- **Location:** `websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/`

---

## ✅ Step-by-Step Activation

### Step 1: Verify Theme Files Are on Server

**Option A: Check via FTP/SFTP**
- Connect to your hosting server
- Navigate to: `/wp-content/themes/`
- Verify these directories exist:
  - `houstonsipqueen/` (for houstonsipqueen.com)
  - `digitaldreamscape/` (for digitaldreamscape.site)

**Option B: Check via WordPress Admin**
- Log into WordPress admin
- Go to **Appearance > Themes**
- Look for the theme in the list
- If not visible, files need to be uploaded

### Step 2: Upload Theme Files (If Needed)

If theme files are not on the server:

1. **Via FTP/SFTP:**
   ```bash
   # Upload entire theme directory
   # Source: D:\websites\websites\houstonsipqueen.com\wp\wp-content\themes\houstonsipqueen
   # Destination: /wp-content/themes/houstonsipqueen/
   ```

2. **Via WordPress Admin:**
   - Go to **Appearance > Themes > Add New > Upload Theme**
   - Upload theme as ZIP file (create zip of theme directory)
   - Install and activate

3. **Via Git/Deployment:**
   - If using git deployment, ensure theme files are committed and deployed
   - Run deployment script if available

### Step 3: Activate Theme

**For each site:**

1. **Log into WordPress Admin:**
   - houstonsipqueen.com: https://houstonsipqueen.com/wp-admin
   - digitaldreamscape.site: https://digitaldreamscape.site/wp-admin

2. **Navigate to Themes:**
   - Go to **Appearance > Themes**

3. **Find Your Theme:**
   - Look for "Houston Sip Queen" or "Digital Dreamscape"
   - Theme should show a preview

4. **Activate:**
   - Hover over the theme
   - Click **"Activate"** button
   - Wait for confirmation

5. **Verify:**
   - Visit the homepage
   - Check that custom styling is applied
   - Verify navigation works
   - Test forms (if applicable)

---

## 🔍 Verification Checklist

After activation, verify:

### houstonsipqueen.com:
- [ ] Custom header with logo/site name visible
- [ ] "Request a Quote" button in header works
- [ ] Navigation menu functional
- [ ] Footer links work
- [ ] Quote form page loads at `/quote`
- [ ] Mobile menu toggles correctly

### digitaldreamscape.site:
- [ ] Custom header with logo/site name visible
- [ ] Navigation menu functional
- [ ] Footer links work
- [ ] Mobile menu toggles correctly
- [ ] Blog content displays properly

---

## 🛠️ Alternative Methods

### Method 1: WP-CLI (If Available)

```bash
# For houstonsipqueen.com
cd /path/to/houstonsipqueen.com/wp
wp theme activate houstonsipqueen

# For digitaldreamscape.site
cd /path/to/digitaldreamscape.site/wp
wp theme activate digitaldreamscape
```

### Method 2: Database (Advanced)

**⚠️ Warning:** Only use if you have database access and know what you're doing.

```sql
-- For houstonsipqueen.com
UPDATE wp_options 
SET option_value = 'houstonsipqueen' 
WHERE option_name = 'template';

UPDATE wp_options 
SET option_value = 'houstonsipqueen' 
WHERE option_name = 'stylesheet';

-- For digitaldreamscape.site
UPDATE wp_options 
SET option_value = 'digitaldreamscape' 
WHERE option_name = 'template';

UPDATE wp_options 
SET option_value = 'digitaldreamscape' 
WHERE option_name = 'stylesheet';
```

---

## 📁 Theme File Structure

### houstonsipqueen Theme Files:
```
houstonsipqueen/
├── style.css          ✅ (Theme header)
├── functions.php       ✅ (Theme setup, form handling)
├── header.php         ✅ (Navigation, logo)
├── footer.php         ✅ (Footer links)
├── index.php          ✅ (Main template)
├── page-quote.php     ✅ (Quote form page)
└── js/
    └── main.js        ✅ (Mobile menu, interactions)
```

### digitaldreamscape Theme Files:
```
digitaldreamscape/
├── style.css          ✅ (Theme header)
├── functions.php       ✅ (Theme setup)
├── header.php         ✅ (Navigation, logo)
├── footer.php         ✅ (Footer links)
├── index.php          ✅ (Main template)
└── js/
    └── main.js        ✅ (Mobile menu, interactions)
```

---

## 🚨 Troubleshooting

### Theme Not Appearing in List

**Possible Causes:**
1. Theme files not uploaded to server
2. Missing `style.css` with proper theme header
3. File permissions incorrect
4. Theme directory name mismatch

**Solutions:**
- Verify files are in `/wp-content/themes/[theme-name]/`
- Check `style.css` has correct Theme Name header
- Verify file permissions (755 for directories, 644 for files)
- Check theme directory name matches exactly

### Activation Fails

**Possible Causes:**
1. PHP errors in `functions.php`
2. Missing required files
3. Plugin conflicts
4. Memory limit issues

**Solutions:**
- Check WordPress debug log for errors
- Verify all required files exist
- Deactivate plugins temporarily
- Increase PHP memory limit if needed

### Theme Active But Not Styling

**Possible Causes:**
1. Cache issues
2. CSS not loading
3. Child theme conflicts
4. CDN cache

**Solutions:**
- Clear WordPress cache
- Clear browser cache
- Check browser console for CSS errors
- Verify CSS file is enqueued in `functions.php`
- Clear CDN cache if applicable

---

## 📞 Support

If you encounter issues:

1. Check WordPress debug log: `wp-content/debug.log`
2. Check browser console for JavaScript errors
3. Verify theme files are complete
4. Test with default theme to isolate issues
5. Check server error logs

---

## ✅ Post-Activation Tasks

After themes are activated:

1. **Configure Menus:**
   - Go to **Appearance > Menus**
   - Assign menu to "Primary Menu" location
   - Add menu items (Home, About, Blog, etc.)

2. **Set Logo (Optional):**
   - Go to **Appearance > Customize > Site Identity**
   - Upload logo image
   - Set site title and tagline

3. **Configure Widgets (If Needed):**
   - Go to **Appearance > Widgets**
   - Add widgets to sidebar/footer areas

4. **Test Everything:**
   - Navigate all pages
   - Test all forms
   - Check mobile responsiveness
   - Verify all links work

---

## 📝 Notes

- Theme files are ready in the repository
- Both themes are complete and functional
- Activation is a simple one-click process in WordPress admin
- After activation, all our fixes will be visible

---

**Status:** Ready for activation  
**Priority:** High  
**Estimated Time:** 5-10 minutes per site

