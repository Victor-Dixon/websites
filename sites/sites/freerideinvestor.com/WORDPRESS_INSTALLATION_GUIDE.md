# FreeRideInvestor WordPress Installation Guide
===========================================

## 🚨 CRITICAL ISSUE IDENTIFIED

The repository contains **only theme files** but **no WordPress core installation**.
This is why menu links don't work - there's no WordPress site to run the menu code!

## 📋 Current Status

### ✅ What Exists:
- Theme files in `sites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-v2/`
- Menu setup script: `freerideinvestor-menu-setup.php`
- Page templates: `page-about.php`, `page-services.php`, etc.

### ❌ What's Missing:
- WordPress core files (`wp-config.php`, `wp-admin/`, `wp-includes/`)
- Database configuration
- Actual WordPress installation

## 🛠️ SOLUTION: Install WordPress

### Option 1: Fresh WordPress Installation (Recommended)

1. **Download WordPress Core:**
   ```bash
   cd sites/freerideinvestor.com/
   wget https://wordpress.org/latest.tar.gz
   tar -xzf latest.tar.gz
   mv wordpress/* wp/
   rm -rf wordpress latest.tar.gz
   ```

2. **Create wp-config.php:**
   ```bash
   cd wp/
   cp wp-config-sample.php wp-config.php
   # Edit wp-config.php with your database credentials
   ```

3. **Set up database:**
   ```sql
   CREATE DATABASE freerideinvestor_db;
   CREATE USER 'freerideinvestor_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON freerideinvestor_db.* TO 'freerideinvestor_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

4. **Run WordPress installation:**
   ```bash
   wp core install --url="https://freerideinvestor.com" --title="FreeRideInvestor" --admin_user="admin" --admin_email="admin@freerideinvestor.com"
   ```

### Option 2: Use Existing WordPress Installation

If freerideinvestor.com already has WordPress running elsewhere:

1. **Identify the live WordPress installation path**
2. **Copy theme files to live theme directory:**
   ```bash
   # Assuming live WordPress is at /var/www/freerideinvestor.com/
   cp -r sites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-v2/ /var/www/freerideinvestor.com/wp-content/themes/
   ```

3. **Activate the theme:**
   ```bash
   wp theme activate freerideinvestor-v2
   ```

## 🍽️ Menu Setup

After WordPress is installed and theme is activated:

### Automated Setup (Preferred):
```bash
wp eval "require_once('wp-content/themes/freerideinvestor-v2/freerideinvestor-menu-setup.php'); freerideinvestor_setup_pages_and_menu();"
```

### Manual Setup (WordPress Admin):
1. **Create Pages:** WordPress Admin → Pages → Add New
   - About (slug: about, template: About)
   - Services (slug: services, template: Services)
   - Resources (slug: resources, template: Resources)
   - Blog (slug: blog, template: Blog)
   - Contact (slug: contact, template: Contact)
   - Trading Strategies (slug: trading-strategies, template: Trading Strategies)

2. **Create Primary Menu:** Appearance → Menus
   - Add all pages to menu
   - Assign to "Primary Menu" location

3. **Create Footer Menu:** Appearance → Menus
   - Add About, Services, Resources, Contact
   - Assign to "Footer Menu" location

## 🧪 Testing

After setup, test these URLs:
- https://freerideinvestor.com/ (homepage)
- https://freerideinvestor.com/about/
- https://freerideinvestor.com/services/
- https://freerideinvestor.com/resources/
- https://freerideinvestor.com/blog/
- https://freerideinvestor.com/contact/
- https://freerideinvestor.com/trading-strategies/

## 🔧 Troubleshooting

### Menu Not Showing:
1. Check Appearance → Menus → Primary Menu location assigned
2. Verify theme is active: Appearance → Themes
3. Check browser console for JavaScript errors

### Pages Not Loading:
1. Run `wp rewrite flush` to refresh permalinks
2. Check page templates are assigned correctly
3. Verify pages are published (not draft)

### Theme Not Working:
1. Check theme files are in correct directory
2. Verify functions.php has no syntax errors
3. Check file permissions: `chmod 755 wp-content/themes/freerideinvestor-v2/`

## 📞 Support

If issues persist:
1. Check WordPress debug logs
2. Verify PHP version compatibility (7.4+ recommended)
3. Ensure all required PHP extensions are installed
4. Check web server configuration (Apache/Nginx)

---
**This guide addresses the root cause: missing WordPress installation!**
