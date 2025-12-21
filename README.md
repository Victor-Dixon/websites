# FreeRideInvestor Vlog Templates

This directory contains the vlog/blog templates for FreeRideInvestor.

## Templates

### 1. `blog_template_basic.html`
A basic HTML template with Jinja2 templating for blog/vlog posts. Features:
- Dark theme with green accent colors (#0a4d3e)
- Simple, clean layout
- Header, sections, images, and footer
- Email subscription form
- Timeline support

### 2. `blog_template_advanced.html`
An advanced HTML template with enhanced features:
- Modern dark theme with CSS variables
- Responsive design (mobile-friendly)
- Enhanced typography and spacing
- Code block support
- Blockquotes styling
- Print-friendly styles
- Better accessibility

### 3. `page-dev-blog.php`
WordPress template for the development blog page:
- Hero section
- Mission statement
- Latest updates grid
- Development insights blog posts
- WordPress integration

### 4. `devlog.css`
CSS stylesheet for devlog posts:
- Dark theme with green highlights
- Grid layouts
- Responsive design
- Hover effects
- Call-to-action styling

## Usage

### HTML Templates (Jinja2)
These templates use Jinja2 syntax and require variables:
- `post_title` - Blog post title
- `post_subtitle` - Blog post subtitle
- `introduction` - Introduction section (optional)
- `sections` - Array of section objects with `title` and `content`
- `image` - Image object with `title`, `url`, and `alt` (optional)
- `conclusion` - Conclusion section (optional)
- `cta` - Call-to-action object with `title`, `content`, and `form_action`

### WordPress Template
Use as a page template in WordPress:
1. Upload to your theme's `page-templates` directory
2. Create a new page in WordPress
3. Select "Dev Blog" as the template
4. Publish the page

## Features

- **Dark Theme**: All templates use a dark color scheme suitable for developer/trader content
- **Responsive**: Mobile-friendly designs
- **Brand Colors**: FreeRideInvestor green accent colors (#0a4d3e, #116611)
- **Modern UI**: Clean, professional appearance
- **Accessibility**: Semantic HTML and ARIA labels where applicable


