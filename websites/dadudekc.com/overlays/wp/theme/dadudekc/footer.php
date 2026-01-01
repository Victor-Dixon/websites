<?php
/**
 * Footer template.
 *
 * @package DaDudeKC
 */
?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong><?php esc_html_e('DaDudeKC', 'dadudekc'); ?></strong>
            <p><?php esc_html_e('Builder portfolio, Idea Lab, and blog for Victor.', 'dadudekc'); ?></p>
        </div>
        <div class="footer-links">
            <strong><?php esc_html_e('Quick Links', 'dadudekc'); ?></strong>
            <a href="<?php echo esc_url(dadudekc_get_portfolio_url()); ?>"><?php esc_html_e('Portfolio', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>"><?php esc_html_e('Idea Lab', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>"><?php esc_html_e('Blog', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(feed_link('rss2')); ?>"><?php esc_html_e('RSS Feed', 'dadudekc'); ?></a>
        </div>
        <div>
            <strong><?php esc_html_e('Stay in the loop', 'dadudekc'); ?></strong>
            <form class="cta-row" action="#" method="post">
                <label class="screen-reader-text" for="footer-email"><?php esc_html_e('Email', 'dadudekc'); ?></label>
                <input type="email" id="footer-email" name="email" placeholder="<?php esc_attr_e('Email address', 'dadudekc'); ?>">
                <button type="submit"><?php esc_html_e('Subscribe', 'dadudekc'); ?></button>
            </form>
            <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>"><?php esc_html_e('Contact Victor →', 'dadudekc'); ?></a>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
