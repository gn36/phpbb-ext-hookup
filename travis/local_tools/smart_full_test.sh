#!/bin/bash
#
# Performs a full travis-like test on the extension.
# Please adjust settings to ensure that this works.
#
# This crude script assumes the following setup:
# /
#   extensions/
#     gn36/hookup/
#   test/
#     testdir/ (will be created automatically)
#     smart_full_test.sh
#     phpunittest.sh
#     full_test.sh
#     test_config.php (optional)
#
# Testing done by this script is very similar to travis testing.
# The script was created based upon a travis configuration. 
# This leads to some weird constructions of folders sometimes.
# 
# (c) 2016 - Martin Beckmann <gn#36@phpbb.de>
# License: GPL v2
# 

# Environment:
EXTNAME="gn36/hookup"  # CHANGE name of the extension HERE
EXTBRANCH="dev/tests"		# Branch to test (not used)
SNIFF="1"            # Should we run code sniffer on your code?
IMAGE_ICC="1"        # Should we run icc profile sniffer on your images?
EPV="1"              # Should we run EPV (Extension Pre Validator) on your code?
LANG_TEST="0"		 # Should we validate the language files?
PHPBB_BRANCH="3.1.x" # Which phpBB branch to check out

DB=mysqli
TRAVIS_PHP_VERSION=5.5.9

# Go into testdir and copy the extension:
EXTDIR=${EXTNAME%/*}
if [ ! -e testdir ]; then
	mkdir testdir
else
	# Existing test dir, delete a few things to make sure everything works out correctly
	rm -rf testdir/$EXTNAME
	rm -rf testdir/phpBB3/phpBB/ext/$EXTNAME
	rm -rf testdir/phpBB3/phpBB/ext/$EXTDIR
	rm -rf testdir/phpBB3/phpBB/cache/*
	cp testdir/phpBB3/phpBB/ext/index.htm testdir/phpBB3/phpBB/cache/
	rm -rf testdir/tmp

	# Not sure if this is really necessary
	dbs=$(mysql -e "SET SESSION group_concat_max_len=1000000; SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' ) AS statement FROM information_schema.tables WHERE table_schema = 'phpbb_tests' AND table_name LIKE 'phpbb_%';")
	cleaned=$(echo "$dbs" | sed 's/statement//g')
	mysql -e "USE phpbb_tests; $cleaned"
fi
cd testdir
if [ -e $EXTDIR ]; then
	rm -rf $EXTDIR
fi
mkdir $EXTDIR

# Copy instead of cloning (on some systems, cloning may be faster?)
cp -r ../../extensions/$EXTNAME $EXTDIR
#git clone --depth=1 ../../extensions/$EXTNAME $EXTNAME --branch=$EXTBRANCH
cd $EXTNAME

# Install
#php composer.phar install --dev --no-interaction --prefer-source
php composer.phar install --no-interaction
if [ ! -e ../../phpBB3 ]; then
	chmod +x travis/prepare-phpbb.sh
	travis/prepare-phpbb.sh $EXTNAME $PHPBB_BRANCH
	if [ "$LANG_TEST" != '0' ]; then
		travis/prepare-langtest.sh $EXTNAME $PHPBB_BRANCH
	fi
	cd ../../phpBB3
else
	if [ "$LANG_TEST" != '0' ]; then
		travis/prepare-langtest.sh $EXTNAME $PHPBB_BRANCH
	fi
	if [ ! -e ../../tmp ]; then
		# This is from prepare-phpbb.sh
		mkdir ../../tmp
		cp -R . ../../tmp
	fi
	cd ../../phpBB3
	git pull origin
fi

travis/prepare-extension.sh $EXTNAME $PHPBB_BRANCH
travis/setup-phpbb.sh $DB $TRAVIS_PHP_VERSION
travis/install-phpbb-test-dependencies.sh
cd phpBB
#php ../composer.phar install --dev --no-interaction --prefer-source
php ../composer.phar install --no-interaction 
cd ..

# Preparation:
travis/setup-database.sh $DB $TRAVIS_PHP_VERSION

# Database setup:
if [ -e ../../../test_config.php ]; then
	cp ../../../test_config.php ../tests/
fi

# script:
sh -c "if [ '$SNIFF' != '0' ]; then travis/ext-sniff.sh $DB $TRAVIS_PHP_VERSION $EXTNAME; fi"
sh -c "if [ '$IMAGE_ICC' != '0' ]; then travis/check-image-icc-profiles.sh $DB $TRAVIS_PHP_VERSION; fi"
# This would be default, but the default sql user config is kind of stupid in some setups. 
# So use the project unittest file and a config file instead:
#php phpBB/vendor/bin/phpunit --configuration phpBB/ext/$EXTNAME/travis/phpunit-$DB-travis.xml --bootstrap ./tests/bootstrap.php
php phpBB/vendor/bin/phpunit --configuration phpBB/ext/$EXTNAME/phpunit.xml.dist --bootstrap ./tests/bootstrap.php
sh -c "if [ '$EPV' != '0' ]  && [ '$DB' = 'mysqli' ]; then phpBB/ext/$EXTNAME/vendor/bin/EPV.php run --dir='phpBB/ext/$EXTNAME/'; fi"
sh -c "if [ '$LANG_TEST' != '0' ]; then ../langtest/travis/test-lang.sh;  fi"
