set -euo pipefail

ROOT="/home/u996867598/domains/dadudekc.site/public_html"
THEME_SLUG="dreamos-emergence"
ZIP_REMOTE="/tmp/dreamos-emergence.zip"
REPORT_REMOTE="/tmp/dreamos_emergence_functionality_inventory.txt"

cd "$ROOT"
test -f wp-config.php

: > "$REPORT_REMOTE"

log() {
  echo "$@" | tee -a "$REPORT_REMOTE"
}

log "== BEFORE THEME =="
ACTIVE_BEFORE="$(wp theme list --status=active --field=name --skip-plugins --skip-themes | tail -n 1 || true)"
log "ACTIVE_BEFORE=$ACTIVE_BEFORE"

log "== INSTALL THEME =="
wp theme install "$ZIP_REMOTE" --force --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE"

log "== ACTIVATE THEME =="
wp theme activate "$THEME_SLUG" --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE"

ACTIVE_AFTER="$(wp theme list --status=active --field=name --skip-plugins --skip-themes | tail -n 1 || true)"
log "ACTIVE_AFTER=$ACTIVE_AFTER"
test "$ACTIVE_AFTER" = "$THEME_SLUG"

log "== SET HOMEPAGE =="
PAGE_ID="$(wp post list --post_type=page --name=the-emergence --field=ID --format=ids --skip-plugins --skip-themes | tail -n 1)"
test -n "$PAGE_ID"
wp option update show_on_front page --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE"
wp option update page_on_front "$PAGE_ID" --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE"
log "HOMEPAGE_SET=PASS id=$PAGE_ID"

log "== PAGE INVENTORY =="
wp post list --post_type=page --fields=ID,post_title,post_name,post_status --format=table --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE"

log "== RELEVANT PAGE INVENTORY =="
wp post list --post_type=page --fields=ID,post_title,post_name,post_status --format=table --skip-plugins --skip-themes | grep -Ei 'spark|quiz|generator|awakening|emergence|battle' | tee -a "$REPORT_REMOTE" || true

log "== PLUGIN INVENTORY =="
wp plugin list --fields=name,status,version --format=table --skip-themes | tee -a "$REPORT_REMOTE"

log "== RELEVANT PLUGIN INVENTORY =="
wp plugin list --fields=name,status,version --format=table --skip-themes | grep -Ei 'spark|quiz|form|acf|shortcode|battle|emergence|hostinger|elementor|generator' | tee -a "$REPORT_REMOTE" || true

log "== SHORTCODE INVENTORY =="
wp post list --post_type=page --post_status=any --format=ids --skip-plugins --skip-themes | while read -r id; do
  [ -n "$id" ] || continue
  title="$(wp post get "$id" --field=post_title --skip-plugins --skip-themes || true)"
  slug="$(wp post get "$id" --field=post_name --skip-plugins --skip-themes || true)"
  content="$(wp post get "$id" --field=post_content --skip-plugins --skip-themes || true)"
  echo "$content" | grep -Eo '\[[a-zA-Z0-9_-]+' | sort -u | sed "s#^#SHORTCODE page_id=$id slug=$slug title=$title code=#" | tee -a "$REPORT_REMOTE" || true
done

log "== FILE INVENTORY =="
find wp-content -maxdepth 6 -type f \
  \( -iname '*spark*' -o -iname '*quiz*' -o -iname '*battle*' -o -iname '*emergence*' -o -iname '*generator*' \) \
  2>/dev/null | sort | head -240 | tee -a "$REPORT_REMOTE" || true

log "== DETECT SHORTCODES FROM PLUGIN PHP =="
grep -RIn "add_shortcode" wp-content/plugins wp-content/themes 2>/dev/null | grep -Ei 'spark|battle|emergence|quiz|generator' | head -120 | tee -a "$REPORT_REMOTE" || true

log "== ROUTE RESTORE FROM DETECTED SHORTCODES =="
SHORTCODES="$(grep -Rho "add_shortcode([^)]*)" wp-content/plugins wp-content/themes 2>/dev/null | grep -Eo "['\"][a-zA-Z0-9_-]+['\"]" | tr -d "'\"" | sort -u | grep -Ei 'spark|battle|emergence|quiz|generator' || true)"
echo "$SHORTCODES" | sed 's/^/DETECTED_SHORTCODE=/' | tee -a "$REPORT_REMOTE" || true

restore_page() {
  local title="$1"
  local slug="$2"
  local shortcode="$3"

  local existing
  existing="$(wp post list --post_type=page --name="$slug" --field=ID --format=ids --skip-plugins --skip-themes | tail -n 1 || true)"

  local content
  content="[$shortcode]"

  if [ -n "$existing" ]; then
    log "RESTORE_PAGE_EXISTS slug=$slug id=$existing shortcode=$shortcode"
    wp post update "$existing" --post_title="$title" --post_name="$slug" --post_status=publish "--post_content=$content" --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE"
  else
    log "RESTORE_PAGE_CREATE slug=$slug shortcode=$shortcode"
    wp post create --post_type=page --post_title="$title" --post_name="$slug" --post_status=publish "--post_content=$content" --porcelain --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE"
  fi
}

if echo "$SHORTCODES" | grep -qx "spark_generator"; then
  restore_page "Spark Generator" "spark-generator" "spark_generator"
elif echo "$SHORTCODES" | grep -qx "emergence_character_generator"; then
  restore_page "Spark Generator" "spark-generator" "emergence_character_generator"
else
  log "SPARK_GENERATOR_SHORTCODE_NOT_DETECTED"
fi

if echo "$SHORTCODES" | grep -qx "spark_battle_sim"; then
  restore_page "Spark Battle Sim" "spark-battle-sim" "spark_battle_sim"
elif echo "$SHORTCODES" | grep -qx "spark_battle"; then
  restore_page "Spark Battle Sim" "spark-battle-sim" "spark_battle"
else
  log "SPARK_BATTLE_SHORTCODE_NOT_DETECTED"
fi

log "== FLUSH CACHE =="
wp cache flush --skip-plugins --skip-themes | tee -a "$REPORT_REMOTE" || true

log "REMOTE_THEME_RESTORE=PASS"
