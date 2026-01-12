# Crosby Ultimate Events Theme

A WordPress theme for CrosbyUltimateEvents.com - Ultimate Frisbee events, tournaments, and community platform with trading robot integration.

## Features

- **Ultimate Frisbee Design**: Field markings, frisbee animations, and sports-themed UI
- **Event Management**: Custom event post types with registration and scheduling
- **Community Focus**: Player statistics, team management, and social features
- **Responsive Layout**: Mobile-first design optimized for all devices
- **Trading Robot Ready**: Pre-configured for trading robot plugin integration
- **Modern Animations**: CSS animations and JavaScript interactions for frisbee and field effects
- **SEO Optimized**: Built-in SEO best practices for event promotion

## Installation

1. Download the theme ZIP file
2. Upload to WordPress admin under Appearance > Themes > Add New > Upload Theme
3. Activate the "Crosby Ultimate Events" theme
4. Configure theme options in Customizer
5. Set up event pages and navigation menus

## Theme Structure

```
crosbyultimateevents-theme/
├── style.css              # Main stylesheet with theme header
├── functions.php          # Theme functions and setup
├── index.php              # Main template file
├── front-page.php         # Front page template with hero section
├── header.php             # Header template
├── footer.php             # Footer template
├── template-parts/        # Reusable template parts
│   └── hero-events.php    # Hero section template
├── js/                    # JavaScript files
│   └── script.js          # Main theme JavaScript
├── images/                # Theme images and assets
├── screenshot.png         # Theme preview image
└── README.md              # This file
```

## Customization

### Colors
The theme uses ultimate frisbee-themed colors:

```css
:root {
    --ultimate-green: #52b788;
    --ultimate-green-dark: #2d6a4f;
    --ultimate-green-light: #74c69d;
    --field-green: #1a472a;
}
```

### Animations
Frisbee and field animations can be customized in `js/script.js` and the CSS animations.

### Events
The theme includes custom event post types. Configure event fields in the admin panel.

## Plugins Compatibility

This theme is designed to work with:

- **Trading Robot Plugin**: Core trading functionality
- **TRP Paper Trading Stats**: Paper trading statistics
- **TRP Swarm Status**: Swarm intelligence monitoring
- **Event Management Plugins**: The Events Calendar, Event Espresso
- **Sports Leagues**: League management and scoring systems

## Development

### Requirements
- WordPress 6.0+
- PHP 8.0+
- Modern browser support

### Custom Post Types
The theme registers an "Event" custom post type with the following fields:
- Event Date
- Event Location
- Max Capacity

### Building
The theme is built with modern web standards and requires no build process for basic usage.

### Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For support and documentation, visit:
- [CrosbyUltimateEvents.com](https://crosbyultimateevents.com)
- [Ultimate Frisbee Resources](https://crosbyultimateevents.com/resources)

## License

GPL v2 or later - Same as WordPress

## Changelog

### Version 1.0.0
- Initial release
- Ultimate frisbee field hero section
- Custom event post types
- Frisbee and field animations
- Community statistics dashboard
- Mobile-responsive design
- Trading robot integration ready