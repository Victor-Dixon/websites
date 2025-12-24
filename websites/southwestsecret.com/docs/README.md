# 🎵 SouthWest Secret - Chopped & Screwed DJ

Welcome to the official website for **SouthWest Secret**, your source for authentic chopped and screwed music!

![SouthWest Secret](https://img.shields.io/badge/Genre-Chopped%20%26%20Screwed-purple)
![Status](https://img.shields.io/badge/Status-Live-brightgreen)
![YouTube](https://img.shields.io/badge/YouTube-Subscribe-red)

## 🌟 About

SouthWest Secret is dedicated to keeping the Houston chopped and screwed tradition alive. Each mix is carefully crafted with that signature screwed tempo, creating a hypnotic and laid-back vibe perfect for cruising or just vibing out.

## 🚀 Features

- **Graffiti & Bubble Letter Design** - Authentic street art aesthetic
- **Responsive Design** - Looks great on all devices
- **YouTube Integration** - Embedded videos and direct channel links
- **Smooth Animations** - Modern, engaging user experience
- **Dark Theme** - Easy on the eyes with vibrant accents

## 📦 Project Structure

```
southwestsecret.com/
├── index.html          # Main HTML file
├── css/
│   └── style.css       # Styles with graffiti fonts
├── js/
│   └── script.js       # Interactive features
├── .gitignore          # Git ignore file
└── README.md           # This file
```

## 🛠️ Tech Stack

- **HTML5** - Semantic markup
- **CSS3** - Modern styling with gradients, animations
- **JavaScript** - Smooth scrolling, animations, easter eggs
- **Google Fonts** - Rubik Doodle Shadow, Rubik Bubbles, Permanent Marker

## 🚀 Deployment Instructions

### Option 1: GitHub Pages (Recommended)

1. **Create a GitHub repository:**
   ```bash
   cd southwestsecret.com
   git init
   git add .
   git commit -m "Initial commit: SouthWest Secret website"
   ```

2. **Push to GitHub:**
   ```bash
   # Create a new repository on GitHub (https://github.com/new)
   # Then run these commands:
   git remote add origin https://github.com/YOUR_USERNAME/southwestsecret.git
   git branch -M main
   git push -u origin main
   ```

3. **Enable GitHub Pages:**
   - Go to your repository on GitHub
   - Click "Settings" → "Pages"
   - Under "Source", select "main" branch
   - Click "Save"
   - Your site will be live at: `https://YOUR_USERNAME.github.io/southwestsecret/`

4. **Auto-Deploy:**
   - Any push to the `main` branch will automatically update your website!

### Option 2: Netlify (Alternative)

1. **Deploy to Netlify:**
   - Go to [netlify.com](https://www.netlify.com/)
   - Drag and drop the `southwestsecret.com` folder
   - Your site goes live instantly!

2. **Connect to GitHub:**
   - Link your GitHub repository
   - Netlify will auto-deploy on every push

### Option 3: Custom Domain

To use **southwestsecret.com**:

1. **Purchase the domain** from a registrar (GoDaddy, Namecheap, etc.)

2. **Configure DNS:**
   - Add an A record pointing to GitHub Pages IPs:
     - `185.199.108.153`
     - `185.199.109.153`
     - `185.199.110.153`
     - `185.199.111.153`

3. **Update GitHub Pages settings:**
   - Go to Settings → Pages
   - Enter your custom domain: `southwestsecret.com`
   - Enable "Enforce HTTPS"

## 📝 Customization

### Change Colors
Edit `css/style.css` and modify the CSS variables:
```css
:root {
    --primary-color: #ff00ff;      /* Main purple/pink */
    --secondary-color: #00ffff;    /* Cyan */
    --accent-color: #ffff00;       /* Yellow */
}
```

### Add More Videos
In `index.html`, duplicate the video section:
```html
<div class="video-container">
    <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID"></iframe>
</div>
```

### Update YouTube Channel Link
Replace `@SouthWestSecret` with your actual YouTube handle in `index.html`.

## 🎨 Design Features

- **Graffiti Title** - Uses Rubik Doodle Shadow font with gradient effects
- **Bubble Letters** - Uses Rubik Bubbles font with glow effects
- **Animated Background** - Twinkling stars effect
- **Gradient Animations** - Smooth color transitions
- **Parallax Scrolling** - Hero section moves with scroll
- **Responsive Navigation** - Works on all screen sizes

## 🐛 Known Features (Easter Eggs)

- Try the Konami Code: ↑↑↓↓←→←→BA
- Check the browser console for hidden messages
- Hover effects on all interactive elements

## 📱 Browser Support

- ✅ Chrome (Latest)
- ✅ Firefox (Latest)
- ✅ Safari (Latest)
- ✅ Edge (Latest)
- ✅ Mobile browsers

## 🤝 Contributing

Want to improve the site? Feel free to:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is open source. Feel free to use and modify as needed!

## 🔗 Links

- **YouTube Channel**: [SouthWest Secret](https://www.youtube.com/@SouthWestSecret)
- **Website**: Coming soon to southwestsecret.com

## 💬 Contact

For bookings, collaborations, or just to say what's up:
- YouTube: [@SouthWestSecret](https://www.youtube.com/@SouthWestSecret)

---

**Made with 💜 by SouthWest Secret**

*Keeping the Screwed Sound Alive* 🎵

## 🚀 Quick Start Commands

```bash
# Initialize Git
git init

# Add all files
git add .

# Commit changes
git commit -m "Initial commit"

# Add remote (replace with your GitHub URL)
git remote add origin https://github.com/YOUR_USERNAME/southwestsecret.git

# Push to GitHub
git push -u origin main
```

After pushing to GitHub, enable GitHub Pages in your repository settings, and your site will be live! 🎉

