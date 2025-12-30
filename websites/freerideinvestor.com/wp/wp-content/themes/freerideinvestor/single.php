<?php
get_header();
?>

<div class="container content-area">
	<div class="main-content">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<div class="entry-meta">
						<span class="posted-on">
							<?php echo get_the_date(); ?>
						</span>
						<span class="byline">
							<?php esc_html_e( 'by', 'freerideinvestor' ); ?> <?php the_author(); ?>
						</span>
					</div>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="post-thumbnail mb-2">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>

				<div class="entry-content">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'freerideinvestor' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>
			</article>
			<?php
			// Previous/next post navigation.
			the_post_navigation(
				array(
					'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'freerideinvestor' ) . '</span> ' .
						'<span class="screen-reader-text">' . __( 'Next post:', 'freerideinvestor' ) . '</span> ' .
						'<span class="post-title">%title</span>',
					'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'freerideinvestor' ) . '</span> ' .
						'<span class="screen-reader-text">' . __( 'Previous post:', 'freerideinvestor' ) . '</span> ' .
						'<span class="post-title">%title</span>',
				)
			);

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>
	</div>
	
	<aside class="sidebar">
        <!-- Sidebar content -->
        <div class="widget">
            <h3>Recent Posts</h3>
            <ul>
                <?php
                $recent_posts = wp_get_recent_posts(array('numberposts' => 5, 'post_status' => 'publish'));
                foreach($recent_posts as $post) {
                    echo '<li><a href="' . get_permalink($post['ID']) . '">' .   $post['post_title'].'</a></li>';
                }
                ?>
            </ul>
        </div>
	</aside>
</div>

<?php
get_footer();
