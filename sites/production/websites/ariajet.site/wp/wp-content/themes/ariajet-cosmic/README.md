# AriaJet Cosmic WordPress Theme

A magical cosmic-themed WordPress theme for Aria's 2D game showcase. Features animated starfield backgrounds, neon glow effects, floating animations, and galaxy-inspired gradients.

## Features

- 🚀 **Cosmic Design** - Space-themed with animated backgrounds
- 🎮 **Game Showcase** - Custom post type for games
- 🎵 **Playlists Page** - Custom page template for music playlists
- 💻 **Projects Page** - Custom page template for showcasing projects
- ✨ **Animated Elements** - Floating planets, shooting stars, nebula clouds
- 📱 **Responsive** - Mobile-first design
- 🎨 **Customizable** - WordPress Customizer support

## Installation

1. Upload the `ariajet-cosmic` folder to `/wp-content/themes/`
2. Activate the theme through the 'Appearance' menu in WordPress
3. Configure menus in Appearance → Menus
4. Create pages and assign templates

## Page Templates

### Playlists Page
- **Template Name**: Playlists Page
- **File**: `page-playlists.php`
- **Usage**: Create a new page, select "Playlists Page" template

### Projects Page
- **Template Name**: Projects Page
- **File**: `page-projects.php`
- **Usage**: Create a new page, select "Projects Page" template

## Setting Up Pages

1. **Create Playlists Page**:
   - Go to Pages → Add New
   - Title: "Playlists"
   - Select "Playlists Page" template
   - Publish

2. **Create Projects Page**:
   - Go to Pages → Add New
   - Title: "Projects"
   - Select "Projects Page" template
   - Publish

3. **Add to Menu**:
   - Go to Appearance → Menus
   - Add your pages to the Primary Menu
   - Save menu

## Custom Post Type: Games

The theme includes a custom post type for games:

- **Post Type**: `game`
- **Archive**: `/games/`
- **Custom Fields**:
  - Game URL
  - Game Type (2D, Puzzle, Adventure, Survival)
  - Status (Published, Beta, In Development)

## Navigation Menus

The theme supports three menu locations:

1. **Primary Menu** - Main navigation in header
2. **Footer Menu** - Footer navigation
3. **Social Links** - Social media links (optional)

## Customization

### Colors
The theme uses CSS custom properties (variables) defined in `style.css`. Main colors:
- Cosmic Dark: `#0a0a1a`
- Neon Cyan: `#00fff7`
- Neon Pink: `#ff2d95`
- Neon Purple: `#bf00ff`

### Fonts
- **Headings**: Orbitron (Google Fonts)
- **Body**: Poppins (Google Fonts)

## File Structure

```
ariajet-cosmic/
├── style.css              # Main stylesheet
├── functions.php          # Theme functions
├── header.php             # Header template
├── footer.php             # Footer template
├── index.php              # Main template
├── page.php               # Default page template
├── page-playlists.php     # Playlists page template
├── page-projects.php      # Projects page template
├── single.php             # Single post template
├── single-game.php         # Single game template
├── archive-game.php       # Games archive template
├── 404.php                # 404 error template
├── css/
│   └── games.css          # Games-specific styles
└── js/
    ├── main.js            # Main JavaScript
    └── games.js           # Games JavaScript
```

## Support

For issues or questions, check the theme documentation or contact support.

---

**Version**: 1.0.0  
**Author**: Aria  
**License**: GNU General Public License v2 or later

