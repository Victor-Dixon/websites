# Guestbook & Birthday Fun Features

## Overview

This WordPress theme now includes:
1. **Guestbook Page** - Visitors can submit birthday messages
2. **Birthday Fun Page** - Interactive animated cat with confetti
3. **WordPress Admin Panel** - Manage and approve guestbook entries
4. **Python Automation** - Optional automation scripts for bulk operations

## Installation

1. **Activate the theme** in WordPress admin (Appearance → Themes)
2. The database table will be created automatically
3. Pages will be created automatically:
   - `/guestbook` - Guestbook page
   - `/birthday-fun` - Birthday Fun page

## Guestbook Features

### For Visitors
- Submit name and birthday message
- View approved messages
- Character counter (500 max)
- Form validation

### For Administrators
- **WordPress Admin Menu**: "Guestbook" appears in admin sidebar
- **Approve/Reject Messages**: Click buttons to moderate entries
- **View All Entries**: See pending, approved, and rejected messages
- **Delete Entries**: Remove unwanted messages

### Database Structure
Table: `wp_guestbook_entries`
- `id` - Auto-increment ID
- `guest_name` - Visitor's name (max 100 chars)
- `message` - Birthday message (text)
- `status` - pending/approved/rejected
- `created_at` - Timestamp

## Birthday Fun Page

### Features
- **Animated Birthday Cat**: CSS-animated cat with party hat
- **Click/Tap Interaction**: Cat responds to clicks
- **Confetti Animation**: Colorful confetti on click
- **Sound Effects**: Simple beep sound (Web Audio API)
- **Click Counter**: Tracks interactions
- **Fun Messages**: Random birthday messages appear

### Mobile Support
- Touch-friendly
- Responsive design
- Works on all devices

## Python Automation

### Setup
1. Install dependencies:
   ```bash
   pip install mysql-connector-python python-dotenv
   ```

2. Create `.env` file:
   ```
   WP_DB_HOST=localhost
   WP_DB_USER=your_db_user
   WP_DB_PASSWORD=your_db_password
   WP_DB_NAME=your_wordpress_db
   ```

### Usage
```bash
# View statistics
python guestbook_automation.py stats

# View pending entries
python guestbook_automation.py pending

# Approve an entry
python guestbook_automation.py approve 123

# Auto-approve recent entries (last 24 hours)
python guestbook_automation.py auto-approve 24
```

## Future Blog Structure

A blog post type is prepared but commented out in `functions.php`. When ready:
1. Uncomment the `southwestsecret_register_blog_post_type()` function
2. Uncomment the `add_action('init', ...)` line
3. Create a `page-blog.php` template
4. Blog will be available at `/blog`

## WordPress Editability

✅ **All features are fully editable through WordPress:**
- Pages can be edited in WordPress admin (Pages → All Pages)
- Guestbook entries managed in WordPress admin (Guestbook menu)
- Template files can be customized
- Styles can be modified in WordPress Customizer (if theme supports it)

## Notes

- **No existing content changed** - All new features are separate pages
- **Colors preserved** - Uses existing theme color variables
- **Layout preserved** - New pages follow theme structure
- **Responsive** - Works on all screen sizes

## Support

For issues or questions, check:
- WordPress Admin → Guestbook (for entry management)
- Theme files in `/wordpress-theme/southwestsecret/`
- Python script: `guestbook_automation.py`

