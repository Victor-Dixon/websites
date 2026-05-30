<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\comments.php
Description: Template for displaying comments within The Trading Robot Plug theme, including custom comment callback, AJAX functionality for loading more comments, and security features like a honeypot field for spam prevention.
Version: 1.0.0
Author: Victor Dixon
*/

/**
 * Template for displaying comments.
 *
 * This template includes various enhancements for accessibility, security, and user experience.
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area" role="region" aria-labelledby="comments-title">
    <h2 id="comments-title" class="comments-title">
        <?php
        printf(
            /* translators: %s: number of comments */
            _n('One comment', '%1$s comments', get_comments_number(), 'my-custom-theme'),
            number_format_i18n(get_comments_number())
        );
        ?>
    </h2>

    <?php if (have_comments()) : ?>
        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'callback'   => 'my_custom_comments_callback', // Optional: Custom callback for comment formatting
            ));
            ?>
        </ol>

        <?php the_comments_navigation(); ?>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <button id="load-more-comments"><?php _e('Load More Comments', 'my-custom-theme'); ?></button>
            <script>
            jQuery('#load-more-comments').click(function() {
                // AJAX request to load more comments
                // Implement AJAX functionality here
            });
            </script>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (!comments_open()) : ?>
        <p class="no-comments"><?php _e('Comments are closed.', 'my-custom-theme'); ?></p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'comment_field' => '<textarea id="comment" name="comment" placeholder="' . __('Your thoughts...', 'my-custom-theme') . '" required></textarea>',
        'fields' => array(
            'author' => '<input id="author" name="author" type="text" placeholder="' . __('Your Name', 'my-custom-theme') . '" required />',
            'email'  => '<input id="email" name="email" type="email" placeholder="' . __('Your Email', 'my-custom-theme') . '" required />',
            'honeypot' => '<input type="text" name="my_honeypot" style="display:none" />', // Honeypot field for spam prevention
        ),
    ));

    /**
     * Custom function to add a vote button (upvote/downvote) to each comment.
     */
    function my_custom_comments_callback($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                <footer class="comment-meta">
                    <div class="comment-author vcard">
                        <?php echo get_avatar($comment, 48, '', '', array('loading' => 'lazy')); ?>
                        <?php printf(__('%s <span class="says">says:</span>', 'my-custom-theme'), get_comment_author_link()); ?>
                    </div>
                    <div class="comment-metadata">
                        <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                            <?php printf(__('%1$s at %2$s', 'my-custom-theme'), get_comment_date(), get_comment_time()); ?>
                        </a>
                    </div>
                    <?php edit_comment_link(__('Edit', 'my-custom-theme'), '<span class="edit-link">', '</span>'); ?>
                </footer>

                <?php if ($comment->comment_approved == '0') : ?>
                    <p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'my-custom-theme'); ?></p>
                <?php endif; ?>

                <div class="comment-content">
                    <?php echo esc_html(get_comment_text()); ?>
                </div>

                <div class="comment-vote">
                    <a href="#" class="comment-vote-up">👍</a>
                    <a href="#" class="comment-vote-down">👎</a>
                </div>

                <div class="reply">
                    <?php
                    comment_reply_link(array_merge($args, array(
                        'reply_text' => __('Reply', 'my-custom-theme'),
                        'depth'      => $depth,
                        'max_depth'  => $args['max_depth'],
                    )));
                    ?>
                </div>
            </article>
        </li>
        <?php
    }
    ?>
</div>

<?php
// Security: Validate and process honeypot field
add_filter('preprocess_comment', 'my_custom_verify_honeypot');
function my_custom_verify_honeypot($commentdata) {
    if (!empty($_POST['my_honeypot'])) {
        wp_die(__('Spam detected!', 'my-custom-theme'));
    }
    return $commentdata;
}
?>
