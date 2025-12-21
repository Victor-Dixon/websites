# tradingrobotplug.com — setup notes

This repo deploys `TradingRobotPlugWeb/` to the `tradingrobotplug.com` WordPress site via the auto-deploy hook.

## Theme

Theme path:

- `TradingRobotPlugWeb/wp-content/themes/tradingrobotplug-theme/`

## WordPress admin steps (once deployed)

- **Activate theme**: Appearance → Themes → “TradingRobotPlug Theme”
- **Create pages**
  - **Thank you page**
    - Pages → Add New → Title: “Thank You”
    - Slug: `thank-you`
    - Template: “Thank You (Waitlist)”
  - **Validation checklist page**
    - Pages → Add New → Title: “Validation Checklist”
    - Slug: `validation-checklist`
    - Template: “Validation Checklist”
- **Homepage**
  - If using a static homepage: Settings → Reading → set “Homepage” to your intended page.
  - The theme’s `front-page.php` is designed to be the landing page.

## Waitlist form behavior

- Form action uses `admin-post.php` with `action=trp_waitlist_signup`
- On submit, the handler saves a private CPT record:
  - Post type: `trp_waitlist_signup`
  - Post meta: `trp_email`, `trp_email_hash`, `trp_ip`, `trp_user_agent`
- After submit, it redirects to the `thank-you` page if present; otherwise to the site root.

