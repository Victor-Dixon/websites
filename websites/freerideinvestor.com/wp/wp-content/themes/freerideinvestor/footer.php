	</main>

	<footer class="site-footer">
		<div class="footer-container container">
			<div class="footer-content">
				<div class="footer-links">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
						)
					);
					?>
				</div>
				<div class="footer-copyright">
					<p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
				</div>
			</div>
		</div>
	</footer>
</div><!-- .site-wrapper -->

<?php wp_footer(); ?>
</body>
</html>
