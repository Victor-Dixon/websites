<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fri_default_menu() {
	?>
	<ul id="primary-menu" class="menu">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#work">Work</a></li>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#services">Services</a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
	</ul>
	<?php
}
