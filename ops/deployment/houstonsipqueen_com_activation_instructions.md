
# Theme Activation Instructions for houstonsipqueen.com

## Theme: houstonsipqueen

### Method 1: WordPress Admin (Recommended)
1. Log into WordPress admin: https://houstonsipqueen.com/wp-admin
2. Navigate to: **Appearance > Themes**
3. Find the theme: **houstonsipqueen**
4. Click **"Activate"** button
5. Verify the theme is active

### Method 2: WP-CLI (If available)
```bash
cd /path/to/houstonsipqueen.com/wp
wp theme activate houstonsipqueen
```

### Method 3: Database (Advanced)
If you have database access, you can update the option directly:
```sql
UPDATE wp_options 
SET option_value = 'houstonsipqueen' 
WHERE option_name = 'template' OR option_name = 'stylesheet';
```

### Verification
After activation, visit https://houstonsipqueen.com and verify:
- Theme styling is applied
- Custom header/footer are visible
- Navigation works correctly
- Forms are functional

### Theme Location
Theme files should be at:
`websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen/`

### Troubleshooting
- If theme doesn't appear: Check that theme files are uploaded to server
- If activation fails: Check file permissions
- If styling breaks: Clear cache and check for plugin conflicts
