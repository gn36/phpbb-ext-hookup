#!/bin/bash

# Environment:
#EXTNAME="phpbbde/pastebin"  # CHANGE name of the extension HERE
EXTNAME="gn36/hookup"  # CHANGE name of the extension HERE
#EXTBRANCH="dev/translation-validator"		# Branch to test
EXTBRANCH="dev/tests"		# Branch to test
SNIFF="1"            # Should we run code sniffer on your code?
IMAGE_ICC="1"        # Should we run icc profile sniffer on your images?
EPV="1"              # Should we run EPV (Extension Pre Validator) on your code?
LANG_TEST="0"
PHPBB_BRANCH="3.1.x"

DB=mysqli
TRAVIS_PHP_VERSION=5.5.9

# Go into testdir and copy the extension:
EXTDIR=${EXTNAME%/*}
if [ ! -e testdir ]; then
	mkdir testdir
fi
cd testdir
if [ -e $EXTDIR ]; then
	rm -rf $EXTDIR
fi
mkdir $EXTDIR
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

# Functional tests:
if [ -e ../../../test_config.php ]; then
	cp ../../../test_config.php ../tests/
fi

# script:
sh -c "if [ '$SNIFF' != '0' ]; then travis/ext-sniff.sh $DB $TRAVIS_PHP_VERSION $EXTNAME; fi"
sh -c "if [ '$IMAGE_ICC' != '0' ]; then travis/check-image-icc-profiles.sh $DB $TRAVIS_PHP_VERSION; fi"
#php phpBB/vendor/bin/phpunit --configuration phpBB/ext/$EXTNAME/travis/phpunit-$DB-travis.xml --bootstrap ./tests/bootstrap.php
php phpBB/vendor/bin/phpunit --configuration phpBB/ext/$EXTNAME/phpunit.xml.dist --bootstrap ./tests/bootstrap.php
sh -c "if [ '$EPV' != '0' ]  && [ '$DB' = 'mysqli' ]; then phpBB/ext/$EXTNAME/vendor/bin/EPV.php run --dir='phpBB/ext/$EXTNAME/'; fi"
sh -c "if [ '$LANG_TEST' != '0' ]; then ../langtest/travis/test-lang.sh;  fi"
