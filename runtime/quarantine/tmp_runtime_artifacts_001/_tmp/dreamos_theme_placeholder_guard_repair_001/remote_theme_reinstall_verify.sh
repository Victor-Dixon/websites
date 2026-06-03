set -euo pipefail

ROOT="/home/u996867598/domains/dadudekc.site/public_html"
THEME_SLUG="dreamos-emergence"
ZIP_REMOTE="/tmp/dreamos-emergence.zip"

cd "$ROOT"
test -f wp-config.php

echo "== INSTALL UPDATED THEME =="
wp theme install "$ZIP_REMOTE" --force --skip-plugins --skip-themes

echo "== ACTIVATE THEME =="
wp theme activate "$THEME_SLUG" --skip-plugins --skip-themes

ACTIVE_THEME="$(wp theme list --status=active --field=name --skip-plugins --skip-themes | tail -n 1)"
echo "ACTIVE_THEME=$ACTIVE_THEME"
test "$ACTIVE_THEME" = "$THEME_SLUG"

echo "== REMOTE THEME PLACEHOLDER LITERAL SCAN =="
if grep -RIEq "trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter" "wp-content/themes/$THEME_SLUG"; then
  echo "REMOTE_THEME_PLACEHOLDER_LITERAL_SCAN=FAIL"
  grep -RInE "trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter" "wp-content/themes/$THEME_SLUG" || true
  exit 3
fi
echo "REMOTE_THEME_PLACEHOLDER_LITERAL_SCAN=PASS"

echo "== VERIFY HOMEPAGE OPTION =="
PAGE_ID="$(wp post list --post_type=page --name=the-emergence --field=ID --format=ids --skip-plugins --skip-themes | tail -n 1)"
test -n "$PAGE_ID"
wp option update show_on_front page --skip-plugins --skip-themes
wp option update page_on_front "$PAGE_ID" --skip-plugins --skip-themes
echo "HOMEPAGE_SET=PASS id=$PAGE_ID"

echo "== FLUSH CACHES =="
wp cache flush --skip-plugins --skip-themes || true
wp litespeed-purge all --allow-root || true
rm -rf wp-content/cache/* 2>/dev/null || true
rm -rf wp-content/litespeed/* 2>/dev/null || true

echo "REMOTE_THEME_REINSTALL_VERIFY=PASS"
