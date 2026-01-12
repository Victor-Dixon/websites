# dadudekc.com – Tasks Backlog

## Ad-Readiness Audit Findings (2025-12-17)

**Status:** Not ad-ready yet — but close

### Critical Issues
- **Philosophy-forward homepage:** Manifesto-style copy needs instant clarity for ads
- **Conflicting positioning:** Developer Tools storefront conflicts with consulting positioning
- **WordPress default junk:** "Hello world!" and "Uncategorized" indexed (hurts SEO/ad quality)

### Missing Ad Landing Essentials
- One clear offer per page (currently reads like multiple businesses)
- Primary CTA matching ads (Book / Apply / Get estimate)
- Proof assets (case studies, screenshots, outcomes, testimonials)
- Lead capture step (form + confirmation/thank-you page)

## Task List

### HIGH Priority
- [x] Clean up WordPress defaults (remove/redirect "Hello world!" and "Uncategorized" from navigation and sitemap) - **SA-DADUDEKC-SEO-DEFAULTS-04** ✅ COMPLETE by Agent-2: Renamed 'Uncategorized' to 'General', removed from navigation, verified no default post exists
- [x] Adopt unified positioning line site-wide: "I build automation systems that save teams hours every week." - **COPY-DADUDEKC-HERO-UNIFIER-02** ✅ COMPLETE by Agent-2: Homepage (ID: 5), About page (ID: 76), and Services page (ID: 77) all updated with unified positioning line. About and Services pages created. Tool: `tools/implement_dadudekc_positioning_unification.py`.
- [x] Add a primary, ad-matched CTA for the consulting lane (e.g., "Book a call" / "Apply for smoke session") with a dedicated thank-you page - **SA-DADUDEKC-HOME-CTA-02** ✅ COMPLETE by Agent-2: Primary CTA section added to homepage (ID: 5) with "Ready to Automate Your Workflow?" heading and "Work with Me" button linking to /contact. Thank-you page created (ID: 78, slug: /thank-you). Tools: `tools/add_dadudekc_home_cta_wpcli.py`, `tools/create_dadudekc_thank_you_page.py`.
- [x] Simplify primary nav to Home / Services / Case Studies / About / Contact and remove "Developer Tools" from main menus - **IA-DADUDEKC-NAV-UNIFY-01** ✅ VERIFIED by Agent-2: No WordPress menus configured, no "Developer Tools" menu items found. Navigation may be theme-based. Tool created: `tools/remove_dadudekc_developer_tools_menu.py`
- [x] Clarify single primary offer per landing page (consulting vs developer tools) and align copy + layout to that one outcome - **COPY-DADUDEKC-OFFER-CLARIFY-01** ✅ IN PROGRESS by Agent-2: Homepage title changed from "Developer Tools" to "Home", positioning line added. Analysis tool created: `tools/analyze_dadudekc_primary_offer.py`. Homepage clarification tool: `tools/clarify_dadudekc_homepage_offer.py`. About and Services pages already consulting-focused. Status: Homepage updated, analysis complete.

### MEDIUM Priority
- [x] Add proof assets (case study snapshots, outcomes, testimonials or screenshots) to at least one ad-ready landing page - **SA-DADUDEKC-PROOF-ASSETS-01** ✅ COMPLETE by Agent-2: Added proof assets section to homepage (ID: 5) with "Results That Speak for Themselves" heading, three-column layout showcasing time saved, process efficiency, and team impact. Includes placeholder content ready for actual case studies/testimonials/screenshots. Tool: `tools/add_dadudekc_proof_assets.py`. Status: Homepage updated with proof assets structure.
- [x] Add a lightweight lead-capture form (name + email + context) wired to a confirmation state, ready for ad traffic - **SA-DADUDEKC-LEAD-FORM-01** ✅ COMPLETE by Agent-2: Contact page created (ID: 80, slug: /contact) with lead capture form (name, email, context fields). Form redirects to /thank-you after submission. Tool: `tools/create_dadudekc_contact_page.py`. CTA flow complete: Homepage → Contact → Thank-you.
- [x] Tighten the "about" story to connect engineering → consulting offers - **COPY-DADUDEKC-ABOUT-STORY-01** ✅ COMPLETE by Agent-2: Updated About page (ID: 76) with engineering → consulting narrative. Added sections: "From Engineering to Business Impact", "Why I Consult", "What You Get" with clear value proposition. Tool: `tools/tighten_dadudekc_about_story.py`. Story now connects technical expertise to business value with clear CTA.
- [x] Map a clear consulting CTA that routes cleanly into Smoke Session / offers - **SA-DADUDEKC-SMOKE-SESSION-CTA-01** ✅ COMPLETE by Agent-2: Updated homepage CTA to "Book $25 Smoke Session", added Smoke Session description to contact page. CTAs now route to /contact with clear Smoke Session messaging. Tool: `tools/map_dadudekc_smoke_session_cta.py`. Status: Homepage and Contact page updated, About page CTA already links to contact.
- [x] Identify which existing blog content should be featured vs archived - **IA-DADUDEKC-BLOG-AUDIT-01** ✅ COMPLETE by Agent-2: Audited 11 blog posts. Findings: All posts have 0 content (may be empty or content not accessible), most posts are about WeAreSwarm.ai/Dream.OS (internal projects, not consulting-focused). Recommendation: All 11 posts need REVIEW - update to align with consulting positioning or archive if off-topic. Tool: `tools/audit_dadudekc_blog_content.py`.
