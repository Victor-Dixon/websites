<?php
/**
 * Template Name: Thank You - Roadmap Download
 * Description: Thank you page after roadmap download
 * Phase 1 P0 Fix - FUN-01
 */

get_header();
?>

<section class="thank-you-page">
  <div class="container">
    <div class="thank-you-content">
      <h1><?php esc_html_e('✅ Check Your Email!', 'simplifiedtradingtheme'); ?></h1>
      <p class="success-message">
        <?php esc_html_e('Your Free Trading Roadmap is on its way to your inbox.', 'simplifiedtradingtheme'); ?>
      </p>
      
      <div class="download-section">
        <h2><?php esc_html_e('What\'s Next?', 'simplifiedtradingtheme'); ?></h2>
        <div class="next-steps">
          <div class="step">
            <strong><?php esc_html_e('1. Check your email', 'simplifiedtradingtheme'); ?></strong>
            <p><?php esc_html_e('Download the roadmap PDF from the email we just sent.', 'simplifiedtradingtheme'); ?></p>
          </div>
          <div class="step">
            <strong><?php esc_html_e('2. Start implementing', 'simplifiedtradingtheme'); ?></strong>
            <p><?php esc_html_e('Follow the roadmap step-by-step to build your trading system.', 'simplifiedtradingtheme'); ?></p>
          </div>
          <div class="step">
            <strong><?php esc_html_e('3. Join our community', 'simplifiedtradingtheme'); ?></strong>
            <p><?php esc_html_e('Get support and share your progress with other traders.', 'simplifiedtradingtheme'); ?></p>
          </div>
        </div>
      </div>

      <div class="cta-section">
        <h3><?php esc_html_e('Ready to Level Up?', 'simplifiedtradingtheme'); ?></h3>
        <p><?php esc_html_e('Get access to premium trading strategies, advanced TBOW tactics, and exclusive community support.', 'simplifiedtradingtheme'); ?></p>
        <a href="<?php echo esc_url(home_url('/premium')); ?>" class="cta-button">
          <?php esc_html_e('Explore Premium Membership →', 'simplifiedtradingtheme'); ?>
        </a>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>

