set -euo pipefail

ROOT="/home/u996867598/domains/dadudekc.site/public_html"
cd "$ROOT"
test -f wp-config.php

echo "== ACTIVE THEME / HOMEPAGE =="
wp theme activate dreamos-emergence --skip-plugins --skip-themes || true

PAGE_ID="$(wp post list --post_type=page --name=the-emergence --field=ID --format=ids --skip-plugins --skip-themes | tail -n 1)"
test -n "$PAGE_ID"

wp option update show_on_front page --skip-plugins --skip-themes
wp option update page_on_front "$PAGE_ID" --skip-plugins --skip-themes

echo "PAGE_ID=$PAGE_ID"

echo "== PAGE CONTENT VERIFY =="
wp post get "$PAGE_ID" --field=post_content --skip-plugins --skip-themes > /tmp/dreamos_page_content.html
grep -q "Spark OS" /tmp/dreamos_page_content.html
grep -q "Cinematic outside. Deterministic inside." /tmp/dreamos_page_content.html

echo "== HARD CACHE PURGE =="
wp cache flush --skip-plugins --skip-themes || true
wp litespeed-purge all --allow-root || true
wp transient delete --all --skip-plugins --skip-themes || true

rm -rf wp-content/cache/* 2>/dev/null || true
rm -rf wp-content/litespeed/* 2>/dev/null || true
rm -rf wp-content/uploads/cache/* 2>/dev/null || true
rm -rf wp-content/.cache/* 2>/dev/null || true

echo "== ADD NO-CACHE MU PLUGIN FOR EMERGENCE ROUTES =="
mkdir -p wp-content/mu-plugins
cat > wp-content/mu-plugins/dreamos-emergence-no-cache.php << 'PHP'
<?php
/**
 * Plugin Name: DreamOS Emergence No Cache
 * Description: Forces no-cache headers on Emergence launch routes during active buildout.
 */
if (!defined('ABSPATH')) {
    exit;
}
add_action('send_headers', function () {
    if (is_front_page() || is_page(['the-emergence', 'spark-generator', 'spark-battle-sim'])) {
        nocache_headers();
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
        header('X-DreamOS-Emergence: live');
    }
}, 0);
PHP

echo "MU_NO_CACHE_PLUGIN=PASS"

echo "== STATIC ROOT FALLBACK PREP =="
STATIC_SOURCE="/tmp/dreamos_static_root.html"

# Pull rendered page content from WP source payload by reconstructing from current page content.
# Use local source shipped from operator as static source if present.
cat > "$STATIC_SOURCE" < /dev/stdin

grep -q "Spark OS" "$STATIC_SOURCE"
grep -q "What-If Arena" "$STATIC_SOURCE"

if grep -q "Spark Protocol Archive" "$STATIC_SOURCE"; then
  echo "STATIC_SOURCE_OLD=FAIL"
  exit 4
fi

echo "STATIC_SOURCE_READY=PASS"

echo "== PLAIN ROOT PROBE BEFORE STATIC OVERRIDE =="
PLAIN="$(curl -L -s -H 'Cache-Control: no-cache' https://dadudekc.site/ || true)"
if printf '%s' "$PLAIN" | grep -q "Spark OS" && ! printf '%s' "$PLAIN" | grep -q "Spark Protocol Archive"; then
  echo "PLAIN_ROOT_ALREADY_FIXED=PASS"
  echo "STATIC_OVERRIDE_USED=NO"
else
  echo "PLAIN_ROOT_STILL_STALE=YES"
  STAMP="$(date +%Y%m%d_%H%M%S)"
  if [ -f index.html ]; then
    cp index.html "index.html.bak_dreamos_$STAMP"
    echo "INDEX_HTML_BACKUP=index.html.bak_dreamos_$STAMP"
  fi
  cp "$STATIC_SOURCE" index.html
  echo "STATIC_OVERRIDE_USED=YES"
fi

echo "== FINAL REMOTE ROOT PROBE =="
curl -L -s -H 'Cache-Control: no-cache' https://dadudekc.site/ > /tmp/final_root.html || true
grep -q "Spark OS" /tmp/final_root.html
grep -q "What-If Arena" /tmp/final_root.html

if grep -Eq "Spark Protocol Archive|trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter" /tmp/final_root.html; then
  echo "FINAL_REMOTE_ROOT_STALE_OR_PLACEHOLDER=FAIL"
  grep -En "Spark Protocol Archive|trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter" /tmp/final_root.html || true
  exit 5
fi

echo "REMOTE_FORCE_ROOT=PASS"
