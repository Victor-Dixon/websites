<?php
/**
 * Services Section Template
 * 
 * Displays services on the home page with a unified developer tools link
 */

?>
<section class="services-section" id="services" aria-labelledby="services-heading">
  <h2 id="services-heading" class="section-heading">
    <?php esc_html_e('Our Services', 'simplifiedtradingtheme'); ?>
  </h2>
  <p class="services-intro">
    <?php esc_html_e('Discover powerful tools and resources designed to elevate your trading journey.', 'simplifiedtradingtheme'); ?>
  </p>
  
  <div class="services-grid">
    <!-- Trading Dashboard Service -->
    <div class="service-item featured-service">
      <h3><?php esc_html_e('Trading Dashboard', 'simplifiedtradingtheme'); ?></h3>
      <p><?php esc_html_e('Monitor performance data, sentiment signals, and personalized alerts in one streamlined view tailored to your trading workflow.', 'simplifiedtradingtheme'); ?></p>
      <a href="<?php echo esc_url(home_url('/services')); ?>" class="cta-button">
        <?php esc_html_e('Explore the Dashboard', 'simplifiedtradingtheme'); ?>
      </a>
    </div>

    <!-- Trading Strategies Service -->
    <div class="service-item">
      <h3><?php esc_html_e('Trading Strategies', 'simplifiedtradingtheme'); ?></h3>
      <p><?php esc_html_e('Leverage our AI-powered strategies to optimize your trades and stay ahead in the market.', 'simplifiedtradingtheme'); ?></p>
      <a href="<?php echo esc_url(home_url('/trading-strategies')); ?>" class="cta-button">
        <?php esc_html_e('Explore Strategies', 'simplifiedtradingtheme'); ?>
      </a>
    </div>

    <!-- Educational Resources Service -->
    <div class="service-item">
      <h3><?php esc_html_e('Educational Resources', 'simplifiedtradingtheme'); ?></h3>
      <p><?php esc_html_e('Master trading fundamentals with our tutorials, webinars, and comprehensive guides.', 'simplifiedtradingtheme'); ?></p>
      <a href="<?php echo esc_url(home_url('/education')); ?>" class="cta-button">
        <?php esc_html_e('Learn More', 'simplifiedtradingtheme'); ?>
      </a>
    </div>
  </div>
  
</section>

<style>
.services-section {
  margin: var(--spacing-lg) 0;
  padding: var(--spacing-md) 0;
}

.services-intro {
  text-align: center;
  max-width: 800px;
  margin: 0 auto var(--spacing-lg);
  color: var(--color-text-muted);
  font-size: 1.1rem;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: var(--spacing-md);
  margin-top: var(--spacing-lg);
}

.service-item {
  background: var(--color-dark-grey);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: var(--spacing-md);
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.service-item h3 {
  color: var(--color-accent);
  font-size: 1.5rem;
  margin-bottom: var(--spacing-sm);
}

.service-item p {
  color: var(--color-text-muted);
  margin-bottom: var(--spacing-md);
  line-height: 1.6;
}

.service-item .cta-button {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background: var(--color-accent);
  color: var(--color-black);
  text-decoration: none;
  border-radius: 4px;
  font-weight: 700;
  transition: background 0.3s ease;
}

.service-item .cta-button:hover {
  background: var(--color-nav-hover-bg);
}

@media (max-width: 768px) {
  .services-section .services-grid {
    grid-template-columns: 1fr !important;
    display: grid !important;
  }
  
  .services-section .service-item {
    display: block !important;
    margin-bottom: var(--spacing-md);
  }
}
</style>

