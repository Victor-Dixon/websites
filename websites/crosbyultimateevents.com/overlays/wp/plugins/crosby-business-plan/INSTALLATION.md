# Installation Guide: Crosby Business Plan Plugin

## Quick Start

### Step 1: Upload Plugin Files
1. Navigate to your WordPress installation
2. Go to `/wp-content/plugins/` directory
3. Create a folder named `crosby-business-plan`
4. Upload all plugin files to this folder

**Plugin Structure:**
```
wp-content/plugins/crosby-business-plan/
├── crosby-business-plan.php (main plugin file)
├── assets/
│   └── style.css
├── templates/
│   └── business-plan-display.php
└── README.md
```

### Step 2: Activate Plugin
1. Log in to WordPress Admin
2. Go to **Plugins → Installed Plugins**
3. Find "Crosby Ultimate Events - Business Plan"
4. Click **Activate**

### Step 3: Add to Your Website

#### Option A: Create a Dedicated Page
1. Go to **Pages → Add New**
2. Title: "Business Plan" or "Our Business Plan"
3. Add shortcode: `[crosby_business_plan]`
4. Publish the page
5. Add to your navigation menu if desired

#### Option B: Add to Existing Page
1. Edit any existing page
2. Add shortcode: `[crosby_business_plan]`
3. Update the page

#### Option C: Add to Sidebar/Widget Area
1. Go to **Appearance → Widgets**
2. Add a "Shortcode" or "Text" widget
3. Insert: `[crosby_business_plan]`
4. Save

## Shortcode Examples

### Full Business Plan
```
[crosby_business_plan]
```

### Executive Summary Only
```
[crosby_business_plan section="executive"]
```

### Financial Section Only
```
[crosby_business_plan section="financial"]
```

### Hide Download Link
```
[crosby_business_plan download="false"]
```

## Recommended Page Setup

### Create a Business Plan Page
1. **Page Title**: "Business Plan" or "Our Business Plan"
2. **Page Slug**: `/business-plan` or `/about/business-plan`
3. **Content**: 
   ```
   [crosby_business_plan]
   ```
4. **Add to Menu**: Consider adding to "About" or main navigation

### Alternative: Add to About Page
If you have an "About Us" page, you could add a section:
```
## Our Business Plan

[crosby_business_plan section="executive"]
```

## Customization

### Matching Your Theme Colors
The plugin automatically uses your theme's CSS variables. If your theme defines:
- `--primary-color`
- `--secondary-color`
- `--text-color`
- `--light-bg`
- `--white`

The business plan will match your brand colors automatically.

### Custom CSS (Optional)
If you need additional styling, add to your theme's `style.css` or use a custom CSS plugin:

```css
.crosby-business-plan {
    /* Your custom styles */
}
```

## Troubleshooting

### Plugin Not Showing
- Ensure the plugin is activated
- Check that the shortcode is correct: `[crosby_business_plan]`
- Clear any caching plugins
- Check for JavaScript errors in browser console

### Styling Issues
- Clear browser cache
- Check if your theme's CSS is overriding plugin styles
- Verify CSS file is loading (check page source)

### Shortcode Not Working
- Ensure you're using the exact shortcode: `[crosby_business_plan]`
- Check for typos or extra spaces
- Try deactivating and reactivating the plugin

## Support

For issues or questions:
1. Check the README.md file
2. Review WordPress error logs
3. Contact the plugin developer

---

**Plugin Version:** 1.0.0  
**WordPress Version Required:** 5.0+  
**PHP Version Required:** 7.2+

