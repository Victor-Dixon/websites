<?php
if (!defined('ABSPATH')) {
    exit;
}
$dreamos_title = $dreamos_title ?? 'WeAreSwarm | Dream.OS';
$dreamos_active = $dreamos_active ?? '';
$dreamos_canonical = $dreamos_canonical ?? '';
$shell_css_path = get_template_directory() . '/assets/dreamos-shell.css';
$shell_css = is_readable($shell_css_path) ? file_get_contents($shell_css_path) : '';
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html($dreamos_title); ?></title>
<?php if ($dreamos_canonical): ?>
<link rel="canonical" href="<?php echo esc_url($dreamos_canonical); ?>">
<?php endif; ?>
<style><?php echo $shell_css; ?></style>
<?php if (!empty($dreamos_page_css)): ?>
<style><?php echo $dreamos_page_css; ?></style>
<?php endif; ?>
</head>
<body>
<div class="stars" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
<div class="wrap">
  <div class="shell">
    <header class="shell-header">
      <a class="brand" href="https://www.weareswarm.site/">We Are Swarm</a>
      <?php get_template_part('nav'); ?>
    </header>
    <div class="shell-main">
