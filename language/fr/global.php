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
	'HOOKUP_NOTIFY_BASE'				=> 'Ceci est une notification du planificateur d’événement.',
	'HOOKUP_NOTIFY_ACTIVE_DATE_SET'		=> 'La <strong>date effective de l’événement</strong> a été determinée le <strong>%3$s</strong> pour %2$s par %1$s (%4$s oui, %5$s non, %6$s peut-être).',
	'HOOKUP_NOTIFY_ACTIVE_DATE_RESET'	=> 'La <strong>date effective de l’événement</strong> pour %2$s a été <strong>révisée</strong> par %1$s.',
	'HOOKUP_NOTIFY_DATE_ADDED'			=> 'De nouvelles dates ont été ajoutées à l’événement %2$s par %1$s.',
	'HOOKUP_NOTIFY_DATE_ADDED_ROTATION'	=> 'De nouvelles dates hebdomadaires ont été ajoutées à l’événement %2$s.',
	'HOOKUP_NOTIFY_USER_ADDED'			=> 'Un nouveau participant %1$s a été ajouté au planificateur d’événement %2$s.',
	'HOOKUP_NOTIFY_INVITED'				=> 'Vous avez recu une <strong>invitation</strong> pour participer à l’événement %2$s par %1$s.'
));
