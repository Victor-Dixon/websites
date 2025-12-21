# Hero Headline A/B Test Plan — Crosby Ultimate Events

**Master task log:** “A/B test hero headline for better benefit focus + add urgency”

---

## Current direction (baseline)

Baseline hero goal:
- Make the benefit unmistakable (“stress-free, flawless event”)
- Add a second CTA that feeds the lead magnet funnel
- Add honest urgency (availability-based, not fake countdowns)

---

## Variants (copy blocks)

### Variant A (Benefit + stress relief)
**Headline:** Create an Unforgettable Event — Without the Stress  
**Subhead:** Premium private chef service + meticulous event planning in one seamless experience.  
**Primary CTA:** Book a Free Consultation  
**Secondary CTA:** Get the Free Event Planning Checklist  
**Urgency line:** Limited availability for the next 30–90 days.

### Variant B (Outcome + social proof angle)
**Headline:** Flawless Events That Impress Guests (While You Enjoy the Moment)  
**Subhead:** One trusted partner for culinary artistry + full coordination—no vendor juggling.  
**Primary CTA:** Check Availability  
**Secondary CTA:** Get the Free Checklist  
**Urgency line:** Prime dates book first—secure yours early.

### Variant C (Corporate-friendly positioning)
**Headline:** Executive-Level Events, Seamlessly Delivered  
**Subhead:** Premium private chef + event coordination for client entertainment and celebrations.  
**Primary CTA:** Book a Free Consultation  
**Secondary CTA:** Download the Checklist  
**Urgency line:** Corporate bookings often require 60–90 days lead time.

---

## How to run it (no-code options)

### Option 1: Google Optimize alternative (recommended approach)
Google Optimize is deprecated; instead:
- Use your page builder/theme to create **Hero A** and **Hero B** as two versions of the homepage
- Route traffic using:
  - A/B testing feature in your theme/builder (if available), or
  - A lightweight WordPress A/B plugin, or
  - A simple “week-by-week” test (swap hero copy weekly; track results)

### Option 2: Week-by-week test (fastest)
Run:
- Week 1: Variant A
- Week 2: Variant B
- Week 3: Variant C (optional)

Keep everything else the same.

---

## What to measure (minimum)

Primary success metric:
- **Consultation bookings per 100 homepage visitors**

Secondary metrics:
- Lead magnet form conversion rate
- Clicks on primary CTA vs secondary CTA

---

## UTM conventions

For hero CTAs, add UTMs:
- `utm_source=website`
- `utm_medium=hero`
- `utm_campaign=home_abtest_<variant>`
- `utm_content=primary_cta` or `secondary_cta`

Example:
`https://calendly.com/<your-handle>/<event-vision-consult>?utm_source=website&utm_medium=hero&utm_campaign=home_abtest_A&utm_content=primary_cta`

