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
	'HOOKUP_NOTIFY_BASE_OPTION'					=> 'Empfange eine generische Hookup Benachrichtigung.',
	'HOOKUP_NOTIFY_ACTIVE_DATE_SET_OPTION'		=> 'Das aktive Datum eines Terminplaners an dem Sie teilnehmen wurde festgesetzt',
	'HOOKUP_NOTIFY_ACTIVE_DATE_RESET_OPTION'	=> 'Das aktive Datum eines Terminplaners an dem Sie teilnehmen wurde zurückgesetzt',
	'HOOKUP_NOTIFY_DATE_ADDED_OPTION'			=> 'Neue Datumsoptionen wurden zu einem Terminplaner an dem Sie teilnehmen hinzugefügt',
	'HOOKUP_NOTIFY_USER_ADDED_OPTION'			=> 'Ein neuer Benutzer wurde zu einem Terminplaner an dem Sie teilnehmen hinzugefügt',
	'HOOKUP_NOTIFY_DATE_ADDED_ROTATION_OPTION'	=> 'Neue wöchentliche Datumsoptionen wurden zu einem Terminplaner an dem Sie teilnehmen hinzugefügt',
	'HOOKUP_NOTIFY_INVITED_OPTION'				=> 'Sie wurden zu einem Terminplaner eingeladen',
	'NOTIFICATION_GROUP_HOOKUP'					=> 'Hookup Benachrichtigungen',
));
