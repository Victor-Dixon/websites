<?php
/**
 * Unified WeAreSwarm navigation.
 */
$dreamos_active = $dreamos_active ?? '';
$items = array(
  'home' => array('label' => 'Command Center', 'href' => '/'),
  'feed' => array('label' => 'Feed', 'href' => '/feed/'),
  'projects' => array('label' => 'Projects', 'href' => '/projects/'),
  'tasks' => array('label' => 'Tasks', 'href' => '/tasks/'),
  'skill-tree' => array('label' => 'Skill Tree', 'href' => '/skill-tree'),
  'services' => array('label' => 'Services', 'href' => '/dreamos-services/'),
  'operator' => array('label' => 'Operator', 'href' => '/profile/'),
  'live-ops' => array('label' => 'Live Ops', 'href' => '/live-ops/'),
  'api' => array('label' => 'API', 'href' => '/wp-json/dreamos/v1/status'),
);
?>
<nav class="topnav" aria-label="Primary navigation">
<?php foreach ($items as $key => $item): ?>
  <a href="<?php echo esc_url($item['href']); ?>"<?php echo $dreamos_active === $key ? ' class="active"' : ''; ?>><?php echo esc_html($item['label']); ?></a>
<?php endforeach; ?>
</nav>
