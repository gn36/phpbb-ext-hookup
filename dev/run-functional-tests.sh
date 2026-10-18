#!/bin/bash
# Führt die Funktionstests headless aus: startet den PHP-Entwicklungsserver auf dem phpBB-Checkout,
# installiert die Erweiterungs-Abhängigkeiten (vendor/) falls nötig und ruft dev/run-tests.sh auf.
set -e
EXT="$(cd "$(dirname "$0")/.." && pwd)"
CORE="${PHPBB_DIR:-/opt/phpbb-core}"
PORT="${PORT:-8000}"
[ -d "$EXT/vendor" ] || (cd "$EXT" && composer install --no-interaction)
php -S "127.0.0.1:$PORT" -t "$CORE" >/dev/null 2>&1 &
SERVER=$!
trap 'kill $SERVER 2>/dev/null' EXIT
sleep 1
export PHPBB_FUNCTIONAL_URL="http://127.0.0.1:$PORT/phpBB/"
"$EXT/dev/run-tests.sh" --testsuite="Extension Functional Tests" "$@"
