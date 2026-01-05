# Fix "Service Unavailable" Error for ariajet.site

## ✅ Files Created

1. **`.htaccess`** - Server configuration file
   - Sets index.html as default
   - Handles WordPress/static HTML routing
   - Adds security headers
   - Enables compression and caching

2. **`index.php`** - Fallback PHP file
   - Checks if WordPress exists
   - Serves index.html if WordPress not found
   - Provides error handling

## 🚀 Deployment Steps

### Step 1: Upload Files to Server

Upload these files to your hosting server (via FTP/SFTP or File Manager):

```
ariajet.site/
├── .htaccess          ← Upload this (NEW)
├── index.php          ← Upload this (NEW)
├── index.html         ← Already exists
└── games/              ← Already exists
```

**Important**: Make sure `.htaccess` file is uploaded (it's a hidden file starting with a dot)

### Step 2: Check Server Configuration

#### Via Hostinger File Manager:
1. Log into Hostinger: https://hpanel.hostinger.com
2. Go to **File Manager**
3. Navigate to `public_html` (or your domain's root directory)
4. Upload `.htaccess` and `index.php`
5. Make sure file permissions are correct:
   - `.htaccess` should be `644`
   - `index.php` should be `644`

#### Via FTP/SFTP:
```bash
# Upload files
scp .htaccess user@ariajet.site:/path/to/public_html/
scp index.php user@ariajet.site:/path/to/public_html/
```

### Step 3: Verify File Permissions

```bash
# On server, check permissions
chmod 644 .htaccess
chmod 644 index.php
chmod 644 index.html
```

### Step 4: Check for Maintenance Mode

If your hosting uses a maintenance file, check for:
- `.maintenance` file in root directory
- Remove it if it exists

### Step 5: Clear Cache

1. Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
2. Clear server cache (if using caching plugin)
3. Wait 1-2 minutes for DNS propagation

## 🔍 Troubleshooting

### If Still Getting "Service Unavailable":

1. **Check Server Status**
   - Contact Hostinger support
   - Ask if server is down or in maintenance

2. **Check Application Pool** (if using IIS)
   - Ensure application pool is running
   - Restart if needed

3. **Check PHP Version**
   - Ensure PHP 7.4+ is enabled
   - Check PHP error logs

4. **Check .htaccess Syntax**
   - Some hosts disable certain .htaccess directives
   - Try renaming `.htaccess` to `.htaccess.backup` temporarily
   - If site works, .htaccess has an issue

5. **Check WordPress Installation**
   - If WordPress is installed, ensure `wp-config.php` exists
   - Check database connection

6. **Check Disk Space**
   - Ensure server has enough disk space
   - Check quota limits

## 📋 Quick Checklist

- [ ] `.htaccess` uploaded to server root
- [ ] `index.php` uploaded to server root
- [ ] File permissions set correctly (644)
- [ ] No `.maintenance` file exists
- [ ] Server is running (not in maintenance)
- [ ] PHP is enabled and working
- [ ] Browser cache cleared
- [ ] Tested in incognito/private window

## 🆘 Still Not Working?

If the issue persists after following these steps:

1. **Contact Hostinger Support**
   - Provide them with the error message
   - Ask them to check server logs
   - Request server status check

2. **Check Error Logs**
   - Look for PHP errors in error logs
   - Check Apache/Nginx error logs
   - Look for 503 errors in access logs

3. **Temporary Workaround**
   - Rename `.htaccess` to `.htaccess.old`
   - Test if site loads
   - If yes, .htaccess has an issue
   - If no, it's a server-side problem

## 📝 Notes

- The `.htaccess` file prioritizes `index.html` for static content
- If WordPress is installed, it will use WordPress instead
- The `index.php` file provides a fallback if WordPress isn't installed
- Security headers are included for better protection

---

**Created**: December 20, 2025  
**Purpose**: Fix "Service Unavailable" (503) error on ariajet.site

