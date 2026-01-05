# Crosby Ultimate Events WordPress Theme

Professional WordPress theme for private chef and event planning services.

## Features

- **Modern Design**: Clean, professional layout perfect for event planning businesses
- **Responsive**: Mobile-friendly design that works on all devices
- **Blog Support**: Full blog functionality for content marketing
- **Custom Post Types**: Built-in support for Services and Events
- **SEO Friendly**: Semantic HTML and proper WordPress structure
- **Easy Customization**: Well-organized CSS with CSS variables

## Installation

1. Upload the theme folder to `/wp-content/themes/`
2. Activate the theme in WordPress Admin → Appearance → Themes
3. Configure menus in Appearance → Menus
4. Set up widgets in Appearance → Widgets

## Theme Structure

```
crosbyultimateevents/
├── style.css          # Main stylesheet
├── functions.php      # Theme functions and setup
├── index.php          # Main blog template
├── single.php         # Single post template
├── page.php           # Page template
├── header.php         # Header template
└── footer.php         # Footer template
```

## Customization

### Colors

Edit CSS variables in `style.css`:

```css
:root {
    --primary-color: #d4af37;    /* Gold (primary brand color) */
    --secondary-color: #111111;  /* Near-black (headings, accents) */
    --accent-color: #f5c542;     /* Bright gold accent (hover states, highlights) */
}
```

### Menus

1. Go to Appearance → Menus
2. Create a new menu
3. Assign to "Primary Menu" location

## Custom Post Types

The theme includes two custom post types:

- **Services**: For showcasing your services
- **Events**: For highlighting past/past events

These appear in the WordPress admin automatically.

## Support

For questions or issues, contact the theme developer.

---

**Version**: 1.0.0  
**Author**: DaDudeKC  
**License**: GPL v2 or later
