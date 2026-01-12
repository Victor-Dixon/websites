# WeAreSwarm Online Theme

A WordPress theme for WeAreSwarm.online - Dream.OS documentation & case studies platform with trading robot integration and swarm intelligence features.

## Features

- **Swarm Intelligence Design**: Neural network-inspired animations and particle effects
- **Responsive Layout**: Mobile-first design that works on all devices
- **AI Agent Integration**: Built-in support for swarm intelligence features
- **Trading Robot Ready**: Pre-configured for trading robot plugin integration
- **Modern Animations**: CSS animations and JavaScript interactions
- **SEO Optimized**: Built-in SEO best practices
- **Performance Focused**: Optimized loading and caching

## Installation

1. Download the theme ZIP file
2. Upload to WordPress admin under Appearance > Themes > Add New > Upload Theme
3. Activate the "WeAreSwarm Online" theme
4. Configure theme options in Customizer

## Theme Structure

```
weareswarm-theme/
├── style.css              # Main stylesheet with theme header
├── functions.php          # Theme functions and setup
├── index.php              # Main template file
├── front-page.php         # Front page template with hero section
├── header.php             # Header template
├── footer.php             # Footer template
├── template-parts/        # Reusable template parts
│   └── hero-swarm.php     # Hero section template
├── js/                    # JavaScript files
│   └── script.js          # Main theme JavaScript
├── images/                # Theme images and assets
├── screenshot.png         # Theme preview image
└── README.md              # This file
```

## Customization

### Colors
The theme uses CSS custom properties for easy color customization:

```css
:root {
    --swarm-purple: #a855f7;
    --swarm-cyan: #06b6d4;
    --swarm-pink: #ec4899;
    --swarm-yellow: #f59e0b;
    --swarm-dark: #1a1a2e;
}
```

### Animations
Swarm intelligence animations can be customized in `js/script.js` and the CSS animations.

### Layout
The theme uses Tailwind CSS classes for responsive design. Modify the grid layouts in the template files as needed.

## Plugins Compatibility

This theme is designed to work with:

- **Trading Robot Plugin**: Core trading functionality
- **TRP Paper Trading Stats**: Paper trading statistics
- **TRP Swarm Status**: Swarm intelligence monitoring
- **Hostinger Reach Online Plugin**: Hosting integration

## Development

### Requirements
- WordPress 6.0+
- PHP 8.0+
- Modern browser support

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
- [WeAreSwarm.online](https://weareswarm.online)
- [Dream.OS Documentation](https://weareswarm.online/docs)

## License

GPL v2 or later - Same as WordPress

## Changelog

### Version 1.0.0
- Initial release
- Swarm intelligence hero section
- Responsive design
- Trading robot integration
- Neural network animations
- Mobile-optimized navigation