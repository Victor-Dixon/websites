<?php
/**
 * Template Name: Contact
 * @package Freerideinvestor-Modern
 * 
 * This template loads the proper contact page template
 */

// Load the proper contact page template directly (it includes get_header() and get_footer())
$template_path = get_template_directory() . '/page-templates/page-contact.php';
if (file_exists($template_path)) {
    // Include the template directly - it handles header/footer
    include($template_path);
    exit; // Prevent WordPress from loading default page template
} else {
    // Fallback: Display basic contact info
    get_header();
    ?>
    <div class="container">
        <div class="content-area">
            <div class="main-content">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>
                        <div class="entry-content">
                            <?php the_content(); ?>
                            <p>Email us at <a href="mailto:support@freerideinvestor.com">support@freerideinvestor.com</a> or join our Discord community.</p>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php
    get_footer();
}
?>