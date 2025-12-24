# Manual Deployment Guide - Stunning Theme

Since automated deployment requires credentials, here's how to manually deploy the stunning theme:

## Quick Method: Via WordPress Admin

### Step 1: Upload Template File
1. Go to WordPress Admin → Plugins
2. Install "WP File Manager" plugin if not already installed
3. Navigate to: WP File Manager → `wp-content/themes/freerideinvestor-modern/page-templates/`
4. Upload: `websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/page-templates/page-front-page-stunning.php`

### Step 2: Activate Template
1. WordPress Admin → Pages → Add New (or edit existing front page)
2. Title: "Home" 
3. Page Attributes (right sidebar) → Template → Select **"Stunning Front Page"**
4. Click "Publish" or "Update"

### Step 3: Set as Front Page
1. Settings → Reading
2. Front page displays: Select **"A static page"**
3. Front page: Choose your page with "Stunning Front Page" template
4. Save Changes

### Step 4: Clear Cache
- Clear WordPress cache (if using caching plugin)
- Clear browser cache (Ctrl+Shift+Delete)
- Visit the site!

## Alternative: Direct File Copy (If You Have Server Access)

The file is located at:
- **Local:** `D:\websites\websites\freerideinvestor.com\wp\wp-content\themes\freerideinvestor-modern\page-templates\page-front-page-stunning.php`
- **Remote:** `domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern/page-templates/page-front-page-stunning.php`

Copy the local file to the remote location, then follow Steps 2-4 above.

