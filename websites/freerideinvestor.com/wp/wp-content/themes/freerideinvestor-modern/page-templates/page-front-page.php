<?php

/**
 * Template Name: Front Page
 * Description: The front page template for the SimplifiedTradingTheme.
 */

get_header();
?>

<section class="hero">
  <h1 id="hero-heading"><?php esc_html_e('Stop Losing Money on Trades', 'simplifiedtradingtheme'); ?></h1>
  <p class="hero-subheadline"><?php esc_html_e('Get Proven TBOW Tactics That Work - Join 1,000+ Traders Building Consistent Edge', 'simplifiedtradingtheme'); ?></p>
  <?php get_template_part('template-parts/components/positioning-statement'); ?>
  <div class="hero-cta-row">
    <a class="cta-button primary" href="<?php echo esc_url(home_url('/roadmap')); ?>" role="button">
      <?php esc_html_e('Get Your Free Trading Roadmap →', 'simplifiedtradingtheme'); ?>
    </a>
    <a class="cta-button secondary" href="<?php echo esc_url(home_url('/discord')); ?>" role="button">
      <?php esc_html_e('Join Community', 'simplifiedtradingtheme'); ?>
    </a>
  </div>
  <p class="hero-urgency"><?php esc_html_e('Limited spots available - Start your trading transformation today', 'simplifiedtradingtheme'); ?></p>
</section>

<!-- Main Container -->
<div class="container">

  <!-- Welcome (About) Section -->
  <section class="welcome-section" id="about" aria-labelledby="about-heading">
    <h2 id="about-heading" class="section-heading">
      <?php esc_html_e('Welcome to FreeRideInvestor.com', 'simplifiedtradingtheme'); ?>
    </h2>
    <p class="introduction">
      <?php esc_html_e(
        'At FreeRideInvestor.com, we transform the teamwork and preparation principles from collaborative challenges into actionable trading strategies. Our Tbow Tactics, branded as "The Blueprint of Winners," are designed to simplify complex markets into clear, confidence-building steps for consistent success.',
        'simplifiedtradingtheme'
      ); ?>
    </p>
    <?php get_template_part('template-parts/components/icp-definition'); ?>
    <a href="<?php echo esc_url(home_url('/about')); ?>" class="cta-button">
      <?php esc_html_e('Learn More', 'simplifiedtradingtheme'); ?>
    </a>
  </section>


  <!-- Services Section -->
  <?php get_template_part('template-parts/services-section'); ?>

  <!-- Offer Ladder Section -->
  <section class="offer-ladder-section" aria-labelledby="offer-ladder-heading">
    <h2 id="offer-ladder-heading" class="section-heading">
      <?php esc_html_e('Your Path to Trading Success', 'simplifiedtradingtheme'); ?>
    </h2>
    <?php get_template_part('template-parts/components/offer-ladder'); ?>
  </section>

  <!-- Tbow Tactics Section -->
  <section class="tbow-tactics" aria-labelledby="tbow-tactics-heading">
    <h2 id="tbow-tactics-heading"><?php esc_html_e('Tbow Tactics: The Blueprint of Winners', 'simplifiedtradingtheme'); ?></h2>
    <p>
      <?php esc_html_e(
        'Tbow Tactics distill years of market insights into a structured approach to trading. Learn strategies like the "Sniper Entry Method," which combines market timing and technical indicators for precision trades. By mastering these tactics, you’ll build the confidence and skills necessary to thrive in any market condition.',
        'simplifiedtradingtheme'
      ); ?>
    </p>
    <a href="<?php echo esc_url(home_url('/tbow-tactics')); ?>" class="cta-button">
      <?php esc_html_e('Explore Tbow Tactics', 'simplifiedtradingtheme'); ?>
    </a>
  </section>

  <!-- eBook Showcase Section -->
  <section class="ebook-showcase" aria-labelledby="ebook-heading">
    <h2 id="ebook-heading" class="section-heading">
      <?php esc_html_e('Get Your Free Trading Guide!', 'simplifiedtradingtheme'); ?>
    </h2>
    <div class="ebook-content">
      <div class="ebook-image">
        <img
          src="<?php echo get_template_directory_uri(); ?>/assets/images/ebook-cover.jpg"
          alt="<?php esc_attr_e('Free Trading eBook Cover', 'simplifiedtradingtheme'); ?>"
          loading="lazy">
      </div>
      <div class="ebook-details">
        <h3><?php esc_html_e('Mastering the Markets: Your Ultimate Trading Playbook', 'simplifiedtradingtheme'); ?></h3>
        <p><?php esc_html_e(
              'This eBook is the perfect introduction to FreeRideInvestor\'s methodologies. Discover actionable strategies, risk management frameworks, and exclusive insights to elevate your trading journey.',
              'simplifiedtradingtheme'
            ); ?></p>
        <p class="ebook-urgency"><?php esc_html_e(
                                    'Don’t miss out! This exclusive resource is available for a limited time. Download your copy today and start trading smarter.',
                                    'simplifiedtradingtheme'
                                  ); ?></p>
        <form
          action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
          method="POST"
          class="ebook-form"
          aria-label="<?php esc_attr_e('eBook Download Form', 'simplifiedtradingtheme'); ?>">
          <?php wp_nonce_field('ebook_download_form', 'ebook_nonce'); ?>
          <input
            type="email"
            name="subscribe_email"
            placeholder="<?php esc_attr_e('Your email', 'simplifiedtradingtheme'); ?>"
            required>
          <div class="form-footer">
            <label>
              <input type="checkbox" name="agree_to_policy" required>
              <?php esc_html_e('I agree to the Privacy Policy', 'simplifiedtradingtheme'); ?>
            </label>
            <button type="submit" class="btn btn-accent">
              <?php esc_html_e('Download Now', 'simplifiedtradingtheme'); ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Latest Posts Section -->
  <section class="latest-posts" aria-labelledby="latest-posts-heading">
    <h2 id="latest-posts-heading"><?php esc_html_e('Latest Tbow Tactic Insights', 'simplifiedtradingtheme'); ?></h2>
    <?php
    $latest_posts = new WP_Query([
      'posts_per_page' => 6,
      'category_name'  => 'tbow-tactic',
      'ignore_sticky_posts' => true
    ]);

    if ($latest_posts->have_posts()) :
      echo '<div class="posts-grid">';
      while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
        <div class="post-item">
          <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('thumbnail', ['alt' => get_the_title(), 'loading' => 'lazy']); ?>
            </a>
          <?php endif; ?>
          <p><?php the_excerpt(); ?></p>
          <a href="<?php the_permalink(); ?>" class="read-more" role="button">
            <?php esc_html_e('Read More', 'simplifiedtradingtheme'); ?>
          </a>
        </div>
    <?php endwhile;
      echo '</div>';
      wp_reset_postdata();
    else :
      echo '<p>' . esc_html__('No recent tactics available.', 'simplifiedtradingtheme') . '</p>';
    endif;
    ?>
  </section>

  <!-- Subscription Section - Low Friction (WEB-04 Quick Win) -->
  <section class="subscription-section" aria-labelledby="subscription-heading">
    <h2 id="subscription-heading"><?php esc_html_e('Get Free Trading Insights', 'simplifiedtradingtheme'); ?></h2>
    <p class="subscription-intro"><?php esc_html_e('Join our newsletter and get weekly trading tips delivered to your inbox', 'simplifiedtradingtheme'); ?></p>
    <form
      action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
      method="POST"
      class="subscription-form low-friction"
      aria-label="<?php esc_attr_e('Subscription Form', 'simplifiedtradingtheme'); ?>">
      <?php
      wp_nonce_field('mailchimp_subscription', 'mailchimp_subscription_nonce');
      ?>
      <input type="hidden" name="action" value="mailchimp_subscription_form">
      <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">
      <div class="subscription-form-simple">
        <input
          type="email"
          id="subscription-email"
          name="subscribe_email"
          placeholder="<?php esc_attr_e('Enter your email', 'simplifiedtradingtheme'); ?>"
          required
          class="email-only-input">
        <button type="submit" class="cta-button">
          <?php esc_html_e('Subscribe Free →', 'simplifiedtradingtheme'); ?>
        </button>
      </div>
      <p class="subscription-note"><?php esc_html_e('No spam. Unsubscribe anytime.', 'simplifiedtradingtheme'); ?></p>
    </form>
    <div class="premium-upgrade-cta">
      <p><?php esc_html_e('Ready to level up?', 'simplifiedtradingtheme'); ?></p>
      <a href="<?php echo esc_url(home_url('/premium')); ?>" class="cta-button">
        <?php esc_html_e('Explore Premium Membership →', 'simplifiedtradingtheme'); ?>
      </a>
    </div>
  </section>

</div>

<?php get_footer(); ?>