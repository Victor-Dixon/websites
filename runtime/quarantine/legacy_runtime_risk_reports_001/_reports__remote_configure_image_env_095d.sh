#!/usr/bin/env bash
set -euo pipefail

WP_ROOT='/home/u996867598/domains/dadudekc.site/public_html'
PLUGIN_SLUG='emergence-character-generator'
REMOTE_PLUGIN_DIR='/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator'
REMOTE_TARBALL='/tmp/emergence-character-generator_095d.tar.gz'
REMOTE_KEY_JSON='/tmp/emergence_image_env_payload_095d.json'
REMOTE_BACKUP_DIR='/home/u996867598/domains/dadudekc.site/public_html/dreamos_backups/image_env_095d_20260531_121122'

mkdir -p "$REMOTE_BACKUP_DIR"
cp "$WP_ROOT/wp-config.php" "$REMOTE_BACKUP_DIR/wp-config.php.bak"

if [ -d "$REMOTE_PLUGIN_DIR" ]; then
  tar -czf "$REMOTE_BACKUP_DIR/$PLUGIN_SLUG-existing.tar.gz" -C "$WP_ROOT/wp-content/plugins" "$PLUGIN_SLUG"
fi
echo "REMOTE_BACKUP=PASS"

rm -rf "$REMOTE_PLUGIN_DIR"
mkdir -p "$REMOTE_PLUGIN_DIR"
tar -xzf "$REMOTE_TARBALL" -C "$REMOTE_PLUGIN_DIR"
rm -f "$REMOTE_TARBALL"
chmod -R u=rwX,go=rX "$REMOTE_PLUGIN_DIR"

/opt/alt/php82/usr/bin/php -l "$REMOTE_PLUGIN_DIR/emergence-character-generator.php"
echo "REMOTE_PHP_LINT=PASS"

cd "$WP_ROOT"

python3 - << 'PY'
from pathlib import Path
import json

wp = Path("wp-config.php")
payload = Path("/tmp/emergence_image_env_payload_095d.json")

text = wp.read_text()
data = json.loads(payload.read_text())
payload.unlink(missing_ok=True)

key_value = data.get("key", "")
if not key_value:
    raise SystemExit("REMOTE_KEY_VALUE_EMPTY")

defs = {
    "EMERGENCE_IMAGE_PROVIDER": "openai",
    "EMERGENCE_IMAGE_LIVE": "1",
    "EMERGENCE_IMAGE_MODEL": "gpt-image-1",
    "EMERGENCE_IMAGE_SIZE": "1024x1024",
    "EMERGENCE_IMAGE_QUALITY": "medium",
    "EMERGENCE_IMAGE_API_KEY": key_value,
}

lines = []
for name, value in defs.items():
    lines.append(f"define('{name}', {json.dumps(value)});")

block = "\n// DREAMOS_EMERGENCE_IMAGE_ENV_BEGIN\n" + "\n".join(lines) + "\n// DREAMOS_EMERGENCE_IMAGE_ENV_END\n"

begin = "// DREAMOS_EMERGENCE_IMAGE_ENV_BEGIN"
end = "// DREAMOS_EMERGENCE_IMAGE_ENV_END"

if begin in text and end in text:
    start = text.index(begin)
    finish = text.index(end) + len(end)
    text = text[:start].rstrip() + block + text[finish:]
else:
    anchor = "/* That's all, stop editing!"
    if anchor in text:
        idx = text.index(anchor)
        text = text[:idx].rstrip() + block + "\n\n" + text[idx:]
    else:
        text = text.rstrip() + block + "\n"

wp.write_text(text)
PY

grep -F 'DREAMOS_EMERGENCE_IMAGE_ENV_BEGIN' wp-config.php >/dev/null
grep -F "define('EMERGENCE_IMAGE_PROVIDER', \"openai\");" wp-config.php >/dev/null
grep -F "define('EMERGENCE_IMAGE_LIVE', \"1\");" wp-config.php >/dev/null
grep -F "define('EMERGENCE_IMAGE_MODEL', \"gpt-image-1\");" wp-config.php >/dev/null
echo "WP_CONFIG_CONSTANTS=PASS"

wp plugin activate "$PLUGIN_SLUG" --skip-plugins --skip-themes || true
wp plugin is-active "$PLUGIN_SLUG" --skip-plugins --skip-themes
echo "PLUGIN_ACTIVE=PASS"

wp cache flush || true
if wp plugin is-active litespeed-cache >/dev/null 2>&1; then
  wp litespeed-purge all || true
  echo "LITESPEED_PURGE=PASS"
fi

echo "REMOTE_ENV_CONFIG=PASS"
