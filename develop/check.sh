#!/bin/bash
# Führt lokal dieselben Prüfungen wie das CI (phpbb-extensions/test-framework@3.3.x) aus:
# Code Sniffer, Executable-Check, ICC-Profile, EPV (falls installiert) und die Unit-Tests (SQLite).
# Nutzung: develop/check.sh
set -e
EXTNAME=gn36/hookup
EXT="$(cd "$(dirname "$0")/.." && pwd)"
CORE="${PHPBB_DIR:-/opt/phpbb-core}"
DEST="$CORE/phpBB/ext/$EXTNAME"
rm -rf "$DEST" && mkdir -p "$DEST"
cp -a "$EXT/." "$DEST/" && rm -rf "$DEST/.git"
cd "$CORE"

echo "== Code Sniffer"
phpBB/vendor/bin/phpcs -s --extensions=php --standard=build/code_sniffer/ruleset-php-extensions.xml \
  --ignore=*/"$EXTNAME"/tests/*,*/"$EXTNAME"/vendor/* phpBB/ext/"$EXTNAME"

echo "== Executable files"
.github/ext-check-executable-files.sh ./ "$EXTNAME"

echo "== ICC profiles"
if command -v exiftool >/dev/null; then
  .github/check-image-icc-profiles.sh
else
  echo "exiftool nicht installiert, übersprungen"
fi

echo "== EPV"
if [ -x phpBB/vendor/bin/EPV.php ]; then
  phpBB/vendor/bin/EPV.php run --dir="phpBB/ext/$EXTNAME/"
else
  echo "EPV nicht installiert, übersprungen"
fi

echo "== Unit tests"
"$EXT/develop/run-tests.sh"
