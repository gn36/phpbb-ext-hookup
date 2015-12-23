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
	'HOOKUP_NOTIFY_BASE'				=> 'Das hier ist eine generische Hookup Benachrichtigung.',
	'HOOKUP_NOTIFY_ACTIVE_DATE_SET'		=> 'Das <strong>aktive Datum</strong> wurde auf <strong>%3$s</strong> durch %1$s gesetzt für %2$s  (%4$s ja, %5$s nein, %6$s evtl.).',
	'HOOKUP_NOTIFY_ACTIVE_DATE_RESET'	=> 'Das <strong>aktive Datum</strong> für %2$s wurde durch %1$s <strong>zurückgesetzt</strong>.',
	'HOOKUP_NOTIFY_DATE_ADDED'			=> 'Neue Datumsoptionen wurden von %1$s zu  %2$s hinzugefügt.',
	'HOOKUP_NOTIFY_USER_ADDED'			=> 'Ein neuer Benutzer %1$s wurde zu %2$s hinzugefügt.',
	'HOOKUP_NOTIFY_INVITED'				=> 'Sie wurden von %1$s zum Terminplaner in %2$s <strong>eingeladen</strong>.',
));
