<?php
/**
*
* @package language
* @copyright (c) 2015 gn#36
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/


/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Define categories and permission types
$lang = array_merge($lang, array(
	'WRONG_PHP_VERSION' 		=> 'Your PHP version is incompatible with this extension.',
	'WRONG_PHPBB_VERSION' 		=> 'Your phpBB version is incompatible with this extension.',
	'WRONG_EXTENSION_VERSION' 	=> 'The version of the <strong>%s</strong> extension is incompatible with this extension.',
	'MISSING_DEPENDENCIES' 		=> 'Dependencies of this extension are missing. Please use composer to install the missing dependencies or use a complete installation package.',
	'MISSING_EXTENSION'			=> 'To install this extension, the extension <strong>%s</strong> is required.',
));
