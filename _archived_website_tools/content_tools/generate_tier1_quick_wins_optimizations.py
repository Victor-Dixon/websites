#!/usr/bin/env python3
"""
Tier 1 Quick Wins Optimization Generator
Generates optimized hero/CTA and contact/booking forms for 3 sites:
- dadudekc.com
- crosbyultimateevents.com
- tradingrobotplug.com

Tier 1 Quick Wins: WEB-01 (Hero clarity + CTA) and WEB-04 (Contact/booking friction)
"""

import json
import os
from pathlib import Path
from datetime import datetime

# Base directory for websites
WEBSITES_DIR = Path(__file__).parent.parent / "websites"
OUTPUT_DIR = Path(__file__).parent.parent / "websites" / "tier1_quick_wins_output"

# Site configurations based on positioning statements
SITE_CONFIGS = {
    "dadudekc.com": {
        "name": "DaDudeKC",
        "hero_headline": "Get 10+ Hours Per Week Back From Automated Workflows",
        "hero_subheadline": "Done-for-you automation sprints that eliminate workflow bottlenecks in 2 weeks—zero technical knowledge required.",
        "primary_cta_text": "Get Your Free Workflow Audit →",
        "primary_cta_url": "/audit",
        "secondary_cta_text": "See How It Works",
        "secondary_cta_url": "/how-it-works",
        "urgency_text": "Limited spots available—start your automation sprint today",
        "contact_form_intro": "Get started with a free workflow audit. No technical knowledge required.",
        "contact_form_note": "We'll review your workflows and show you exactly what we can automate.",
        "target_audience": "Service business owners ($50K-$500K revenue) drowning in manual workflows",
        "pain_points": "Manual workflows stealing 5-15 hours/week, follow-ups, scheduling, invoicing bottlenecks",
        "desired_outcome": "10+ hours/week back, scalable operations, working automations in 2 weeks",
    },
    "crosbyultimateevents.com": {
        "name": "Crosby Ultimate Events",
        "hero_headline": "Create Unforgettable Events Without the Stress",
        "hero_subheadline": "Premium private chef service + meticulous event planning in one seamless experience. Eliminate vendor juggling—we handle everything.",
        "primary_cta_text": "Book a Free Consultation",
        "primary_cta_url": "/consultation",
        "secondary_cta_text": "Get the Free Event Planning Checklist",
        "secondary_cta_url": "/checklist",
        "urgency_text": "Limited availability for the next 30–90 days—prime dates book first",
        "contact_form_intro": "Start planning your perfect event. Free consultation available.",
        "contact_form_note": "We'll discuss your vision and show you how we bring it to life flawlessly.",
        "target_audience": "Affluent professionals ($150K+ income, ages 35-65) wanting stress-free events",
        "pain_points": "Event planning stress, managing multiple vendors, lack of time, fear of things going wrong",
        "desired_outcome": "Flawless events, ability to enjoy the moment, one trusted partner, peace of mind",
    },
    "tradingrobotplug.com": {
        "name": "Trading Robot Plug",
        "hero_headline": "Join the Waitlist for AI-Powered Trading Robots",
        "hero_subheadline": "We're building and testing trading robots in real-time. Join the waitlist to get early access when we launch—watch our swarm build live.",
        "primary_cta_text": "Join the Waitlist →",
        "primary_cta_url": "/waitlist",
        "secondary_cta_text": "Watch Us Build Live",
        "secondary_cta_url": "#swarm-status",
        "urgency_text": "Limited early access spots—join now to be first in line",
        "contact_form_intro": "Join the waitlist for early access to our trading robots.",
        "contact_form_note": "We'll notify you when we launch and give you priority access.",
        "target_audience": "Traders and investors interested in AI-powered trading automation",
        "pain_points": "Manual trading, emotional decision-making, lack of consistent strategy",
        "desired_outcome": "Automated trading, consistent strategy, early access to tested robots",
    },
}


def generate_hero_html(site_key, config):
    """Generate optimized hero section HTML"""
    return f"""<!-- Optimized Hero Section - Tier 1 Quick Win WEB-01 -->
<section class="hero">
  <h1 id="hero-heading">{config['hero_headline']}</h1>
  <p class="hero-subheadline">{config['hero_subheadline']}</p>
  <div class="hero-cta-row">
    <a class="cta-button primary" href="{config['primary_cta_url']}" role="button">
      {config['primary_cta_text']}
    </a>
    <a class="cta-button secondary" href="{config['secondary_cta_url']}" role="button">
      {config['secondary_cta_text']}
    </a>
  </div>
  <p class="hero-urgency">{config['urgency_text']}</p>
</section>"""


def generate_hero_php(site_key, config):
    """Generate optimized hero section PHP (WordPress-ready)"""
    return f"""<!-- Optimized Hero Section - Tier 1 Quick Win WEB-01 -->
<section class="hero">
  <h1 id="hero-heading"><?php esc_html_e('{config['hero_headline']}', 'theme-textdomain'); ?></h1>
  <p class="hero-subheadline"><?php esc_html_e('{config['hero_subheadline']}', 'theme-textdomain'); ?></p>
  <div class="hero-cta-row">
    <a class="cta-button primary" href="<?php echo esc_url(home_url('{config['primary_cta_url']}')); ?>" role="button">
      <?php esc_html_e('{config['primary_cta_text']}', 'theme-textdomain'); ?>
    </a>
    <a class="cta-button secondary" href="<?php echo esc_url(home_url('{config['secondary_cta_url']}')); ?>" role="button">
      <?php esc_html_e('{config['secondary_cta_text']}', 'theme-textdomain'); ?>
    </a>
  </div>
  <p class="hero-urgency"><?php esc_html_e('{config['urgency_text']}', 'theme-textdomain'); ?></p>
</section>"""


def generate_contact_form_html(site_key, config):
    """Generate low-friction contact/booking form HTML"""
    return f"""<!-- Low-Friction Contact/Booking Form - Tier 1 Quick Win WEB-04 -->
<div class="subscription-form low-friction">
  <p class="subscription-intro">{config['contact_form_intro']}</p>
  <form action="#" method="POST" class="subscription-form-simple" aria-label="Contact Form">
    <input 
      type="email" 
      name="email" 
      class="email-only-input" 
      placeholder="Enter your email address" 
      required
      aria-label="Email address"
    >
    <button type="submit" class="cta-button primary">Get Started</button>
  </form>
  <p class="subscription-note">{config['contact_form_note']}</p>
  <div class="premium-upgrade-cta">
    <p><strong>Ready to get started?</strong> Book a free consultation to discuss your needs.</p>
    <a href="/consultation" class="cta-button secondary">Schedule Consultation</a>
  </div>
</div>"""


def generate_contact_form_php(site_key, config):
    """Generate low-friction contact/booking form PHP (WordPress-ready)"""
    return f"""<!-- Low-Friction Contact/Booking Form - Tier 1 Quick Win WEB-04 -->
<div class="subscription-form low-friction">
  <p class="subscription-intro"><?php esc_html_e('{config['contact_form_intro']}', 'theme-textdomain'); ?></p>
  <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="subscription-form-simple" aria-label="Contact Form">
    <?php wp_nonce_field('contact_form', 'contact_nonce'); ?>
    <input type="hidden" name="action" value="handle_contact_form">
    <input 
      type="email" 
      name="email" 
      class="email-only-input" 
      placeholder="<?php esc_attr_e('Enter your email address', 'theme-textdomain'); ?>" 
      required
      aria-label="<?php esc_attr_e('Email address', 'theme-textdomain'); ?>"
    >
    <button type="submit" class="cta-button primary"><?php esc_html_e('Get Started', 'theme-textdomain'); ?></button>
  </form>
  <p class="subscription-note"><?php esc_html_e('{config['contact_form_note']}', 'theme-textdomain'); ?></p>
  <div class="premium-upgrade-cta">
    <p><strong><?php esc_html_e('Ready to get started?', 'theme-textdomain'); ?></strong> <?php esc_html_e('Book a free consultation to discuss your needs.', 'theme-textdomain'); ?></p>
    <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="cta-button secondary"><?php esc_html_e('Schedule Consultation', 'theme-textdomain'); ?></a>
  </div>
</div>"""


def generate_hero_css(site_key, config):
    """Generate hero optimization CSS (based on freerideinvestor.com pattern)"""
    # Get site-specific color (you can customize per site)
    primary_color = "#0066ff"  # Default, can be customized per site
    if site_key == "dadudekc.com":
        primary_color = "#0066ff"  # Can customize
    elif site_key == "crosbyultimateevents.com":
        primary_color = "#d4af37"  # Gold for premium feel
    elif site_key == "tradingrobotplug.com":
        primary_color = "#00ff88"  # Tech green
    
    return f"""/*--------------------------------------------------------------
  Hero Optimization - Tier 1 Quick Win WEB-01
  Site: {site_key}
  Phase 1 P0 Fix - Conversion Optimization
--------------------------------------------------------------*/

.hero-cta-row {{
  display: flex;
  gap: var(--spacing-md, 20px);
  justify-content: center;
  flex-wrap: wrap;
  margin: var(--spacing-lg, 30px) 0;
}}

.hero-cta-row .cta-button.primary {{
  background: {primary_color};
  color: #fff;
  padding: 18px 36px;
  font-size: 1.2rem;
  font-weight: 700;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 102, 255, 0.3);
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-block;
}}

.hero-cta-row .cta-button.primary:hover {{
  background: {primary_color};
  opacity: 0.9;
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(0, 102, 255, 0.4);
}}

.hero-cta-row .cta-button.secondary {{
  background: transparent;
  color: var(--text-color, #333);
  border: 2px solid {primary_color};
  padding: 18px 36px;
  font-size: 1.2rem;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-block;
}}

.hero-cta-row .cta-button.secondary:hover {{
  background: rgba(0, 102, 255, 0.1);
  border-color: {primary_color};
}}

.hero-subheadline {{
  font-size: 1.3rem;
  line-height: 1.6;
  color: var(--text-muted, #666);
  margin-bottom: var(--spacing-md, 20px);
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}}

.hero-urgency {{
  font-size: 0.95rem;
  color: var(--accent-color, #ffb300);
  font-style: italic;
  margin-top: var(--spacing-sm, 15px);
  text-align: center;
}}

/* Low Friction Form Styles - WEB-04 Quick Win */
.subscription-form.low-friction {{
  max-width: 600px;
  margin: 0 auto;
}}

.subscription-form-simple {{
  display: flex;
  gap: var(--spacing-sm, 15px);
  margin-bottom: var(--spacing-sm, 15px);
}}

.subscription-form-simple .email-only-input {{
  flex: 1;
  padding: 15px;
  font-size: 1rem;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 4px;
  background: #fff;
  color: var(--text-color, #333);
}}

.subscription-form-simple .email-only-input:focus {{
  outline: none;
  border-color: {primary_color};
  box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
}}

.subscription-intro {{
  text-align: center;
  color: var(--text-muted, #666);
  margin-bottom: var(--spacing-md, 20px);
}}

.subscription-note {{
  text-align: center;
  font-size: 0.9rem;
  color: var(--text-muted, #666);
  margin-top: var(--spacing-xs, 10px);
}}

.premium-upgrade-cta {{
  margin-top: var(--spacing-lg, 30px);
  padding: var(--spacing-md, 20px);
  background: rgba(0, 102, 255, 0.1);
  border: 1px solid rgba(0, 102, 255, 0.2);
  border-radius: 8px;
  text-align: center;
}}

.premium-upgrade-cta p {{
  margin-bottom: var(--spacing-sm, 15px);
  color: var(--text-color, #333);
}}

/* Responsive */
@media (max-width: 768px) {{
  .hero-cta-row {{
    flex-direction: column;
  }}
  
  .hero-cta-row .cta-button {{
    width: 100%;
  }}
  
  .subscription-form-simple {{
    flex-direction: column;
  }}
  
  .subscription-form-simple .email-only-input {{
    width: 100%;
  }}
}}"""


def generate_deployment_readme(site_key, config):
    """Generate deployment instructions"""
    return f"""# Tier 1 Quick Wins - Deployment Instructions
## {config['name']} ({site_key})

**Generated:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
**Fixes:** WEB-01 (Hero clarity + CTA), WEB-04 (Contact/booking friction)

---

## Files Generated

1. **hero-section.html** - Optimized hero HTML (use directly or convert to PHP)
2. **hero-section.php** - WordPress-ready hero PHP template
3. **contact-form.html** - Low-friction contact form HTML
4. **contact-form.php** - WordPress-ready contact form PHP
5. **hero-optimization.css** - Hero and form styling

---

## Deployment Steps

### 1. Hero Section (WEB-01)

**Option A: Direct HTML Integration**
- Copy content from `hero-section.html`
- Replace existing hero section in your homepage template
- Ensure CSS from `hero-optimization.css` is included

**Option B: WordPress PHP Integration**
- Copy content from `hero-section.php`
- Replace hero section in `page-front-page.php` or your homepage template
- Update textdomain if needed (currently 'theme-textdomain')
- Update CTA URLs to match your site structure

### 2. Contact/Booking Form (WEB-04)

**Option A: Direct HTML Integration**
- Copy content from `contact-form.html`
- Replace existing contact form
- Update form action URL to your form handler endpoint
- Ensure CSS from `hero-optimization.css` is included

**Option B: WordPress PHP Integration**
- Copy content from `contact-form.php`
- Add form handler in `functions.php` (see below)
- Replace contact form in your contact page
- Update URLs to match your site structure

**WordPress Form Handler (add to functions.php):**
```php
add_action('admin_post_handle_contact_form', 'handle_contact_form_submission');
add_action('admin_post_nopriv_handle_contact_form', 'handle_contact_form_submission');

function handle_contact_form_submission() {{
    // Verify nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'contact_form')) {{
        wp_die('Security check failed');
    }}
    
    $email = sanitize_email($_POST['email']);
    
    // Process email (add to mailing list, send notification, etc.)
    // Example: wp_mail($admin_email, 'New Contact', 'Email: ' . $email);
    
    // Redirect to thank you page
    wp_redirect(home_url('/thank-you'));
    exit;
}}
```

### 3. CSS Integration

**Option A: Add to existing stylesheet**
- Copy CSS from `hero-optimization.css`
- Add to your theme's main stylesheet
- Customize colors if needed (primary_color variable)

**Option B: Create separate stylesheet**
- Upload `hero-optimization.css` to your theme's CSS directory
- Enqueue in `functions.php`:
```php
wp_enqueue_style('hero-optimization', get_template_directory_uri() . '/css/hero-optimization.css', array(), '1.0.0');
```

---

## Customization Notes

### Colors
- Primary CTA color: Currently set to site-specific color
- Update `--primary-color` variable in CSS for global changes
- Secondary button uses border with primary color

### CTA URLs
- Update `primary_cta_url` and `secondary_cta_url` in generated PHP files
- Ensure URLs match your site structure
- For WordPress, use `home_url()` for relative URLs

### Form Processing
- HTML forms need backend handler (PHP/Node.js/etc.)
- WordPress forms use `admin-post.php` action hooks
- Consider integration with email marketing platform (MailChimp, ConvertKit, etc.)

---

## Testing Checklist

- [ ] Hero headline displays correctly
- [ ] Subheadline is readable and compelling
- [ ] Primary CTA button is prominent and clickable
- [ ] Secondary CTA button is visible and styled correctly
- [ ] Urgency text displays below CTAs
- [ ] Contact form accepts email input
- [ ] Form submits successfully
- [ ] Mobile responsive (test on phone/tablet)
- [ ] All links work correctly
- [ ] Colors match brand guidelines

---

## Expected Impact

**WEB-01 (Hero clarity + CTA):**
- Clearer value proposition
- Dual CTAs increase conversion options
- Urgency text creates action motivation

**WEB-04 (Contact/booking friction):**
- Reduced form fields (email-only) decreases abandonment
- Premium upgrade CTA provides upsell opportunity
- Low-friction design increases submissions

---

## Support

For questions or issues, refer to:
- Framework: `docs/website_audits/2026/STRATEGIC_P0_PRIORITIZATION_FRAMEWORK_2025-12-25.md`
- Tracking: `docs/website_audits/2026/P0_FIX_TRACKING.md`
- Reference implementation: `freerideinvestor.com` (WEB-01 + WEB-04 complete)
"""


def main():
    """Generate optimization files for all 3 sites"""
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    
    for site_key, config in SITE_CONFIGS.items():
        site_dir = OUTPUT_DIR / site_key
        site_dir.mkdir(parents=True, exist_ok=True)
        
        print(f"Generating optimizations for {site_key}...")
        
        # Generate hero sections
        hero_html = generate_hero_html(site_key, config)
        hero_php = generate_hero_php(site_key, config)
        
        # Generate contact forms
        contact_html = generate_contact_form_html(site_key, config)
        contact_php = generate_contact_form_php(site_key, config)
        
        # Generate CSS
        hero_css = generate_hero_css(site_key, config)
        
        # Generate deployment instructions
        readme = generate_deployment_readme(site_key, config)
        
        # Write files
        (site_dir / "hero-section.html").write_text(hero_html, encoding="utf-8")
        (site_dir / "hero-section.php").write_text(hero_php, encoding="utf-8")
        (site_dir / "contact-form.html").write_text(contact_html, encoding="utf-8")
        (site_dir / "contact-form.php").write_text(contact_php, encoding="utf-8")
        (site_dir / "hero-optimization.css").write_text(hero_css, encoding="utf-8")
        (site_dir / "DEPLOYMENT_INSTRUCTIONS.md").write_text(readme, encoding="utf-8")
        
        # Generate summary JSON
        summary = {
            "site": site_key,
            "name": config["name"],
            "generated": datetime.now().isoformat(),
            "fixes": ["WEB-01", "WEB-04"],
            "files": {
                "hero_html": "hero-section.html",
                "hero_php": "hero-section.php",
                "contact_html": "contact-form.html",
                "contact_php": "contact-form.php",
                "css": "hero-optimization.css",
                "instructions": "DEPLOYMENT_INSTRUCTIONS.md",
            },
            "config": config,
        }
        (site_dir / "optimization_summary.json").write_text(
            json.dumps(summary, indent=2), encoding="utf-8"
        )
        
        print(f"  ✅ Generated 6 files for {site_key}")
    
    print(f"\n✅ Generated optimizations for all 3 sites")
    print(f"📁 Output directory: {OUTPUT_DIR}")
    print(f"\nNext steps:")
    print(f"  1. Review generated files in {OUTPUT_DIR}")
    print(f"  2. Deploy hero sections (WEB-01)")
    print(f"  3. Deploy contact forms (WEB-04)")
    print(f"  4. Update P0_FIX_TRACKING.md with deployment status")


if __name__ == "__main__":
    main()

