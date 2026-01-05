# Theme File Editor API Plugin

WordPress plugin that adds REST API endpoints for programmatic theme file editing.

## Features

- ✅ REST API endpoint for updating theme files
- ✅ REST API endpoint for reading theme files
- ✅ Secure authentication and permission checks
- ✅ File path validation (prevents directory traversal)
- ✅ Automatic cache clearing after file updates

## Installation

### Method 1: Manual Installation

1. **Upload Plugin**:
   - Upload the `theme-file-editor-api` folder to `/wp-content/plugins/`
   - Or use FTP/SFTP to upload to your WordPress site

2. **Activate Plugin**:
   - Go to WordPress Admin → Plugins
   - Find "Theme File Editor API"
   - Click "Activate"

3. **Verify Installation**:
   - Check that REST API endpoint is available:
   - `https://yoursite.com/wp-json/theme-file-editor/v1/update-file`

### Method 2: Via WordPress Admin

1. **Zip the Plugin**:
   - Create a zip file of the `theme-file-editor-api` folder
   - Name it: `theme-file-editor-api.zip`

2. **Upload via Admin**:
   - Go to WordPress Admin → Plugins → Add New
   - Click "Upload Plugin"
   - Select the zip file
   - Click "Install Now"
   - Click "Activate Plugin"

## Configuration

### Required Settings

1. **Enable File Editing**:
   - Ensure `DISALLOW_FILE_EDIT` is NOT set in `wp-config.php`
   - If it's set to `true`, remove it or set to `false`

2. **User Permissions**:
   - User must have `edit_theme_options` capability
   - Administrator role has this by default

3. **Application Password** (for REST API authentication):
   - Go to WordPress Admin → Users → Your Profile
   - Scroll to "Application Passwords"
   - Create a new application password
   - Save the password (you'll need it for API calls)

## API Endpoints

### Update Theme File

**Endpoint**: `POST /wp-json/theme-file-editor/v1/update-file`

**Authentication**: Basic Auth (WordPress username + Application Password)

**Parameters**:
- `theme` (string, required): Theme name (e.g., "freerideinvestor")
- `file` (string, required): File path relative to theme (e.g., "functions.php")
- `content` (string, required): New file content

**Example Request**:
```bash
curl -X POST https://yoursite.com/wp-json/theme-file-editor/v1/update-file \
  -u "username:application_password" \
  -H "Content-Type: application/json" \
  -d '{
    "theme": "freerideinvestor",
    "file": "functions.php",
    "content": "<?php\n// Your file content here\n"
  }'
```

**Example Response**:
```json
{
  "success": true,
  "message": "File updated successfully.",
  "file": "functions.php",
  "theme": "freerideinvestor",
  "bytes_written": 53088
}
```

### Get Theme File

**Endpoint**: `GET /wp-json/theme-file-editor/v1/get-file`

**Authentication**: Basic Auth (WordPress username + Application Password)

**Parameters**:
- `theme` (string, required): Theme name
- `file` (string, required): File path relative to theme

**Example Request**:
```bash
curl -X GET "https://yoursite.com/wp-json/theme-file-editor/v1/get-file?theme=freerideinvestor&file=functions.php" \
  -u "username:application_password"
```

**Example Response**:
```json
{
  "success": true,
  "file": "functions.php",
  "theme": "freerideinvestor",
  "content": "<?php\n// File content...\n",
  "size": 53088
}
```

## Security

### Built-in Security Features

1. **Authentication**: Requires WordPress user login
2. **Permission Checks**: Requires `edit_theme_options` capability
3. **File Path Validation**: Prevents directory traversal attacks
4. **File Type Validation**: Only allows safe file types (php, css, js, txt)
5. **File Location Validation**: Ensures files are within theme directory

### Security Recommendations

1. **Use Application Passwords**: Don't use your main WordPress password
2. **HTTPS Only**: Always use HTTPS for API calls
3. **Limit Access**: Only grant access to trusted users
4. **Monitor Usage**: Check WordPress logs for API usage
5. **Regular Updates**: Keep WordPress and plugins updated

## Troubleshooting

### Error: "File editing is disabled"

**Solution**: Remove `DISALLOW_FILE_EDIT` from `wp-config.php` or set to `false`

### Error: "You do not have permission"

**Solution**: Ensure user has Administrator role or `edit_theme_options` capability

### Error: "Invalid file path"

**Solution**: Check that file path is correct and doesn't contain `..` (directory traversal)

### Error: "File is not writable"

**Solution**: Check file permissions on server (should be 644 or 664)

### Error: "Directory is not writable"

**Solution**: Check directory permissions on server (should be 755 or 775)

## Usage with Python Tool

See `tools/deploy_via_wordpress_rest_api.py` for Python integration.

**Example**:
```bash
python tools/deploy_via_wordpress_rest_api.py \
  --site https://freerideinvestor.com \
  --theme freerideinvestor \
  --file D:/websites/FreeRideInvestor/functions.php \
  --username your_username \
  --password your_application_password
```

## Changelog

### 1.0.0
- Initial release
- REST API endpoint for updating theme files
- REST API endpoint for reading theme files
- Security and permission checks
- File validation

## License

GPL v2 or later

## Author

Agent-7 (Web Development Specialist)




