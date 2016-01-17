<?php
/**
 *
 * hookup [English]
 *
 * @package language
 * @copyright (c) 2015 gn#36 (Martin Beckmann)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 *
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

$lang = array_merge($lang, array(
	// Notifications
	'HOOKUP_NOTIFY_BASE'				=> 'This is a generic Hookup Notification.',
	'HOOKUP_NOTIFY_ACTIVE_DATE_SET'		=> 'The <strong>active date</strong> was set to <strong>%3$s</strong> for %2$s by %1$s (%4$s yes, %5$s no, %6$s maybe).',
	'HOOKUP_NOTIFY_ACTIVE_DATE_RESET'	=> 'The <strong>active date</strong> for %2$s was <strong>reset</strong> by %1$s.',
	'HOOKUP_NOTIFY_DATE_ADDED'			=> 'New date options have been added to %2$s by %1$s.',
	'HOOKUP_NOTIFY_USER_ADDED'			=> 'A new user %1$s has been added to %2$s.',
	'HOOKUP_NOTIFY_INVITED'				=> 'You have been <strong>invited</strong> to participate in %2$s by %1$s.'
));
