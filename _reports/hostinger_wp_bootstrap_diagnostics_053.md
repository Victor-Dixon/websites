# Hostinger WP Bootstrap Diagnostics 053

## Target

- domain: `freerideinvestor.com`
- wp_root: `/home/u996867598/domains/freerideinvestor.com/public_html`

## Diagnostics
```text

== SSH ==
$ echo HOSTINGER_SSH_LOGIN=PASS && pwd
HOSTINGER_SSH_LOGIN=PASS
/home/u996867598
RC=0

== WP ROOT ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && pwd && test -f wp-config.php && echo WP_CONFIG=PASS && ls -la wp-config.php
/home/u996867598/domains/freerideinvestor.com/public_html
WP_CONFIG=PASS
-rw-r--r-- 1 u996867598 o1008028115 3663 May 28 10:57 wp-config.php
RC=0

== PHP VERSION ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && php -v || /opt/alt/php82/usr/bin/php -v
PHP 8.2.30 (cli) (built: Mar  5 2026 00:00:00) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.30, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.30, Copyright (c), by Zend Technologies
RC=0

== WP INFO ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp --info
OS:	Linux 5.14.0-611.41.1.el9_7.x86_64 #1 SMP PREEMPT_DYNAMIC Thu Mar 19 03:50:11 EDT 2026 x86_64
Shell:	/bin/bash
PHP binary:	/opt/alt/php82/usr/bin/php
PHP version:	8.2.30
php.ini used:	/opt/alt/php82/etc/php.ini
MySQL binary:	/usr/bin/mariadb
MySQL version:	mariadb from 11.8.6-MariaDB, client 15.2 for Linux (x86_64) using  EditLine wrapper
SQL modes:	
WP-CLI root dir:	phar://wp-cli.phar/vendor/wp-cli/wp-cli
WP-CLI vendor dir:	phar://wp-cli.phar/vendor
WP_CLI phar path:	phar:///usr/local/bin/wp-cli-2.12.0.phar
WP-CLI packages dir:	
WP-CLI cache dir:	/home/u996867598/.wp-cli/cache
WP-CLI global config:	
WP-CLI project config:	
WP-CLI version:	2.12.0
RC=0

== CONFIG DB NAME ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp config get DB_NAME --skip-plugins --skip-themes --debug
RC=255

== CONFIG TABLE PREFIX ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp config get table_prefix --skip-plugins --skip-themes --debug
RC=255

== DB CHECK ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp db check --skip-plugins --skip-themes --debug
RC=255

== CORE IS INSTALLED DEBUG ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp core is-installed --skip-plugins --skip-themes --debug
RC=255

== OPTION SITEURL DEBUG ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp option get siteurl --skip-plugins --skip-themes --debug
RC=255

== OPTION HOME DEBUG ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp option get home --skip-plugins --skip-themes --debug
RC=255

== PLUGIN LIST SKIP ==
$ cd '/home/u996867598/domains/freerideinvestor.com/public_html' && wp plugin list --skip-plugins --skip-themes --debug
RC=255
```

## Status

STATUS=DIAGNOSTIC_COMPLETE
