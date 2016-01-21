<?php

// Enter URL to test-forum here:
$phpbb_functional_url = 'http://localhost/test/testdir/phpBB3/phpBB/';

// Enter correct db data here:
// You might need to adjust smart_full_test.sh to avoid accidentally deleting the wrong db...
$dbms = 'phpbb\\db\\driver\\mysqli';
$dbhost = '';
$dbport = '';
$dbname = 'phpbb_tests';
$dbuser = 'phpbb_test_user';
$dbpasswd = 'thepassword';
$table_prefix = 'phpbb_'; // Changing this might cause some trouble, some testcases might need adjustment?
$phpbb_adm_relative_path = 'adm/';
$acm_type = 'phpbb\\cache\\driver\\file';
