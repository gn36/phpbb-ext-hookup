#!/bin/bash
# This script assumes a working test setup in testdir (e.g. setup by one of the other scripts)
# It also assumes the extensions to be tested to be located in ../extensions/vendor/name (relative to this script)
# Please make sure this is correct, otherwise testing won't work
# For db and functional tests, the test_config.php is required to be in the same folder.
# 
# (c) 2016 - Martin Beckmann <gn#36@phpbb.de>
# License: GPL v2
# 

# Environment:
export EXTNAME="gn36/hookup" # Name of extension

# Copy the current version of the ext for testing:
EXTDIR=${EXTNAME%/*}
if [ ! -e testdir ]; then
	mkdir testdir
fi
cd testdir/phpBB3/phpBB/ext/
if [ -e $EXTDIR ]; then
	rm -rf $EXTDIR 2>/dev/null
fi
mkdir $EXTDIR
#cp -r ../../../../../extensions/$EXTNAME $EXTDIR
cp -r ../../../../../phpbb/phpBB/ext/$EXTNAME $EXTDIR

#cp -r ../../../../../extensions/$EXTNAME/event $EXTNAME
cp -r ../../../../../phpbb/phpBB/ext/$EXTNAME/event $EXTNAME

cd ../../
pwd

# Functional and database tests require test_config, make sure it's there:
if [ -e ../../test_config.php ]; then
	cp ../../test_config.php tests/
fi

# Use installed phpunit version (may be fairly old)
#phpunit -c phpBB/ext/$EXTNAME/phpunit.xml.dist |sed "s/\[^[[0-9;]*[a-zA-Z]//gi"
#phpunit -c phpBB/ext/$EXTNAME/travis/phpunit-mysqli-travis.xml

# Use phpBB dependency phpunit:
phpBB/vendor/bin/phpunit -c phpBB/ext/$EXTNAME/phpunit.xml.dist
