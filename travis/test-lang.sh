#!/bin/bash
#
# This file is part of the hookup extension for phpBB
#
# @copyright (c) gn#36
# @license GNU General Public License, version 2 (GPL-2.0)
#
#
#set -e
#set -x

echo "Running language tests"
# we should be in phpBB3, so we need to go up one level into the langtest dir:
cd ../langtest ; 

for i in $(ls language/)
do 
	php vendor/bin/PhpbbTranslationValidator.php validate --language-dir=language $i
	echo ""; 
done;

cd ../phpBB3
