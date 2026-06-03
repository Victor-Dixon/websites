set -euo pipefail

ROOT="/home/u996867598/domains/dadudekc.site/public_html"
cd "$ROOT"

echo "== WP ROOT =="
pwd
test -f wp-config.php

echo "== ACTIVE THEME =="
THEME="$(wp theme list --status=active --field=name --skip-plugins --skip-themes | tail -n 1)"
echo "ACTIVE_THEME=$THEME"

echo "== PLACEHOLDER OPTION SCAN =="
for needle in trans-menu trans-contacts email@email.com +123456789 trans-socials trans-newsletter; do
  echo "NEEDLE=$needle"
  wp option list --search="*$needle*" --fields=option_name --format=csv --skip-plugins --skip-themes | tail -n +2 || true
done

echo "== PLACEHOLDER FILE SCAN =="
grep -RIn \
  -e "trans-menu" \
  -e "trans-contacts" \
  -e "email@email.com" \
  -e "+123456789" \
  -e "trans-socials" \
  -e "trans-newsletter" \
  wp-content/themes wp-content/plugins 2>/dev/null | head -120 || true

echo "== ADD SAFE FRONT PAGE CSS OVERRIDE =="
PAGE_ID="$(wp post list --post_type=page --name=the-emergence --field=ID --format=ids --skip-plugins --skip-themes | tail -n 1)"
echo "PAGE_ID=$PAGE_ID"

# Use custom CSS to hide obvious theme placeholder blocks/text containers only on the front page/page-id.
# This is safer than editing serialized widget/theme options blind.
CSS='
/* Dream.OS placeholder cleanup: dadudekc.site front page */
body.home .trans-menu,
body.home .trans-contacts,
body.home .trans-socials,
body.home .trans-newsletter,
body.page-id-'"$PAGE_ID"' .trans-menu,
body.page-id-'"$PAGE_ID"' .trans-contacts,
body.page-id-'"$PAGE_ID"' .trans-socials,
body.page-id-'"$PAGE_ID"' .trans-newsletter {
  display: none !important;
}
body.home footer,
body.page-id-'"$PAGE_ID"' footer {
  color: transparent;
}
body.home footer a,
body.home footer span,
body.home footer p,
body.home footer li,
body.page-id-'"$PAGE_ID"' footer a,
body.page-id-'"$PAGE_ID"' footer span,
body.page-id-'"$PAGE_ID"' footer p,
body.page-id-'"$PAGE_ID"' footer li {
  color: transparent !important;
  text-shadow: none !important;
}
'

wp custom-css update "$THEME" "$CSS" --skip-plugins --skip-themes || wp custom-css post "$THEME" "$CSS" --skip-plugins --skip-themes

echo "CUSTOM_CSS_PLACEHOLDER_HIDE=PASS"
