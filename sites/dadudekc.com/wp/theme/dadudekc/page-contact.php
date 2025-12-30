<?php

/**
 * Contact Page Template
 * 
 * Low-friction contact form (Tier 1 Quick Win WEB-04)
 * 
 * @package DaDudeKC
 * @since 1.0.0
 */

get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('page contact-page'); ?>>
            <header class="entry-header">
                <h1 class="page-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>

                <!-- Low-Friction Contact/Booking Form - Tier 1 Quick Win WEB-04 -->
                <div class="subscription-form low-friction">
                    <p class="subscription-intro"><?php esc_html_e('Get started with a free workflow audit. No technical knowledge required.', 'dadudekc'); ?></p>
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="subscription-form-simple" aria-label="Contact Form">
                        <?php wp_nonce_field('contact_form', 'contact_nonce'); ?>
                        <input type="hidden" name="action" value="handle_contact_form">
                        <input 
                            type="email" 
                            name="email" 
                            class="email-only-input" 
                            placeholder="<?php esc_attr_e('Enter your email address', 'dadudekc'); ?>" 
                            required
                            aria-label="<?php esc_attr_e('Email address', 'dadudekc'); ?>"
                        >
                        <button type="submit" class="cta-button primary"><?php esc_html_e('Get Started', 'dadudekc'); ?></button>
                    </form>
                    <p class="subscription-note"><?php esc_html_e('We\'ll review your workflows and show you exactly what we can automate.', 'dadudekc'); ?></p>
                    <div class="premium-upgrade-cta">
                        <p><strong><?php esc_html_e('Ready to get started?', 'dadudekc'); ?></strong> <?php esc_html_e('Book a free consultation to discuss your needs.', 'dadudekc'); ?></p>
                        <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="cta-button secondary"><?php esc_html_e('Schedule Consultation', 'dadudekc'); ?></a>
                    </div>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php
get_footer();

