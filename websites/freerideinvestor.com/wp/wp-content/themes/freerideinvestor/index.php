<?php
get_header();
?>

<div class="container content-area">
	<div class="main-content">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'card mb-3' ); ?>>
					<div class="post-card">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="post-card-image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</a>
							</div>
						<?php endif; ?>
						
						<h2 class="post-card-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						
						<div class="post-card-excerpt">
							<?php the_excerpt(); ?>
						</div>

						<div class="post-card-meta">
							<span class="date"><?php echo get_the_date(); ?></span>
							<a href="<?php the_permalink(); ?>" class="btn btn-secondary btn-sm">Read More</a>
						</div>
					</div>
				</article>
				<?php
			endwhile;

			the_posts_navigation();

		else :
			?>
			<p><?php esc_html_e( 'No posts found.', 'freerideinvestor' ); ?></p>
			<?php
		endif;
		?>
	</div>
	
	<aside class="sidebar">
		<!-- Sidebar widgets would go here -->
        <div class="widget">
            <h3>Search</h3>
            <?php get_search_form(); ?>
        </div>
	</aside>
</div>

<?php
get_footer();
