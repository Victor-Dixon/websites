# Crosby Business Plan Plugin

WordPress plugin to display the business plan for Crosby Ultimate Events on your website.

## Installation

1. Upload the `crosby-business-plan` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode `[crosby_business_plan]` on any page or post

## Usage

### Basic Shortcode
Display the full business plan:
```
[crosby_business_plan]
```

### Display Specific Section
Show only a specific section:
```
[crosby_business_plan section="executive"]
```

### Hide Download Link
Hide the download link:
```
[crosby_business_plan download="false"]
```

### Combined Options
```
[crosby_business_plan section="financial" download="false"]
```

## Available Sections

- `executive` - Executive Summary
- `company` - Company Description
- `products` - Products and Services
- `market` - Market Analysis
- `marketing` - Marketing and Sales Strategy
- `operations` - Operations Plan
- `financial` - Financial Plan
- `management` - Management and Organization
- `risks` - Risk Analysis and Mitigation
- `growth` - Growth Strategy
- `timeline` - Implementation Timeline
- `metrics` - Success Metrics and KPIs
- `all` - Full business plan (default)

## Features

- **Professional Styling**: Beautiful, responsive design that matches your theme
- **Table of Contents**: Easy navigation with anchor links
- **Section Display**: Show full plan or specific sections
- **Print Friendly**: Optimized for printing
- **Mobile Responsive**: Works on all devices
- **SEO Friendly**: Proper HTML structure and semantic markup

## Customization

The plugin uses CSS variables from your theme. If your theme defines these variables, the business plan will automatically match your brand colors:

- `--primary-color` (default: #d4af37 - gold)
- `--secondary-color` (default: #2c3e50 - dark blue)
- `--text-color` (default: #333)
- `--light-bg` (default: #f8f9fa)
- `--white` (default: #ffffff)

## Admin Settings

Access plugin settings at: **Settings → Business Plan**

## Support

For questions or issues, contact the plugin developer.

---

**Version:** 1.0.0  
**Author:** DaDudeKC  
**License:** GPL v2 or later

