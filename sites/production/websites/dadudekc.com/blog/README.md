# Blog & Content Strategy: dadudekc.com

This website uses a **Centralized Content Architecture**.
The blog posts are not stored in this folder. They are generated and managed in the shared `/content` directory.

## 📍 Configuration Pointers

| Component | Location | Description |
| :--- | :--- | :--- |
| **Site Config** | `/workspace/sites/dadudekc.yaml` | The "router" file. Connects brand, voice, and publishing settings. |
| **Brand Rules** | `/workspace/content/brands/dadudekc.yaml` | Defines target audience, offers, and content pillars. |
| **Voice Profile** | `/workspace/content/voices/victor.md` | Defines the writing style and persona. |
| **Topic Backlog** | `/workspace/content/backlogs/dadudekc.yaml` | List of upcoming blog post ideas. |
| **Drafts** | `/workspace/content/drafts/dadudekc/` | The generated Markdown content. |

## 🚀 How to Run the Autoblogger

To generate or publish content for this site, run the following command from the workspace root:

```bash
# Generate/Publish for Dadudekc
python3 tools/blog/unified_blogging_automation.py --site dadudekc
```

## 🎨 Visual Templates

The **visual appearance** of the blog posts is controlled by the WordPress theme located in:
`../wp-content/themes/<active-theme>/`
