# Theme Files Upload Instructions

**Issue:** Theme files exist in repository but need to be uploaded to production server.

---

## houstonsipqueen.com

**Current Status:** Theme showing as "Broken" - Stylesheet missing

**Required Files to Upload:**
```
websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen/
├── style.css
├── functions.php
├── header.php
├── footer.php
├── index.php
├── page-quote.php
└── js/
    └── main.js
```

**Upload Method:**
1. **Via FTP/SFTP:**
   - Connect to server
   - Navigate to: `/wp-content/themes/`
   - Upload entire `houstonsipqueen/` directory
   - Set permissions: 755 for directories, 644 for files

2. **Via WordPress Admin:**
   - Go to **Appearance > Themes > Add New > Upload Theme**
   - Create ZIP of theme directory
   - Upload and install

---

## digitaldreamscape.site

**Required Files to Upload:**
```
websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/
├── style.css
├── functions.php
├── header.php
├── footer.php
├── index.php
└── js/
    └── main.js
```

**Upload Method:** Same as above

---

## Quick Fix: Upload via WordPress Admin

1. **Create ZIP files:**
   - Zip the theme directories
   - Name them: `houstonsipqueen.zip` and `digitaldreamscape.zip`

2. **Upload via WordPress:**
   - Go to **Appearance > Themes > Add New > Upload Theme**
   - Choose ZIP file
   - Click **Install Now**
   - Click **Activate** after installation

---

**Priority:** High - Theme files must be on server before activation

