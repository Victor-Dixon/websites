set -euo pipefail

ROOT="/home/u996867598/domains/dadudekc.site/public_html"
cd "$ROOT"

echo "== VERIFY WP ROOT =="
pwd
test -f wp-config.php

echo "== ACTIVE THEME =="
THEME="$(wp theme list --status=active --field=name --skip-plugins --skip-themes | tail -n 1)"
echo "ACTIVE_THEME=$THEME"

THEME_DIR="wp-content/themes/$THEME"
test -d "$THEME_DIR"

STAMP="$(date +%Y%m%d_%H%M%S)"
BACKUP_DIR="$ROOT/wp-content/dreamos-backups/theme-footer-placeholder-cleanup-$STAMP"
mkdir -p "$BACKUP_DIR"

echo "BACKUP_DIR=$BACKUP_DIR"

for rel in parts/footer.html parts/footer-landing.html; do
  src="$THEME_DIR/$rel"
  if [ -f "$src" ]; then
    mkdir -p "$BACKUP_DIR/$(dirname "$rel")"
    cp "$src" "$BACKUP_DIR/$rel"
    echo "BACKUP=PASS $src -> $BACKUP_DIR/$rel"
  else
    echo "MISSING_THEME_PART=$src"
  fi
done

echo "== WRITE CLEAN FOOTER PARTS =="
cat > "$THEME_DIR/parts/footer.html" << 'HTML'
<!-- wp:group {"tagName":"footer","align":"full","style":{"spacing":{"padding":{"top":"36px","right":"22px","bottom":"42px","left":"22px"}},"color":{"background":"#030712"}},"layout":{"type":"constrained"}} -->
<footer class="wp-block-group alignfull has-background" style="background-color:#030712;padding-top:36px;padding-right:22px;padding-bottom:42px;padding-left:22px">
  <!-- wp:group {"align":"wide","style":{"border":{"top":{"color":"rgba(255,255,255,.14)","width":"1px"}},"spacing":{"padding":{"top":"24px"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
  <div class="wp-block-group alignwide" style="border-top-color:rgba(255,255,255,.14);border-top-width:1px;padding-top:24px">
    <!-- wp:paragraph {"style":{"typography":{"fontSize":"13px","fontWeight":"700"},"color":{"text":"#718096"}}} -->
    <p class="has-text-color" style="color:#718096;font-size:13px;font-weight:700">The Emergence // Spark OS</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph {"style":{"typography":{"fontSize":"13px"},"color":{"text":"#718096"}}} -->
    <p class="has-text-color" style="color:#718096;font-size:13px">Fair systems. Cinematic outcomes. Real build proof.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->
</footer>
<!-- /wp:group -->
HTML

cat > "$THEME_DIR/parts/footer-landing.html" << 'HTML'
<!-- wp:group {"tagName":"footer","align":"full","style":{"spacing":{"padding":{"top":"36px","right":"22px","bottom":"42px","left":"22px"}},"color":{"background":"#030712"}},"layout":{"type":"constrained"}} -->
<footer class="wp-block-group alignfull has-background" style="background-color:#030712;padding-top:36px;padding-right:22px;padding-bottom:42px;padding-left:22px">
  <!-- wp:group {"align":"wide","style":{"border":{"top":{"color":"rgba(255,255,255,.14)","width":"1px"}},"spacing":{"padding":{"top":"24px"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
  <div class="wp-block-group alignwide" style="border-top-color:rgba(255,255,255,.14);border-top-width:1px;padding-top:24px">
    <!-- wp:paragraph {"style":{"typography":{"fontSize":"13px","fontWeight":"700"},"color":{"text":"#718096"}}} -->
    <p class="has-text-color" style="color:#718096;font-size:13px;font-weight:700">The Emergence // Spark OS</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph {"style":{"typography":{"fontSize":"13px"},"color":{"text":"#718096"}}} -->
    <p class="has-text-color" style="color:#718096;font-size:13px">Fair systems. Cinematic outcomes. Real build proof.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->
</footer>
<!-- /wp:group -->
HTML

echo "== VERIFY THEME FILES CLEAN =="
for rel in parts/footer.html parts/footer-landing.html; do
  file="$THEME_DIR/$rel"
  test -f "$file"
  grep -q "The Emergence // Spark OS" "$file"
  if grep -Eq "trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter" "$file"; then
    echo "THEME_PART_PLACEHOLDER_SCAN=FAIL $file"
    exit 2
  fi
done

echo "== FLUSH CACHE IF AVAILABLE =="
wp cache flush --skip-plugins --skip-themes || true

echo "THEME_FOOTER_PATCH=PASS"
