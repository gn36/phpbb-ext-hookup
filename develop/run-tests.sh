#!/bin/bash
# Führt die Extension-Tests gegen den phpBB-Checkout aus (SQLite, ohne DB-Server).
# Nutzung: develop/run-tests.sh [phpunit-Argumente]
set -e
EXT="$(cd "$(dirname "$0")/.." && pwd)"
CORE="${PHPBB_DIR:-/opt/phpbb-core}"
DEST="$CORE/phpBB/ext/gn36/hookup"
rm -rf "$DEST" && mkdir -p "$DEST"
cp -a "$EXT/." "$DEST/" && rm -rf "$DEST/.git"
cd "$CORE"
export PHPBB_TEST_DBMS=sqlite3 PHPBB_TEST_DBHOST=/tmp/phpbb_test.sqlite
php -d error_reporting=24575 phpBB/vendor/bin/phpunit \
  --configuration phpBB/ext/gn36/hookup/phpunit.xml.dist \
  "${@:---testsuite=Extension Test Suite}"
