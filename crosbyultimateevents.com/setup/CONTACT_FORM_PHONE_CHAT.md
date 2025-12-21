# Reduce Contact Form Friction + Add Phone + Chat — Crosby Ultimate Events

**Master task log:** “Reduce contact form friction (3 fields) + add phone + chat widget”

---

## 1) Contact form (3 fields max)

**Recommended fields (in order):**
1. **Name** (required)
2. **Email** (required)
3. **Event date** (optional OR required depending on lead quality needs)

**Optional (only if you can keep it 3 fields):**
- Replace “Event date” with “Event timeframe” (dropdown: ASAP / 30 days / 60 days / 90+ days)

**Microcopy (above form):**
“Tell us a little about your event. We’ll reply within 1 business day.”

**Button text (choose one):**
- “Get a Quote”
- “Check Availability”
- “Request a Callback”

**Privacy line (suggested):**
“We respect your inbox. No spam—unsubscribe anytime.”

---

## 2) Add phone number prominently (header + footer)

**Placement:**
- Header: clickable `tel:` link (mobile-first)
- Footer: phone + email + service area

**Suggested label copy:**
- “Call/Text: [PHONE]”
- “Questions? Call [PHONE]”

---

## 3) Chat widget (recommended: Tawk.to)

**Why Tawk.to:** free, fast, widely used, easy WP plugin or embed.

### Embed method (generic)

Paste the provider’s snippet into:
- WordPress: theme header/footer injection or a plugin like “Insert Headers and Footers”

### Suggested chat greeting
“Hi! Planning an event? Tell us your date + guest count and we’ll help.”

### Working hours
Set to your actual business hours and enable “leave a message” after-hours.

---

## 4) Call routing / lead capture (minimum ops)

If a lead calls or chats:
- Log in a simple sheet or CRM with:
  - Name
  - Email/phone
  - Event date
  - Guest count (if known)
  - Lead source (landing page, IG, referral, etc.)

---

## 5) Conversion tracking

Track:
- `contact_form_submit`
- `phone_click` (header `tel:` link clicks)
- `chat_open` / `chat_submit` (if supported by widget)

