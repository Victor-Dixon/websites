# 🚀 SouthWest Secret - GitHub Deployment Guide

## Step-by-Step: Get Your Site Live on GitHub Pages!

### ✅ Prerequisites
- GitHub account (free at [github.com](https://github.com/join))
- Git installed on your computer

---

## 📋 Deployment Steps

### Step 1: Create a New Repository on GitHub

1. Go to [github.com/new](https://github.com/new)
2. **Repository name**: `southwestsecret` (or any name you want)
3. **Description**: "SouthWest Secret - Chopped & Screwed DJ Website"
4. **Public** repository (required for free GitHub Pages)
5. **DO NOT** initialize with README, .gitignore, or license (we already have these)
6. Click **"Create repository"**

### Step 2: Connect Your Local Project to GitHub

You're already in the `southwestsecret.com` folder. Now run these commands:

```bash
# Add your GitHub repository as remote
# Replace YOUR_USERNAME with your actual GitHub username
git remote add origin https://github.com/YOUR_USERNAME/southwestsecret.git

# Rename branch to main (if not already)
git branch -M main

# Push your code to GitHub
git push -u origin main
```

**Example:**
If your GitHub username is `djsecret`, the command would be:
```bash
git remote add origin https://github.com/djsecret/southwestsecret.git
```

### Step 3: Enable GitHub Pages

1. Go to your repository on GitHub
2. Click **"Settings"** (top right)
3. Scroll down and click **"Pages"** (left sidebar)
4. Under **"Source"**:
   - Select branch: **main**
   - Select folder: **/ (root)**
5. Click **"Save"**
6. Wait 1-2 minutes...

### Step 4: Your Site is LIVE! 🎉

Your website will be available at:
```
https://YOUR_USERNAME.github.io/southwestsecret/
```

**Example:**
`https://djsecret.github.io/southwestsecret/`

---

## 🔄 Automatic Updates

Now whenever you make changes:

```bash
# Make your changes to files
# Then:

git add .
git commit -m "Update: description of what you changed"
git push

# Your website updates automatically in 1-2 minutes!
```

---

## 🌐 Using a Custom Domain (southwestsecret.com)

### Option A: Buy the Domain First

1. **Purchase southwestsecret.com** from:
   - GoDaddy
   - Namecheap
   - Google Domains
   - Cloudflare

2. **Add DNS Records** (in your domain registrar):

   **A Records** (point to GitHub):
   ```
   Type: A
   Name: @
   Value: 185.199.108.153
   
   Type: A
   Name: @
   Value: 185.199.109.153
   
   Type: A
   Name: @
   Value: 185.199.110.153
   
   Type: A
   Name: @
   Value: 185.199.111.153
   ```

   **CNAME Record** (for www):
   ```
   Type: CNAME
   Name: www
   Value: YOUR_USERNAME.github.io
   ```

3. **Configure in GitHub:**
   - Go to Settings → Pages
   - Under "Custom domain", enter: `southwestsecret.com`
   - Click "Save"
   - Check "Enforce HTTPS"

4. **Wait 24-48 hours** for DNS to propagate

### Option B: Use GitHub Subdomain (Free & Instant)

Keep using:
```
https://YOUR_USERNAME.github.io/southwestsecret/
```

---

## 🎨 Making Changes

### To update the website:

1. **Edit files** in your `southwestsecret.com` folder
2. **Save changes**
3. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Updated [describe change]"
   git push
   ```
4. **Wait 1-2 minutes** for changes to go live

### Common Changes:

**Change YouTube Video:**
Edit `index.html`, find:
```html
<iframe src="https://www.youtube.com/embed/jBQ0gArMvzc"
```
Replace `jBQ0gArMvzc` with your new video ID.

**Change Colors:**
Edit `css/style.css`, find:
```css
:root {
    --primary-color: #ff00ff;
    --secondary-color: #00ffff;
    --accent-color: #ffff00;
}
```
Change the color codes.

**Update Text:**
Edit `index.html` - all the text is clearly labeled with comments.

---

## 🆘 Troubleshooting

### Site not loading?
- Wait 2-3 minutes after enabling Pages
- Check Settings → Pages shows "Your site is published at..."
- Clear your browser cache (Ctrl+F5)

### Changes not showing?
- Did you push to GitHub? (`git push`)
- Wait 1-2 minutes for GitHub to rebuild
- Hard refresh browser (Ctrl+Shift+R)

### Git errors?
```bash
# If remote already exists:
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/southwestsecret.git

# If push fails:
git pull origin main --allow-unrelated-histories
git push -u origin main
```

---

## 📱 Testing Your Site

Before going live, test on:
- Chrome (desktop & mobile)
- Firefox
- Safari (iPhone/iPad)
- Your phone's browser

---

## 🔐 Optional: HTTPS (Security)

GitHub Pages automatically provides HTTPS:
- Check "Enforce HTTPS" in Settings → Pages
- Your site will be `https://` not `http://`

---

## 📊 Optional: Add Analytics

To track visitors, add Google Analytics:

1. Get tracking code from [analytics.google.com](https://analytics.google.com)
2. Add before `</head>` in `index.html`:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=YOUR-ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'YOUR-ID');
</script>
```

---

## 🎯 Quick Command Reference

```bash
# Check status
git status

# Stage all changes
git add .

# Commit with message
git commit -m "Your message"

# Push to GitHub (updates website)
git push

# Pull latest changes
git pull

# View commit history
git log --oneline

# Create new branch
git checkout -b new-feature
```

---

## ✨ What's Next?

1. **Test your site** thoroughly
2. **Share your link** on social media
3. **Promote** your YouTube channel
4. **Update regularly** with new content
5. **Consider custom domain** when ready

---

## 💡 Pro Tips

- **Commit often** - Small, frequent commits are better
- **Descriptive messages** - Write clear commit messages
- **Test locally** - Open `index.html` in browser before pushing
- **Backup** - GitHub is your backup, but download occasionally
- **Mobile first** - Always test on mobile devices

---

## 🆘 Need Help?

- GitHub Pages docs: [pages.github.com](https://pages.github.com)
- Git guide: [git-scm.com/book](https://git-scm.com/book/en/v2)
- YouTube tutorials: Search "GitHub Pages tutorial"

---

**Your website is ready to go live! Just follow the steps above.** 🚀

Made with 💜 by SouthWest Secret

