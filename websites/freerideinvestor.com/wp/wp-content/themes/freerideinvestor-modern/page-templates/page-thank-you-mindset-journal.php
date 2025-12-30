<?php
/**
 * Template Name: Thank You - Mindset Journal Download
 * Description: Thank you page after mindset journal download
 * Phase 1 P0 Fix - FUN-01
 */

get_header();
?>

<section class="thank-you-page">
  <div class="container">
    <div class="thank-you-content">
      <h1><?php esc_html_e('✅ Check Your Email!', 'simplifiedtradingtheme'); ?></h1>
      <p class="success-message">
        <?php esc_html_e('Your Free Trading Mindset Journal is on its way to your inbox.', 'simplifiedtradingtheme'); ?>
      </p>
      
      <div class="download-section">
        <h2><?php esc_html_e('What\'s Next?', 'simplifiedtradingtheme'); ?></h2>
        <div class="next-steps">
          <div class="step">
            <strong><?php esc_html_e('1. Check your email', 'simplifiedtradingtheme'); ?></strong>
            <p><?php esc_html_e('Download the mindset journal PDF from the email we just sent.', 'simplifiedtradingtheme'); ?></p>
          </div>
          <div class="step">
            <strong><?php esc_html_e('2. Start journaling daily', 'simplifiedtradingtheme'); ?></strong>
            <p><?php esc_html_e('Use the journal exercises to build emotional awareness and trading discipline.', 'simplifiedtradingtheme'); ?></p>
          </div>
          <div class="step">
            <strong><?php esc_html_e('3. Track your progress', 'simplifiedtradingtheme'); ?></strong>
            <p><?php esc_html_e('Review your mindset metrics weekly to see your growth.', 'simplifiedtradingtheme'); ?></p>
          </div>
        </div>
      </div>

      <div class="cta-section">
        <h3><?php esc_html_e('Want More Trading Resources?', 'simplifiedtradingtheme'); ?></h3>
        <p><?php esc_html_e('Get our free Trading Roadmap to build your complete trading system.', 'simplifiedtradingtheme'); ?></p>
        <a href="<?php echo esc_url(home_url('/roadmap')); ?>" class="cta-button">
          <?php esc_html_e('Get Free Roadmap →', 'simplifiedtradingtheme'); ?>
        </a>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>

