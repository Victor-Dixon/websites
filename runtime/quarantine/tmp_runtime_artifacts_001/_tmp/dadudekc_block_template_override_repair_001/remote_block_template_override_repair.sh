set -euo pipefail

ROOT="/home/u996867598/domains/dadudekc.site/public_html"
cd "$ROOT"
test -f wp-config.php

echo "== ACTIVE THEME =="
ACTIVE="$(wp theme list --status=active --field=name --skip-plugins --skip-themes | tail -n 1)"
echo "ACTIVE_THEME=$ACTIVE"
if [ "$ACTIVE" != "dreamos-emergence" ]; then
  wp theme activate dreamos-emergence --skip-plugins --skip-themes
fi

echo "== FRONT PAGE =="
PAGE_ID="$(wp post list --post_type=page --name=the-emergence --field=ID --format=ids --skip-plugins --skip-themes | tail -n 1)"
test -n "$PAGE_ID"
wp option update show_on_front page --skip-plugins --skip-themes
wp option update page_on_front "$PAGE_ID" --skip-plugins --skip-themes
echo "THE_EMERGENCE_ID=$PAGE_ID"

echo "== TEMPLATE OVERRIDE INVENTORY =="
wp post list --post_type=wp_template --post_status=any --fields=ID,post_title,post_name,post_status --format=table --skip-plugins --skip-themes || true
wp post list --post_type=wp_template_part --post_status=any --fields=ID,post_title,post_name,post_status --format=table --skip-plugins --skip-themes || true

BACKUP="/tmp/dreamos_template_override_backup_$(date +%Y%m%d_%H%M%S).txt"
: > "$BACKUP"

echo "== REMOVE STALE HOSTINGER PLACEHOLDER TEMPLATE PARTS =="
for id in $(wp post list --post_type=wp_template_part --post_status=any --format=ids --skip-plugins --skip-themes || true); do
  content="$(wp post get "$id" --field=post_content --skip-plugins --skip-themes || true)"
  title="$(wp post get "$id" --field=post_title --skip-plugins --skip-themes || true)"
  name="$(wp post get "$id" --field=post_name --skip-plugins --skip-themes || true)"

  if printf '%s' "$content" | grep -Eq "trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter|Spark Protocol Archive"; then
    {
      echo "----- TEMPLATE_PART_BACKUP id=$id title=$title name=$name -----"
      printf '%s\n' "$content"
    } >> "$BACKUP"
    echo "DELETE_STALE_TEMPLATE_PART id=$id title=$title name=$name"
    wp post delete "$id" --force --skip-plugins --skip-themes
  fi
done

echo "== REMOVE STALE HOSTINGER PAGE TEMPLATE OVERRIDES =="
for id in $(wp post list --post_type=wp_template --post_status=any --format=ids --skip-plugins --skip-themes || true); do
  content="$(wp post get "$id" --field=post_content --skip-plugins --skip-themes || true)"
  title="$(wp post get "$id" --field=post_title --skip-plugins --skip-themes || true)"
  name="$(wp post get "$id" --field=post_name --skip-plugins --skip-themes || true)"

  if printf '%s' "$content" | grep -Eq "trans-menu|trans-contacts|email@email.com|\+123456789|trans-socials|trans-newsletter|Spark Protocol Archive"; then
    {
      echo "----- TEMPLATE_BACKUP id=$id title=$title name=$name -----"
      printf '%s\n' "$content"
    } >> "$BACKUP"
    echo "DELETE_STALE_TEMPLATE id=$id title=$title name=$name"
    wp post delete "$id" --force --skip-plugins --skip-themes
  fi
done

echo "TEMPLATE_OVERRIDE_BACKUP=$BACKUP"

echo "== VERIFY PAGE CONTENT =="
wp post get "$PAGE_ID" --field=post_content --skip-plugins --skip-themes > /tmp/the_emergence_content.html
grep -q "Spark OS" /tmp/the_emergence_content.html
grep -q "Cinematic outside. Deterministic inside." /tmp/the_emergence_content.html
if grep -q "Spark Protocol Archive" /tmp/the_emergence_content.html; then
  echo "PAGE_CONTENT_STILL_OLD=FAIL"
  exit 3
fi

echo "== FLUSH ALL CACHES =="
wp cache flush --skip-plugins --skip-themes || true
wp litespeed-purge all --allow-root || true
rm -rf wp-content/cache/* 2>/dev/null || true
rm -rf wp-content/litespeed/* 2>/dev/null || true

echo "REMOTE_BLOCK_TEMPLATE_REPAIR=PASS"
