#!/bin/bash
# Setup-Script für eine Claude-Cloud-Umgebung zum Testen von phpBB-3.3-Erweiterungen.
# Installiert (möglichst versionsunabhängig): MariaDB, PHP-Erweiterungen, phpBB 3.3.x inkl. PHPUnit 9.
# Wird bei Umgebungen mit Setup-Script einmal ausgeführt und im Ergebnis zwischengespeichert.
set -euo pipefail

PHPBB_BRANCH="${PHPBB_BRANCH:-3.3.x}"
PHPBB_DIR="${PHPBB_DIR:-/opt/phpbb-core}"

export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
# php-cli ist im Basis-Image meist vorhanden; die Pakete ohne Versionsnummer nehmen die Standard-PHP-Version der Distribution.
apt-get install -y -qq git unzip rsync mariadb-server \
  php-cli php-mbstring php-xml php-zip php-gd php-curl php-intl php-mysql php-sqlite3 php-pgsql

# Datenbank für die phpBB-Tests (MariaDB); SQLite geht auch ohne Server.
service mariadb start || service mysql start || true
for i in $(seq 1 20); do mysqladmin ping --silent && break; sleep 1; done
mysql -e "CREATE DATABASE IF NOT EXISTS phpbb_tests CHARACTER SET utf8mb4;
          CREATE USER IF NOT EXISTS 'phpbb'@'localhost' IDENTIFIED BY 'phpbb';
          GRANT ALL ON phpbb_tests.* TO 'phpbb'@'localhost';"

# phpBB-Quellcode (aktueller Stand des 3.3-Branches)
if [ ! -d "$PHPBB_DIR/.git" ]; then
  git clone --depth 1 -b "$PHPBB_BRANCH" https://github.com/phpbb/phpbb "$PHPBB_DIR"
fi
cd "$PHPBB_DIR/phpBB"
composer install --no-interaction --no-progress
# Wie phpBBs eigenes CI unter PHP 8: PHPUnit 9 statt 7
composer remove phpunit/dbunit --dev --update-with-dependencies --no-interaction
composer require symfony/yaml:^4.4 misantron/dbunit:5.2.1 phpunit/phpunit:^9.3 doctrine/instantiator:^1.4 \
  --dev --update-with-all-dependencies --ignore-platform-reqs --no-interaction
