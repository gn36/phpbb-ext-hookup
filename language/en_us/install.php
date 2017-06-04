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
	// Overload enable message to explain the use of the extension:
	'EXTENSION_ENABLE_SUCCESS'	=> 'The extension was enabled successfully.<br><br> This extension has <strong>no special configuration page</strong> in the ACP. <br><br> You can use the <strong>new permission</strong> »Can add hookup meetings« to control for each forum, which users can or cannot attach a meeting planner to a topic in that forum. <br><br> You can then <strong>create a meeting planner</strong> by marking the appropriate checkbox below the posting textarea when creating a new topic or editing the first post of an existing topic.',
));
