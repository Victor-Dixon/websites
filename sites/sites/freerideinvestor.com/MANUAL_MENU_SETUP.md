# Manual Menu Setup for FreeRideInvestor
=======================================

If automated setup fails, follow these manual steps.

## Step 1: Verify Theme Installation

1. WordPress Admin → Appearance → Themes
2. Ensure "FreeRideInvestor V2" theme is active
3. If not visible, upload the theme ZIP file

## Step 2: Create Required Pages

WordPress Admin → Pages → Add New

Create these 6 pages with exact slugs and templates:

| Title | Slug | Template | Content |
|-------|------|----------|---------|
| About | about | About | About page content |
| Services | services | Services | Services page content |
| Resources | resources | Resources | Resources page content |
| Blog | blog | Blog | Blog page content |
| Contact | contact | Contact | Contact page content |
| Trading Strategies | trading-strategies | Trading Strategies | Trading content |

**Important:** Set Page Attributes → Template for each page!

## Step 3: Create Primary Menu

1. WordPress Admin → Appearance → Menus
2. Click "Create a new menu"
3. Name: "Primary Menu"
4. Check "Primary Menu" location
5. Add these menu items:
   - Home (link to your homepage URL)
   - About (select "About" page)
   - Services (select "Services" page)
   - Trading Strategies (select "Trading Strategies" page)
   - Resources (select "Resources" page)
   - Blog (select "Blog" page)
   - Contact (select "Contact" page)
6. Save Menu

## Step 4: Create Footer Menu

1. Create new menu named "Footer Menu"
2. Check "Footer Menu" location
3. Add these pages: About, Services, Resources, Contact
4. Save Menu

## Step 5: Test Menu Navigation

Visit your site and click each menu item. All should load the correct pages.

## Step 6: Check Page Templates

For each page, edit and verify:
- Page Attributes → Template shows correct template name
- Content displays properly
- No template errors

## Common Issues & Fixes

### Menu Not Showing:
- Appearance → Menus → verify locations assigned
- Theme may not support menu locations

### Wrong Templates:
- Page Attributes → Template must be set correctly
- Template files must exist in theme directory

### Broken Links:
- Check permalinks: Settings → Permalinks → Save
- Page slugs must match exactly

### Theme Not Loading:
- Check theme files exist in wp-content/themes/freerideinvestor-v2/
- Functions.php must not have syntax errors
