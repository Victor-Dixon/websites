<?php
/* Template Name: Live Ops */
get_header();
?>
<section class="panel"><span class="badge badge-green"><span class="live-dot"></span> LIVE OPS</span><h1 class="glitch" data-text="OPERATIONS BOARD">OPERATIONS BOARD</h1><p>Machine-readable and human-readable swarm telemetry from the Dream.OS status plugin.</p></section>
<?php echo do_shortcode('[dreamos_swarm_status]'); ?>
<section class="panel"><h2>API Endpoint</h2><p><a href="<?php echo esc_url(home_url('/wp-json/dreamos/v1/status')); ?>">/wp-json/dreamos/v1/status</a></p></section>
<style>.panel h1{font-size:clamp(2.8rem,8vw,6rem);color:var(--green);text-shadow:var(--glow-green)}</style>
<?php get_footer(); ?>
