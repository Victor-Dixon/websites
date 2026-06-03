set -euo pipefail

ROOT="/home/u996867598/domains/dadudekc.site/public_html"
cd "$ROOT"
test -f wp-config.php

echo "== ACTIVE THEME =="
ACTIVE_THEME="$(wp theme list --status=active --field=name --skip-plugins --skip-themes | tail -n 1)"
echo "ACTIVE_THEME=$ACTIVE_THEME"

if [ "$ACTIVE_THEME" != "dreamos-emergence" ]; then
  echo "ACTIVATING_DREAMOS_THEME"
  wp theme activate dreamos-emergence --skip-plugins --skip-themes
fi

echo "== FRONT PAGE OPTIONS =="
SHOW_ON_FRONT="$(wp option get show_on_front --skip-plugins --skip-themes)"
PAGE_ON_FRONT="$(wp option get page_on_front --skip-plugins --skip-themes)"
echo "SHOW_ON_FRONT=$SHOW_ON_FRONT"
echo "PAGE_ON_FRONT=$PAGE_ON_FRONT"

PAGE_ID="$(wp post list --post_type=page --name=the-emergence --field=ID --format=ids --skip-plugins --skip-themes | tail -n 1)"
test -n "$PAGE_ID"
echo "THE_EMERGENCE_ID=$PAGE_ID"

wp option update show_on_front page --skip-plugins --skip-themes
wp option update page_on_front "$PAGE_ID" --skip-plugins --skip-themes

echo "== PAGE CONTENT VERIFY =="
wp post get "$PAGE_ID" --field=post_content --skip-plugins --skip-themes > /tmp/the_emergence_content.html
grep -q "Spark OS" /tmp/the_emergence_content.html
grep -q "Cinematic outside. Deterministic inside." /tmp/the_emergence_content.html

if grep -q "Spark Protocol Archive" /tmp/the_emergence_content.html; then
  echo "REMOTE_PAGE_CONTENT_STILL_OLD=FAIL"
  exit 3
fi

echo "== THEME PART VERIFY =="
for f in wp-content/themes/dreamos-emergence/parts/footer.html wp-content/themes/dreamos-emergence/parts/header.html; do
  test -f "$f"
  grep -q "Spark OS" "$f"
done

if grep -RIEq "trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter" wp-content/themes/dreamos-emergence; then
  echo "DREAMOS_THEME_PLACEHOLDER_SCAN=FAIL"
  grep -RInE "trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter" wp-content/themes/dreamos-emergence || true
  exit 4
fi

echo "== CACHE FLUSH =="
wp cache flush --skip-plugins --skip-themes || true

if wp plugin is-active litespeed-cache --skip-themes >/dev/null 2>&1; then
  wp litespeed-purge all --allow-root || true
  wp option delete litespeed.conf.cache --skip-plugins --skip-themes || true
fi

rm -rf wp-content/cache/* 2>/dev/null || true
rm -rf wp-content/litespeed/* 2>/dev/null || true

echo "REMOTE_REPAIR=PASS"
