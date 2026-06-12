<?php
if (!defined('ABSPATH')) {
    exit;
}
$dreamos_footer_left = $dreamos_footer_left ?? 'Dream.OS capability map';
$dreamos_footer_right = $dreamos_footer_right ?? 'Unlocked through live execution, not theory';
?>
    <div class="footerbar">
      <span><?php echo esc_html($dreamos_footer_left); ?></span>
      <span><?php echo esc_html($dreamos_footer_right); ?></span>
    </div>
  </div>
</div>
</div>
<script>
(() => {
  const root = document.documentElement;
  window.addEventListener('pointermove', (event) => {
    root.style.setProperty('--mx', `${(event.clientX / window.innerWidth) * 100}%`);
    root.style.setProperty('--my', `${(event.clientY / window.innerHeight) * 100}%`);
  }, {passive: true});
})();
</script>
</body>
</html>
