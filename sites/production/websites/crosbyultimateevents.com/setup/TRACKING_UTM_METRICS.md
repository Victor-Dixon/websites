# Analytics + UTMs + Weekly Metrics — crosbyultimateevents.com

**Master task log:** “Install analytics (GA4, Facebook Pixel) + set up UTM tracking + metrics sheet”

---

## 1) GA4 (Google Analytics 4)

### What to implement
- GA4 property for `crosbyultimateevents.com`
- Install via:
  - Google Tag Manager (recommended), or
  - WordPress plugin (Site Kit / GTM / insert header scripts)

### Events to track (minimum)
- `lead_magnet_submit`
- `contact_form_submit`
- `booking_click`
- `phone_click`

If you can track it:
- `booking_complete`
- `deposit_paid`

---

## 2) Meta (Facebook) Pixel

### What to implement
- Meta Pixel base code site-wide (prefer GTM)
- Standard events (suggested mapping):
  - `Lead` on lead magnet form submit + contact form submit
  - `Schedule` (custom) or `Contact` on booking click/complete
  - `Purchase` on deposit payment success page (if using Stripe success URL)

---

## 3) UTM system (simple + consistent)

Use these conventions everywhere you control links (emails, social, hero CTAs):

Required:
- `utm_source` (where): `instagram`, `facebook`, `linkedin`, `pinterest`, `email`, `website`
- `utm_medium` (how): `social`, `bio`, `post`, `story`, `cpc`, `hero`, `footer`, `newsletter`
- `utm_campaign` (why): `leadmagnet_checklist`, `home_abtest_A`, `holiday_events`, `corporate_q1`

Optional:
- `utm_content` (which creative): `cta_primary`, `cta_secondary`, `video_01`, `carousel_02`

Example (Instagram bio → lead magnet):
`https://<your-site>/<lead-magnet-slug>?utm_source=instagram&utm_medium=bio&utm_campaign=leadmagnet_checklist`

---

## 4) Weekly metrics sheet (copy/paste table)

Create a simple sheet with these columns:

| Week starting | Sessions | Users | Lead magnet views | Lead magnet submits | Contact submits | Booking clicks | Bookings | Deposits paid | Revenue | Notes |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|---|

**Notes examples:**
- “Hero variant B live”
- “Ran IG story 3x”
- “Added new testimonial”

---

## 5) Page mapping (recommended)

- Lead magnet landing page: see `crosbyultimateevents.com/pages/lead-magnet-event-planning-checklist-landing.md`
- Thank-you page: see `crosbyultimateevents.com/pages/lead-magnet-event-planning-checklist-thank-you.md`

**Deposit success page:** create a simple page like:
`/deposit-confirmed/` with “Thank you — we’ve received your deposit” + next steps.

