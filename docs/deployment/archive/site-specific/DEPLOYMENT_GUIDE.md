# 🚀 SouthWest Secret - WordPress Deployment Guide

Complete guide for deploying the SouthWest Secret WordPress theme with GitHub auto-updates.

---

## 📦 Package Contents

This folder contains:

1. **`southwestsecret/`** - WordPress theme
2. **`github-auto-updater/`** - Auto-update plugin
3. **`southwestsecret-theme.zip`** - Ready-to-install theme package
4. **`github-auto-updater.zip`** - Ready-to-install plugin package

---

## 🎯 Step 1: Install WordPress

If you haven't already:

1. Download WordPress from https://wordpress.org/download/
2. Install on your web host
3. Complete the 5-minute installation

---

## 🎨 Step 2: Install the Theme

### Option A: Upload ZIP (Recommended)

1. Log into WordPress admin
2. Go to **Appearance → Themes → Add New**
3. Click **Upload Theme**
4. Choose `southwestsecret-theme.zip`
5. Click **Install Now**
6. Click **Activate**

### Option B: Manual FTP Upload

1. Unzip `southwestsecret-theme.zip`
2. Upload the `southwestsecret` folder to `/wp-content/themes/`
3. Go to **Appearance → Themes**
4. Activate "SouthWest Secret"

---

## 🔄 Step 3: Install GitHub Auto-Updater Plugin

### Upload the Plugin:

1. Go to **Plugins → Add New**
2. Click **Upload Plugin**
3. Choose `github-auto-updater.zip`
4. Click **Install Now**
5. Click **Activate**

---

## 🐙 Step 4: Set Up GitHub Repository

### Create GitHub Repo:

1. Create a new repository on GitHub (e.g., `southwestsecret`)
2. Push your theme files:

```bash
cd D:\websites\southwestsecret.com
git init
git add .
git commit -m "Initial commit - SouthWest Secret theme"
git remote add origin https://github.com/YOUR_USERNAME/southwestsecret.git
git push -u origin main
```

### Generate Personal Access Token:

1. Go to https://github.com/settings/tokens
2. Click **Generate new token (classic)**
3. Give it a name: "SouthWest Secret WordPress"
4. Select scopes: `repo` (all)
5. Click **Generate token**
6. **Copy the token** (you won't see it again!)

---

## ⚙️ Step 5: Configure Auto-Updater

1. In WordPress, go to **Settings → GitHub Updater**
2. Fill in the form:
   - **GitHub Repository URL**: `https://github.com/YOUR_USERNAME/southwestsecret`
   - **GitHub Access Token**: Paste the token you generated
   - **Branch**: `main` (or your default branch)
   - **Webhook Secret**: Create a random string (e.g., `my-secret-key-12345`)

3. **Copy the Webhook URL** shown on the page (e.g., `https://yoursite.com/wp-json/github-updater/v1/webhook`)

4. Click **Save Changes**

---

## 🪝 Step 6: Set Up GitHub Webhook

1. Go to your GitHub repository
2. Click **Settings** → **Webhooks** → **Add webhook**
3. Configure:
   - **Payload URL**: Paste the webhook URL from WordPress
   - **Content type**: `application/json`
   - **Secret**: Enter the same webhook secret you used in WordPress
   - **Which events**: Select "Just the push event"
   - **Active**: ✓ Check this box

4. Click **Add webhook**

---

## ✅ Step 7: Test the Auto-Update

### Test Manual Update:

1. In WordPress, go to **Settings → GitHub Updater**
2. Click **Test Update Now**
3. You should see "✅ Update successful!"

### Test Automatic Update:

1. Make a small change to your theme (e.g., edit `style.css`)
2. Commit and push to GitHub:

```bash
git add .
git commit -m "Test auto-update"
git push
```

3. GitHub will trigger the webhook
4. WordPress will automatically pull the changes!
5. Refresh your site to see the update

---

## 🎵 Step 8: Add Your Screw Tapes

### Add Tapes via WordPress Admin:

1. Go to **Screw Tapes → Add New**
2. Enter tape name: "Screw Tape Vol. 3"
3. In the **YouTube Video ID** box (sidebar), enter just the ID: `oYqlfb2sghc`
4. Click **Publish**

The tape will appear automatically in the cassette library!

### Or Edit Manually:

Edit `index.php` and add more cassette divs to the tape grid.

---

## 🔧 Troubleshooting

### Theme not updating?

1. Check webhook deliveries in GitHub (Settings → Webhooks → Recent Deliveries)
2. Verify the secret matches in both places
3. Check WordPress error logs

### Cassette tapes not playing?

1. Verify YouTube video IDs are correct
2. Check browser console for JavaScript errors
3. Ensure YouTube embed is allowed (not blocked by ad blocker)

### Permission errors?

Ensure WordPress has write permissions:
```bash
chmod 755 /wp-content/themes/southwestsecret
```

---

## 🎉 You're Done!

Your SouthWest Secret website is now live with:
- ✅ Interactive cassette tape library
- ✅ Automatic GitHub updates
- ✅ Easy content management

Every time you push to GitHub, your WordPress site updates automatically!

---

## 📞 Need Help?

- **Theme Issues**: Check `README.md` in the theme folder
- **Plugin Issues**: Check Settings → GitHub Updater for status
- **GitHub**: https://github.com/YOUR_USERNAME/southwestsecret

---

**Built with 🎧 by Agent-1**

