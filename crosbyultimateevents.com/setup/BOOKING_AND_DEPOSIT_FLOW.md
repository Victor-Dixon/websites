# Booking Calendar + Deposit Payments — Crosby Ultimate Events (Calendly + Stripe)

**Purpose:** end-to-end booking + deposit collection  
**Master task log:** “Implement booking calendar (Calendly) + payment processing (Stripe) for deposits”

---

## Recommended booking model (simple + fast)

### Option A (best for most service businesses): Consultation is free, deposit collected after proposal

- **Step 1:** Lead books a free consultation (Calendly)
- **Step 2:** You send a proposal/quote (manual or CRM)
- **Step 3:** Client pays a deposit (Stripe payment link/invoice)
- **Step 4:** Confirmation email + intake form + calendar reminders

**Pros:** least friction to book; deposit is tied to a real scope/quote  
**Cons:** deposit is not collected up-front

### Option B: Paid booking to reserve date (deposit collected at booking)

- Client picks a time/date in Calendly
- Pays a deposit via Stripe during booking (Calendly payments)

**Pros:** immediate commitment; fewer no-shows  
**Cons:** higher friction; you must be clear about refund/cancellation terms

---

## Calendly setup checklist

- Create event type: **“Event Vision Consultation (30 min)”**
- Availability rules:
  - Buffer before/after: 15 min
  - Minimum scheduling notice: 24h
  - Max events/day: 2–4 (as desired)
- Add **questions** (keep short):
  - Event type (Dinner / Celebration / Corporate / Other)
  - Event date/timeframe
  - Guest count
  - Venue (known/unknown)
- Notifications:
  - Confirmation email to invitee
  - Confirmation email to you
  - Reminder at 24h + 1h

---

## Stripe deposit options (pick one)

### 1) Stripe Payment Link (fastest)

- Create a product like “Event Deposit”
- Price: either fixed ($500) or “customer chooses amount” (if using a range)
- Set success URL to your confirmation page:
  - `https://<your-site>/<deposit-confirmation-slug>`
- Set cancel URL back to pricing/booking:
  - `https://<your-site>/<booking-or-pricing-slug>`

**Best when:** you want simple, no custom code.

### 2) Stripe Invoice (most flexible)

- Create invoice per client with deposit line item + balance due date
- Accept card/ACH
- Auto-send receipt

**Best when:** deposits vary per proposal.

### 3) Calendly Payments (Stripe connected)

- Connect Stripe inside Calendly
- Turn on “Payments” for the event type
- Define amount + refund policy

**Best when:** you want “pay to book” with minimal work.

---

## Embeds (copy/paste snippets)

### Calendly inline widget (generic)

Paste into a WordPress Custom HTML block:

```html
<!-- Calendly inline widget begin -->
<div class="calendly-inline-widget" data-url="https://calendly.com/<your-handle>/<event-vision-consult>" style="min-width:320px;height:780px;"></div>
<script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
<!-- Calendly inline widget end -->
```

### Stripe Payment Link button (generic)

```html
<a class="button" href="https://buy.stripe.com/<your-payment-link-id>" target="_blank" rel="noopener">
  Pay Deposit
</a>
```

---

## Copy blocks to use on pages (recommended)

### Booking page headline
**Book Your Free Event Vision Consultation**

### Deposit page disclaimer (important)
Add your refund/cancellation terms (plain English).

- **Deposit applies to your final balance** (if that’s true)
- **Cancellation policy** (e.g., refundable up to X days)
- **Rescheduling policy**

---

## Operational handoff (minimum)

When a consult is booked:
- Create a lead record (CRM or spreadsheet)
- Send intake form (Google Form is fine)
- After call: send proposal + Stripe invoice/link

When a deposit is paid:
- Mark lead stage: “Booking Confirmed”
- Send confirmation + next steps + timeline checklist

