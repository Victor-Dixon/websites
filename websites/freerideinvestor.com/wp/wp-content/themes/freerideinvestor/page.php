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
				</header>

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
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>
	</div>
	
	<aside class="sidebar">
        <!-- Sidebar content -->
	</aside>
</div>

<?php
get_footer();
