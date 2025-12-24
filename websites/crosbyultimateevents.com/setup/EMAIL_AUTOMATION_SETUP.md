# Email Automation Setup — Crosby Ultimate Events (ConvertKit/Mailchimp)

**Source copy:** `crosbyultimateevents.com/EMAIL_SEQUENCE_WELCOME_NURTURE.md`  
**Trigger:** Lead magnet form submit (Event Planning Checklist)  
**Sequence:** 5 emails over 14 days (Day 0, 3, 7, 10, 14)

---

## 1) Decide platform (recommended)

- **ConvertKit**: simplest automations for lead magnets + tagging + visual rules  
- **Mailchimp**: fine if you already use it; automations are workable but a bit more rigid  

This guide supports both.

---

## 2) Standardize links + placeholders (do this first)

Replace these placeholders in the email copy:

- `link-to-calendly` → `https://calendly.com/<your-handle>/<event-vision-consult>`
- `link-to-corporate` → `https://<your-site>/<corporate-services-slug>`
- `link-to-intimate-dining` → `https://<your-site>/<intimate-dining-slug>`
- `link-to-case-studies` → `https://<your-site>/<case-studies-slug>`
- `link-to-intimate` / `link-to-celebration` / `link-to-premium` → service/pricing page URLs
- `[website]` → `https://crosbyultimateevents.com` (or final canonical URL)
- `[phone number]` / `[Phone]` → official business phone
- `[Your Name]` → owner/operator name (or “Crosby Ultimate Events Team”)

**Personalization fields:**
- ConvertKit: `{{ subscriber.first_name }}` (or your preferred field)
- Mailchimp: `*|FNAME|*`

---

## 3) Create the lead magnet delivery asset

You need a downloadable PDF URL (or email attachment):

- **PDF file name (recommended):** `ultimate-event-planning-checklist.pdf`
- **Source markdown:** `crosbyultimateevents.com/LEAD_MAGNET_EVENT_PLANNING_CHECKLIST.md`

**Delivery options:**
- **Hosted file link** (recommended): upload PDF to your site or your email platform file manager and link it
- **Attachment**: acceptable, but links usually track better and reduce deliverability issues

---

## 4) Build the form (3 fields max)

Use the landing page template:
- `crosbyultimateevents.com/pages/lead-magnet-event-planning-checklist-landing.md`

**Fields:**
- First name (optional but recommended)
- Email (required)
- Event date / timeframe (optional but recommended)

**Tag (recommended):** `leadmagnet_event_checklist`

---

## 5) Create the automation (ConvertKit)

### A. Create a Sequence
Name: `Welcome + Nurture — Event Planning Checklist (5 emails)`

Set delivery schedule:
- Email 1: Day 0 (immediately)
- Email 2: Day 3
- Email 3: Day 7
- Email 4: Day 10
- Email 5: Day 14

Paste each email from `EMAIL_SEQUENCE_WELCOME_NURTURE.md`.

### B. Create an Automation rule
Rule:
- **Trigger:** Joins form “Event Planning Checklist”
- **Action:** Add tag `leadmagnet_event_checklist`
- **Action:** Subscribe to sequence “Welcome + Nurture — Event Planning Checklist (5 emails)”

### C. Remove buyers/booked leads (recommended)
If you have booking confirmation tracking:
- **Trigger:** Tag added `consultation_booked`
- **Action:** Remove from sequence (or move to a “consultation prep” sequence)

If not, add a manual process:
- When a consult is booked, add tag `consultation_booked` in ConvertKit.

---

## 6) Create the automation (Mailchimp)

Mailchimp has multiple ways; the simplest:

### A. Audience
Create/choose an Audience (list).

### B. Tags/Groups
Create tag: `leadmagnet_event_checklist`

### C. Customer Journey (recommended)
Create a Customer Journey:
- **Starting point:** “Tag added: leadmagnet_event_checklist” (or “Signs up via form”)
- **Send Email:** Email 1 (immediate)
- **Delay:** 3 days → send Email 2
- **Delay:** 4 days → send Email 3 (Day 7)
- **Delay:** 3 days → send Email 4 (Day 10)
- **Delay:** 4 days → send Email 5 (Day 14)

### D. Exit rule (optional)
If you can tag booked leads:
- If tag `consultation_booked` → remove from journey

---

## 7) Test plan (minimum)

- Submit form with a test email
- Confirm Thank-you page renders and download link works
- Confirm Email 1 arrives (inbox + mobile)
- Confirm Calendly link works (and has correct time zone)
- Confirm you can unsubscribe successfully

---

## 8) Metrics to track (weekly)

- Landing page views
- Form conversion rate (submits / views)
- Email 1 open + click rate
- Booking clicks (and bookings, if measurable)

Use: `crosbyultimateevents.com/setup/TRACKING_UTM_METRICS.md` (created in next step).

