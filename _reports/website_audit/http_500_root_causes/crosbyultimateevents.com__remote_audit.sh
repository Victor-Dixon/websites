#!/usr/bin/env bash
set -euo pipefail

ROOT="$1"
DOMAIN="$2"

echo "DOMAIN=$DOMAIN"
echo "REMOTE_ROOT=$ROOT"

if [ ! -d "$ROOT" ]; then
  echo "ROOT_EXISTS=NO"
  exit 0
fi

echo "ROOT_EXISTS=YES"

cd "$ROOT"

echo "== ROOT_STAT =="
pwd
ls -la

echo "== COUNTS =="
echo "FILE_COUNT=$(find . -type f | wc -l | tr -d ' ')"
echo "DIR_COUNT=$(find . -type d | wc -l | tr -d ' ')"

echo "== PERMISSIONS =="
stat -c 'ROOT_MODE=%a ROOT_OWNER=%U ROOT_GROUP=%G' "$ROOT" 2>/dev/null || true
find . -maxdepth 1 -printf '%M %u %g %p\n' 2>/dev/null | sort || true

echo "== INDEX_FILES =="
for f in index.php index.html index.htm default.php default.html; do
  if [ -f "$f" ]; then
    echo "INDEX_PRESENT=$f"
    echo "INDEX_MODE=$(stat -c '%a' "$f" 2>/dev/null || true)"
    echo "INDEX_HEAD_BEGIN=$f"
    head -40 "$f" 2>/dev/null || true
    echo "INDEX_HEAD_END=$f"
  fi
done

echo "== HTACCESS =="
if [ -f .htaccess ]; then
  echo "HTACCESS_PRESENT=YES"
  echo "HTACCESS_MODE=$(stat -c '%a' .htaccess 2>/dev/null || true)"
  echo "HTACCESS_BEGIN"
  sed -n '1,160p' .htaccess 2>/dev/null || true
  echo "HTACCESS_END"
else
  echo "HTACCESS_PRESENT=NO"
fi

echo "== WORDPRESS_MARKERS =="
if [ -f wp-config.php ]; then echo "WP_CONFIG=YES"; else echo "WP_CONFIG=NO"; fi
if [ -d wp-content ]; then echo "WP_CONTENT=YES"; else echo "WP_CONTENT=NO"; fi
if [ -d wp-admin ]; then echo "WP_ADMIN=YES"; else echo "WP_ADMIN=NO"; fi
if [ -f wp-load.php ]; then echo "WP_LOAD=YES"; else echo "WP_LOAD=NO"; fi
if [ -f wp-config.php ]; then
  echo "WP_CONFIG_HEAD_BEGIN"
  grep -E "DB_NAME|DB_USER|WP_DEBUG|table_prefix|ABSPATH" wp-config.php 2>/dev/null | sed 's/password.*/password REDACTED/I' || true
  echo "WP_CONFIG_HEAD_END"
fi

echo "== PHP_MARKERS =="
find . -maxdepth 2 -type f \( -name '*.php' -o -name 'composer.json' -o -name '.user.ini' -o -name 'php.ini' \) | sort | head -100

echo "== COMMON_ERROR_LOGS =="
for log in error_log php_errorlog debug.log wp-content/debug.log logs/error.log; do
  if [ -f "$log" ]; then
    echo "LOG_PRESENT=$log"
    echo "LOG_TAIL_BEGIN=$log"
    tail -80 "$log" 2>/dev/null || true
    echo "LOG_TAIL_END=$log"
  fi
done

echo "== DOMAIN_LEVEL_LOG_SEARCH =="
for base in "$HOME/domains/$DOMAIN/logs" "$HOME/domains/$DOMAIN" "$HOME/logs"; do
  if [ -d "$base" ]; then
    echo "LOG_DIR=$base"
    find "$base" -maxdepth 2 -type f \( -iname '*error*' -o -iname '*.log' \) 2>/dev/null | head -20
  fi
done

echo "== TOP_FILES =="
find . -maxdepth 3 -type f | sort | head -160

echo "== TOP_DIRS =="
find . -maxdepth 3 -type d | sort | head -160

echo "REMOTE_500_AUDIT=PASS"
