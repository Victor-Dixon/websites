<?php
/**
 * Template Name: Trading Mindset Journal Landing Page
 * Description: Lead magnet landing page for Improved Trading Mindset Journal PDF
 * Phase 1 P0 Fix - FUN-01
 */

get_header();
?>

<section class="lead-magnet-landing">
  <div class="container">
    <div class="landing-content">
      <h1><?php esc_html_e('Get Your Free Trading Mindset Journal', 'simplifiedtradingtheme'); ?></h1>
      <p class="lead-text">
        <?php esc_html_e('Master the mental game of trading. This journal helps you build the discipline, emotional control, and mindset needed for consistent trading success.', 'simplifiedtradingtheme'); ?>
      </p>
      
      <div class="value-props">
        <div class="value-prop">
          <strong><?php esc_html_e('✅ Daily trading mindset exercises', 'simplifiedtradingtheme'); ?></strong>
        </div>
        <div class="value-prop">
          <strong><?php esc_html_e('✅ Emotional awareness tracking', 'simplifiedtradingtheme'); ?></strong>
        </div>
        <div class="value-prop">
          <strong><?php esc_html_e('✅ Post-trade review templates', 'simplifiedtradingtheme'); ?></strong>
        </div>
      </div>

      <div class="lead-form-container">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="lead-magnet-form">
          <?php wp_nonce_field('mindset_journal_download_form', 'mindset_journal_nonce'); ?>
          <input type="hidden" name="action" value="mindset_journal_download">
          <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/thank-you-mindset-journal')); ?>">
          
          <div class="form-group">
            <label for="email"><?php esc_html_e('Your Email Address', 'simplifiedtradingtheme'); ?></label>
            <input type="email" id="email" name="email" required placeholder="<?php esc_attr_e('Enter your email', 'simplifiedtradingtheme'); ?>">
          </div>
          
          <div class="form-group">
            <label>
              <input type="checkbox" name="agree_to_policy" required>
              <?php esc_html_e('I agree to receive trading insights and updates', 'simplifiedtradingtheme'); ?>
            </label>
          </div>
          
          <button type="submit" class="cta-button">
            <?php esc_html_e('Get Free Journal →', 'simplifiedtradingtheme'); ?>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>

