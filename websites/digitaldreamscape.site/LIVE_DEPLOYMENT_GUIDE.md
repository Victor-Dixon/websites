# 🚀 DIGITAL DREAMSCAPE - LIVE DEPLOYMENT GUIDE

## 🎯 **GET YOUR POSTS ONLINE**

Your Digital Dreamscape system is working perfectly locally, but the posts need to be imported to your live WordPress database. Here are **4 methods** to get your content online:

---

## 📋 **CURRENT SITUATION**

- ✅ **Local System**: 3 episodes created and working
- ✅ **Live Theme**: Digital Dreamscape theme is active
- ❌ **Live Content**: Posts missing from live database

---

## 🎯 **METHOD 1: MANUAL IMPORT (EASIEST)**

### Step 1: Export Your Posts
```bash
cd D:\websites\websites\digitaldreamscape.site
php export_posts.php > my_posts_backup.json
```

### Step 2: Go to WordPress Admin
Navigate to: `https://digitaldreamscape.site/wp-admin/`

### Step 3: Create Posts Manually

#### **Post 1: "Add relationship preservation in exports"**
1. Click **Posts > Add New**
2. **Title**: `Add relationship preservation in exports`
3. **Content**: `Ensure all post relationships and metadata links are preserved during export/import`
4. **Excerpt**: `Ensure all post relationships and metadata links are preserved during export/import...`
5. **Status**: Publish
6. **Custom Fields** (at bottom):
   - **Name**: `artifact_type`, **Value**: `episode`
   - **Name**: `questline`, **Value**: `technical-debt`
7. Click **Publish**

#### **Post 2: "Data Migration Strategy for Digital Dreamscape"**
1. Click **Posts > Add New**
2. **Title**: `Data Migration Strategy for Digital Dreamscape`
3. **Content**: Copy the full JSON content from your exported file (it's the agent analysis)
4. **Excerpt**: `Strategic analysis of data migration requirements and implementation approaches`
5. **Status**: Publish
6. **Custom Fields**:
   - **Name**: `artifact_type`, **Value**: `canon`
   - **Name**: `questline`, **Value**: `technical-debt`
7. Click **Publish**

#### **Post 3: "System Monitoring Dashboard Analysis"**
1. Click **Posts > Add New**
2. **Title**: `System Monitoring Dashboard Analysis`
3. **Content**: Copy the full JSON content from your exported file (agent analysis)
4. **Excerpt**: `Comprehensive analysis of system monitoring requirements and implementation approach`
5. **Status**: Publish
6. **Custom Fields**:
   - **Name**: `artifact_type`, **Value**: `canon`
   - **Name**: `questline`, **Value**: `system-automation`
7. Click **Publish**

### Step 4: Verify
Visit `https://digitaldreamscape.site/blog/` - you should now see your posts!

---

## 🛠️ **METHOD 2: WP-CLI (AUTOMATED)**

If you have WP-CLI access on your hosting server:

### Step 1: Get the Commands
```bash
cd D:\websites\websites\digitaldreamscape.site
php import_to_live_site.php
```
This will output the exact WP-CLI commands you need.

### Step 2: Run on Live Server
SSH into your hosting server and run the generated commands:
```bash
wp post create --post_title="Add relationship preservation in exports" --post_content="..." --post_status=publish
wp post meta set [POST_ID] artifact_type episode
wp post meta set [POST_ID] questline technical-debt
# ... repeat for other posts
```

---

## 💾 **METHOD 3: DATABASE IMPORT**

If you have direct database access:

### Step 1: Export from Local System
```bash
php export_posts.php > posts_for_live.json
```

### Step 2: Import to Live Database
You'll need to manually insert the posts into your live WordPress database using phpMyAdmin or similar.

### Step 3: Run this SQL for each post:
```sql
INSERT INTO wp_posts (post_title, post_content, post_excerpt, post_status, post_date, post_modified)
VALUES ('Your Post Title', 'Your content...', 'Your excerpt...', 'publish', NOW(), NOW());

-- Get the post ID from above, then:
INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
VALUES (POST_ID, 'artifact_type', 'episode'),
       (POST_ID, 'questline', 'technical-debt');
```

---

## 🌐 **METHOD 4: REST API (ADVANCED)**

### Step 1: Enable Application Passwords
In WordPress admin: **Users > Profile > Application Passwords**

### Step 2: Create Import Script
```php
<?php
// Run this on your local machine
$posts = json_decode(file_get_contents('posts_for_live.json'), true);

foreach ($posts['posts'] as $post) {
    $data = [
        'title' => $post['post_title'],
        'content' => $post['post_content'],
        'excerpt' => $post['post_excerpt'],
        'status' => 'publish'
    ];

    // Add your authentication headers
    $ch = curl_init('https://digitaldreamscape.site/wp-json/wp/v2/posts');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode('username:application_password')
    ]);
    curl_exec($ch);
    curl_close($ch);
}
?>
```

---

## ✅ **VERIFICATION STEPS**

After importing, check these:

### 1. Blog Page
Visit: `https://digitaldreamscape.site/blog/`
- Should show "The Dreamscape Codex" header
- Should display your 3 posts with metadata

### 2. Post Content
Click on any post - should show:
- Full content with proper formatting
- Custom metadata (artifact type, questline)
- Proper styling from your theme

### 3. Theme Features
- Filter buttons should work
- Search functionality should work
- Responsive design should work on mobile

---

## 🔄 **SET UP AUTOMATION (FUTURE)**

Once posts are live, set up continuous deployment:

### Option A: Cron Job on Live Server
```bash
# Add to crontab on live server
*/30 * * * * cd /path/to/site && php auto_promotion_daemon.php run
```

### Option B: GitHub Actions
Set up automated deployment when you push to devlogs/ or agents/output/

### Option C: Webhook Integration
Configure webhooks to trigger promotion when files are added

---

## 🐛 **TROUBLESHOOTING**

### Posts Not Showing
- Clear WordPress cache (if using caching plugin)
- Check post status is "publish"
- Verify theme is active

### Theme Not Working
- Go to WordPress Admin > Appearance > Themes
- Ensure "Digital Dreamscape" is active

### Metadata Missing
- Check if custom fields are enabled in Screen Options
- Manually add missing metadata via post editor

---

## 🎯 **QUICK START SUMMARY**

1. **Export posts**: `php export_posts.php > backup.json`
2. **Go to WordPress admin**: `https://digitaldreamscape.site/wp-admin/`
3. **Create 3 posts manually** with the content above
4. **Add custom fields** for artifact_type and questline
5. **Publish and verify** at `/blog/`

**Your Digital Dreamscape will be live and evolving!** 🌌⚡🤖