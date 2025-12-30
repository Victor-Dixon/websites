<!-- Low-Friction Contact/Booking Form - Tier 1 Quick Win WEB-04 -->
<div class="subscription-form low-friction">
  <p class="subscription-intro"><?php esc_html_e('Join the waitlist for early access to our trading robots.', 'theme-textdomain'); ?></p>
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
  <p class="subscription-note"><?php esc_html_e('We'll notify you when we launch and give you priority access.', 'theme-textdomain'); ?></p>
  <div class="premium-upgrade-cta">
    <p><strong><?php esc_html_e('Ready to get started?', 'theme-textdomain'); ?></strong> <?php esc_html_e('Book a free consultation to discuss your needs.', 'theme-textdomain'); ?></p>
    <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="cta-button secondary"><?php esc_html_e('Schedule Consultation', 'theme-textdomain'); ?></a>
  </div>
</div>