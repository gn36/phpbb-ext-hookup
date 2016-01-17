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
	'HOOKUP_NOTIFY_BASE_OPTION'					=> 'Receive a generic Hookup Notification.',
	'HOOKUP_NOTIFY_ACTIVE_DATE_SET_OPTION'		=> 'The active date of a meeting you participate in has been set',
	'HOOKUP_NOTIFY_ACTIVE_DATE_RESET_OPTION'	=> 'A meeting planner you participate in has its active date reset',
	'HOOKUP_NOTIFY_DATE_ADDED_OPTION'			=> 'New date options have been added to a meeting planner you participate in',
	'HOOKUP_NOTIFY_USER_ADDED_OPTION'			=> 'A new user has been added to a meeting planner you participate in',
	'HOOKUP_NOTIFY_INVITED_OPTION'				=> 'You have been invited to participate in a meeting planner',
	'NOTIFICATION_GROUP_HOOKUP'					=> 'Hookup Notifications',
));
