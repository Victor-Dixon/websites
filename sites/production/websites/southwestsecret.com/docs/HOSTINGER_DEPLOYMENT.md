# 🚀 SouthWest Secret - Hostinger Deployment Guide

## Deploy Your Website to Hostinger Hosting

Since you're using **Hostinger**, here's how to get your website live!

---

## 📋 What You Need

- ✅ Hostinger account with hosting plan
- ✅ Domain: southwestsecret.com (purchased or to be purchased)
- ✅ Your website files (already created in this folder)

---

## 🌐 Option 1: Using Hostinger File Manager (Easiest)

### Step 1: Log into Hostinger

1. Go to [hpanel.hostinger.com](https://hpanel.hostinger.com)
2. Log in with your Hostinger credentials
3. Click on your hosting plan

### Step 2: Access File Manager

1. In your Hostinger panel, find **"File Manager"**
2. Click to open it
3. You'll see your website folders

### Step 3: Navigate to Public Directory

1. Find and open the `public_html` folder
2. This is where your website files go
3. Delete any default files (like `index.html` if present)

### Step 4: Upload Your Files

**Upload these files from `D:\websites\southwestsecret.com\`:**

```
✅ index.html
✅ css/ (entire folder)
✅ js/ (entire folder)
```

**How to Upload:**
1. Click **"Upload Files"** button in File Manager
2. Select all files from your local folder:
   - index.html
   - css folder (drag and drop the whole folder)
   - js folder (drag and drop the whole folder)
3. Wait for upload to complete
4. Your site is LIVE! 🎉

### Step 5: Visit Your Website

Your site will be live at:
- If domain is connected: `https://southwestsecret.com`
- If using Hostinger subdomain: `https://your-site.hostinger-site.com`

---

## 🌐 Option 2: Using FTP (For Advanced Users)

### Step 1: Get FTP Credentials

1. In Hostinger panel, go to **"FTP Accounts"**
2. Note your FTP credentials:
   - **Host**: ftp.southwestsecret.com (or provided by Hostinger)
   - **Username**: Your FTP username
   - **Password**: Your FTP password
   - **Port**: 21

### Step 2: Download FTP Client

Download **FileZilla** (free):
- Go to [filezilla-project.org](https://filezilla-project.org)
- Download and install FileZilla Client

### Step 3: Connect via FTP

1. Open FileZilla
2. Enter your FTP credentials:
   - Host: Your FTP host
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21
3. Click **"Quickconnect"**

### Step 4: Upload Files

1. On the left (Local site): Navigate to `D:\websites\southwestsecret.com\`
2. On the right (Remote site): Navigate to `public_html` folder
3. Select these files/folders on the left:
   - index.html
   - css/ (entire folder)
   - js/ (entire folder)
4. Drag them to the right side (uploads them)
5. Wait for transfer to complete
6. Your site is LIVE! 🎉

---

## 🔗 Connecting Your Domain (southwestsecret.com)

### If You Haven't Bought the Domain Yet:

**Buy from Hostinger (Recommended - Easiest):**
1. In Hostinger panel, go to **"Domains"**
2. Search for `southwestsecret.com`
3. Purchase the domain (~$10-15/year)
4. It will automatically connect to your hosting
5. Done! Your site is at southwestsecret.com

**Buy from Another Registrar:**
1. Buy domain from GoDaddy, Namecheap, etc.
2. Follow "If Domain is Elsewhere" steps below

### If Domain is Already Bought (From Hostinger):

1. In Hostinger panel, go to **"Domains"**
2. Click on `southwestsecret.com`
3. Click **"Manage"** → **"DNS Records"**
4. Make sure it points to your hosting
5. Usually auto-configured - just verify it's correct

### If Domain is Elsewhere (GoDaddy, Namecheap, etc.):

**Get Hostinger Nameservers:**
1. In Hostinger panel, go to **"Domains"** → **"Add Domain"**
2. Note the nameservers (usually):
   - ns1.dns-parking.com
   - ns2.dns-parking.com

**Update Your Domain Registrar:**
1. Log into your domain registrar (GoDaddy, etc.)
2. Find DNS/Nameserver settings
3. Change nameservers to Hostinger's nameservers above
4. Save changes
5. Wait 24-48 hours for DNS propagation

**Alternative - Point A Records:**
1. Get your Hostinger IP address from hPanel
2. In your domain registrar, add A record:
   - Type: A
   - Name: @
   - Value: [Your Hostinger IP]
   - TTL: 3600
3. Add www CNAME:
   - Type: CNAME
   - Name: www
   - Value: southwestsecret.com
   - TTL: 3600

---

## 🔒 Enable SSL (HTTPS) - Important!

### In Hostinger:

1. Go to your hosting panel
2. Find **"SSL"** section
3. Enable **"Free SSL Certificate"** (Let's Encrypt)
4. Click **"Install"** or **"Enable"**
5. Wait 5-10 minutes
6. Your site now has HTTPS: `https://southwestsecret.com` 🔒

---

## 🔄 Updating Your Website

### Method 1: File Manager (Easy)

1. Log into Hostinger File Manager
2. Navigate to `public_html`
3. Click on the file you want to edit (e.g., `index.html`)
4. Click **"Edit"** button
5. Make your changes
6. Click **"Save"**
7. Changes are LIVE immediately!

### Method 2: Re-upload Files

1. Edit files locally on your computer
2. Upload via File Manager or FTP
3. Overwrite existing files
4. Changes are LIVE!

### Method 3: Git Auto-Deploy (Advanced)

If you want auto-deploy from GitHub:

1. Push your code to GitHub (follow original guide)
2. In Hostinger, use **Git integration** feature:
   - Go to **"Advanced"** → **"Git"**
   - Connect your GitHub repository
   - Set up auto-deploy
   - Now pushing to GitHub updates your Hostinger site!

---

## 📁 Folder Structure on Hostinger

Your `public_html` folder should look like:

```
public_html/
├── index.html          ← Your main page
├── css/
│   └── style.css      ← Your styles
└── js/
    └── script.js      ← Your JavaScript
```

**Important Notes:**
- ✅ index.html must be in `public_html` (not in a subfolder)
- ✅ Keep folder names lowercase (css, js)
- ✅ Don't upload .git folder or .gitignore to Hostinger

---

## 🎯 Quick Checklist

Before going live:

- [ ] Hostinger account set up
- [ ] Domain purchased or configured
- [ ] Files uploaded to `public_html`
- [ ] Test website loads: visit your domain
- [ ] SSL certificate enabled (HTTPS)
- [ ] Video plays correctly
- [ ] All links work
- [ ] Test on mobile phone
- [ ] Share your link!

---

## 🆘 Troubleshooting

### Website shows "Coming Soon" or default page?

**Solution:**
- Make sure files are in `public_html` (not in a subfolder)
- Make sure you have `index.html` (not Index.html or index.htm)
- Clear your browser cache (Ctrl+F5)

### Video not playing?

**Solution:**
- Check YouTube embed link is correct
- Make sure you're using the embed URL (not the watch URL)
- Try opening in incognito/private window

### Domain not working?

**Solution:**
- Wait 24-48 hours after changing nameservers
- Check DNS propagation: [whatsmydns.net](https://whatsmydns.net)
- Clear your browser cache
- Try from a different device/network

### SSL certificate not working?

**Solution:**
- Wait 10-15 minutes after installation
- Force HTTPS in Hostinger settings
- Clear browser cache

### File upload failed?

**Solution:**
- Check file size limits (Hostinger usually allows large files)
- Try uploading one folder at a time
- Use FTP if File Manager isn't working
- Contact Hostinger support if needed

---

## 💡 Pro Tips for Hostinger

1. **Use Hostinger's Cache:**
   - Enable LiteSpeed Cache in your panel
   - Makes your site load super fast

2. **Email Accounts:**
   - Create professional email: dj@southwestsecret.com
   - Set up in Hostinger Email section

3. **Backups:**
   - Hostinger auto-backs up your site
   - You can also download backups manually

4. **Analytics:**
   - Add Google Analytics to track visitors
   - Use Hostinger's built-in analytics

5. **Speed Optimization:**
   - Enable Cloudflare in Hostinger (free)
   - Compress images before uploading

---

## 🎵 Your Files Ready to Upload

From: `D:\websites\southwestsecret.com\`

**Upload these to Hostinger:**
```
✅ index.html          (Main page)
✅ css/                (Folder with style.css)
✅ js/                 (Folder with script.js)
```

**Do NOT upload these:**
```
❌ .git/               (Git folder - not needed)
❌ .gitignore          (Git config - not needed)
❌ README.md           (Documentation - keep local)
❌ DEPLOYMENT_GUIDE.md (Documentation - keep local)
```

---

## 📞 Hostinger Support

Need help?
- **24/7 Live Chat**: Available in your hPanel
- **Knowledge Base**: [hostinger.com/tutorials](https://hostinger.com/tutorials)
- **Email Support**: support@hostinger.com

---

## 🚀 Quick Steps Summary

1. **Log into Hostinger** → hpanel.hostinger.com
2. **Open File Manager**
3. **Go to public_html folder**
4. **Upload:** index.html, css folder, js folder
5. **Enable SSL** certificate
6. **Visit your site** at southwestsecret.com
7. **You're LIVE!** 🎉

---

## 🔄 Future Updates

When you want to add new YouTube videos:

1. Edit `index.html` on your computer
2. Find the video section
3. Change the video ID
4. Upload the updated `index.html` to Hostinger
5. Done!

Or edit directly in Hostinger File Manager!

---

**Your website is ready to upload to Hostinger!** 🚀

The design is complete, files are organized, and you just need to upload them.

**Next Steps:**
1. Log into Hostinger
2. Upload the 3 items (index.html, css/, js/)
3. Enable SSL
4. Your site is LIVE!

---

Made with 💜 for SouthWest Secret - Chopped & Screwed DJ

