# Blog & Content Strategy: houstonsipqueen.com

This website uses a **Centralized Content Architecture**.
The blog posts are not stored in this folder. They are generated and managed in the shared `/content` directory.

## 📍 Configuration Pointers

| Component | Location | Description |
| :--- | :--- | :--- |
| **Site Config** | `/workspace/sites/kiki.yaml` | The "router" file. Connects brand, voice, and publishing settings. |
| **Brand Rules** | `/workspace/content/brands/kiki_site.yaml` | Defines target audience, offers, and content pillars. |
| **Voice Profile** | `/workspace/content/voices/kiki.md` | Defines the writing style and persona. |
| **Topic Backlog** | `/workspace/content/backlogs/kiki_site.yaml` | List of upcoming blog post ideas. |
| **Drafts** | `/workspace/content/drafts/kiki/` | The generated Markdown content. |

## 🚀 How to Run the Autoblogger

To generate or publish content for this site, run the following command from the workspace root:

```bash
# Generate/Publish for Houston Sip Queen
python3 tools/blog/unified_blogging_automation.py --site kiki
```

## 🎨 Visual Templates

The **visual appearance** of the blog posts is controlled by the WordPress theme located in:
`../wp-content/themes/<active-theme>/`
