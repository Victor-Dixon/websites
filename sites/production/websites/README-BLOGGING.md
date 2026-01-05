# Centralized Blogging Architecture

We use a "Headless Content" approach where the creative content is decoupled from the website code.

## 🧠 The "Creative Brain" (Centralized)
All blog posts, prompts, and editorial strategies live in **`/workspace/content/`**.

*   **`content/brands/`**: Who we are talking to (Audience, Offer).
*   **`content/voices/`**: How we sound (Persona, Tone).
*   **`content/backlogs/`**: What we are writing about (Topics).
*   **`content/drafts/`**: The actual blog post files (Markdown).

## 🔌 The "Router" (Site Config)
Each website has a configuration file in **`/workspace/sites/`** (e.g., `sites/dadudekc.yaml`).
This file bridges the specific website to its Brand, Voice, and Backlog.

## 💻 The "Render Engine" (Website Code)
This directory (`/workspace/websites/`) contains the source code, themes, and plugins.
It is responsible for **displaying** the content, not creating it.

*   **Visual Templates**: `websites/<domain>/wp/wp-content/themes/<theme>/`

## 🔗 Quick Links
To see how a specific site is configured, check its `blog/README.md` if available, or look for its YAML config in `/workspace/sites/`.
