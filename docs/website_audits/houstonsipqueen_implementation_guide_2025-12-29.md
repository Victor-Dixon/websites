# Houston Sip Queen - Week 1 Implementation Guide

**Date:** 2025-12-29  
**Status:** Implementation Complete - Configuration Required  
**Priority:** P0 - Critical

---

## ✅ Implementation Complete

All Week 1 priorities have been implemented:

1. ✅ **Lead Magnet System** - Landing page and thank-you page templates created
2. ✅ **Email Automation Integration** - Mailchimp/ConvertKit integration code added
3. ✅ **Calendly Integration** - Booking calendar functions added
4. ✅ **Stripe Payment Processing** - Payment form and processing code added
5. ✅ **Homepage Improvements** - New front-page.php with hero, positioning, ICP messaging
6. ✅ **PDF Generator Script** - Python script to generate lead magnet PDF

---

## 📋 Configuration Required

### 1. Email Service Configuration

**Option A: Mailchimp**
```php
// Add to WordPress admin or wp-config.php:
update_option('hsq_email_service', 'mailchimp');
update_option('hsq_email_api_key', 'YOUR_MAILCHIMP_API_KEY');
update_option('hsq_email_list_id', 'YOUR_MAILCHIMP_LIST_ID');
```

**Option B: ConvertKit**
```php
update_option('hsq_email_service', 'convertkit');
update_option('hsq_email_api_key', 'YOUR_CONVERTKIT_API_KEY');
update_option('hsq_email_list_id', 'YOUR_CONVERTKIT_FORM_ID');
```

**How to Get API Keys:**
- **Mailchimp:** Account → Extras → API keys → Create a key
- **ConvertKit:** Settings → Advanced → API Secret

### 2. Calendly Configuration

```php
// Add to WordPress admin:
update_option('hsq_calendly_url', 'https://calendly.com/your-username/consultation');
```

**How to Get Calendly URL:**
1. Create account at calendly.com
2. Create event type (e.g., "30-minute consultation")
3. Copy the event URL

### 3. Stripe Configuration

```php
// Add to WordPress admin (use test keys first):
update_option('hsq_stripe_publishable_key', 'pk_test_...');
update_option('hsq_stripe_secret_key', 'sk_test_...');
```

**How to Get Stripe Keys:**
1. Create account at stripe.com
2. Go to Developers → API keys
3. Copy Publishable key and Secret key
4. Use test keys for development, live keys for production

### 4. Generate Lead Magnet PDF

```bash
# Install reportlab if needed:
pip install reportlab

# Run the generator:
cd D:\websites
python tools/generate_hsq_lead_magnet_pdf.py
```

The PDF will be generated at:
`websites/houstonsipqueen.com/wp/wp-content/uploads/event-bar-planning-checklist.pdf`

### 5. Create WordPress Pages

The pages will be auto-created when the theme is activated, but you can also create them manually:

1. **Event Planning Guide Landing Page**
   - Slug: `event-planning-guide`
   - Template: `page-event-planning-guide.php`
   - URL: `/event-planning-guide`

2. **Thank-You Page**
   - Slug: `thank-you`
   - Parent: Event Planning Guide
   - Template: `page-thank-you-guide.php`
   - URL: `/event-planning-guide/thank-you`

3. **Booking Page** (for Calendly)
   - Slug: `book`
   - Add Calendly widget using: `<?php houstonsipqueen_add_calendly_widget(); ?>`

---

## 🎨 Styling

All new sections have been styled in `style.css`:
- Hero section with gradient background
- Positioning section with outcomes grid
- Offer ladder with tier cards
- Services grid
- Testimonials section
- CTA section with gradient
- Lead magnet form styles
- Thank-you page styles
- Calendly and Stripe form styles

---

## 📝 Usage Examples

### Add Calendly Button to Any Page

```php
<?php houstonsipqueen_calendly_button('Book a Consultation'); ?>
```

### Add Calendly Inline Widget

```php
<?php houstonsipqueen_add_calendly_widget(); ?>
```

### Add Stripe Payment Form

```php
<?php 
houstonsipqueen_stripe_payment_form(
    $amount = 500, // $5.00 deposit
    $description = 'Event Deposit',
    $success_url = home_url('/booking-confirmation'),
    $cancel_url = home_url('/quote')
); 
?>
```

---

## 🔧 Testing Checklist

- [ ] Test lead magnet form submission
- [ ] Verify email delivery with download link
- [ ] Test email service integration (add subscriber)
- [ ] Test Calendly button/widget functionality
- [ ] Test Stripe payment form (use test mode)
- [ ] Verify PDF download link works
- [ ] Test homepage on mobile devices
- [ ] Verify all CTAs link correctly
- [ ] Test thank-you page redirect
- [ ] Verify analytics tracking (if configured)

---

## 🚀 Next Steps

1. **Configure all services** (email, Calendly, Stripe)
2. **Generate PDF** using the Python script
3. **Create WordPress pages** (or verify auto-creation)
4. **Test all functionality** using the checklist above
5. **Deploy to production** after testing
6. **Monitor conversions** and iterate

---

## 📁 Files Created/Modified

### New Files:
- `page-event-planning-guide.php` - Lead magnet landing page
- `page-thank-you-guide.php` - Thank-you page
- `front-page.php` - Improved homepage
- `tools/generate_hsq_lead_magnet_pdf.py` - PDF generator script

### Modified Files:
- `functions.php` - Added lead magnet handler, email integration, Calendly, Stripe
- `style.css` - Added styles for all new sections

---

## ⚠️ Important Notes

1. **Stripe Payment Processing:**
   - Currently uses Stripe API directly via wp_remote_post
   - For production, consider using Stripe PHP SDK: `composer require stripe/stripe-php`
   - Always use HTTPS in production
   - Store secret keys securely (never commit to git)

2. **Email Service:**
   - Mailchimp requires datacenter extraction from API key
   - ConvertKit uses form IDs, not list IDs
   - Test with a real email address before going live

3. **PDF Generation:**
   - Requires `reportlab` Python library
   - PDF will be generated in WordPress uploads directory
   - Ensure uploads directory is writable

4. **Security:**
   - All forms use nonces for CSRF protection
   - Honeypot fields for spam protection
   - Input sanitization and validation
   - AJAX endpoints use nonce verification

---

## 📞 Support

If you encounter issues:
1. Check WordPress error logs
2. Verify all API keys are correct
3. Test in staging environment first
4. Check browser console for JavaScript errors
5. Verify file permissions for uploads directory

---

*Implementation guide created by Agent-2*  
*Date: 2025-12-29*  
*All Week 1 priorities implemented - Configuration required before going live*

