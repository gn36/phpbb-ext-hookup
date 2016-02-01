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
	'HOOKUP_NOTIFY_BASE_OPTION'					=> 'Réception d’une notification du planificateur d’événement.',
	'HOOKUP_NOTIFY_ACTIVE_DATE_SET_OPTION'		=> 'La date d’un événement auquel vous participez a été définie.',
	'HOOKUP_NOTIFY_ACTIVE_DATE_RESET_OPTION'	=> 'Un événement auquel vous participez a vu sa date révisée.',
	'HOOKUP_NOTIFY_DATE_ADDED_OPTION'			=> 'De nouvelles options de date ont été proposé pour un événement auquel vous avez été invité.',
	'HOOKUP_NOTIFY_USER_ADDED_OPTION'			=> 'Un nouveau participant a été ajouté à un événement auquel vous avez été invité.',
	'HOOKUP_NOTIFY_INVITED_OPTION'				=> 'Vous avez été invité a participer à un événement.',
	'NOTIFICATION_GROUP_HOOKUP'					=> 'Notification d’événement.',
));
