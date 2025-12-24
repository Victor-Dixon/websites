# Blog & Content Strategy: digitaldreamscape.site

This website uses a **Centralized Content Architecture**.
The blog posts are not stored in this folder. They are generated and managed in the shared `/content` directory.

## 📍 Configuration Pointers

| Component | Location | Description |
| :--- | :--- | :--- |
| **Site Config** | `/workspace/sites/dream.yaml` | The "router" file. Connects brand, voice, and publishing settings. |
| **Brand Rules** | `/workspace/content/brands/dream.yaml` | Defines target audience, offers, and content pillars. |
| **Voice Profile** | `/workspace/content/voices/victor.md` | Defines the writing style and persona. |
| **Topic Backlog** | `/workspace/content/backlogs/dream.yaml` | List of upcoming blog post ideas. |
| **Drafts** | `/workspace/content/drafts/dream/` | The generated Markdown content. |

## 🚀 How to Run the Autoblogger

To generate or publish content for this site, run the following command from the workspace root:

```bash
# Generate/Publish for Digital Dreamscape
python3 tools/blog/unified_blogging_automation.py --site dream
```

## 🎨 Visual Templates

The **visual appearance** of the blog posts is controlled by the WordPress theme located in:
`../wp-content/themes/<active-theme>/`

---

## 📝 Canon Posts (Narrative Foundation)

These posts establish the Digital Dreamscape narrative and are part of the world-building canon:

0. **000-victor-and-the-swarm.md** - The foundation: Victor and the 8-personality Swarm, their symbiotic relationship
1. **001-the-birth-of-digital-dreamscape.md** - The origin story: creating the WHAT_IS document
2. **002-the-trading-domain-emerges.md** - Adding FreeRideInvestor as the Trading Domain
3. **003-introducing-thea.md** - Thea as narrative + coherence authority, authority separation
4. **004-the-severance.md** - Canon event: The arc where Thea was unaddressed, and the reconnection
5. **005-automating-canon.md** - Automating canon extraction from agent work cycles

These posts are **canon events** in the Digital Dreamscape narrative. They tell the story of building the world itself.

---

## 🌍 World-Building Protocol

When adding new domains to Digital Dreamscape:

1. **Define the domain** - What is it? What are its rules?
2. **Write it down** - Create WHAT_IS document
3. **Tell the story** - Blog post that narratively adds it to the world
4. **Close the loop** - Ensure it feeds back into execution

This is how we build the Digital Dreamscape civilization.
