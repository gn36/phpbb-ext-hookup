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
	'WRONG_PHP_VERSION' 		=> 'Die verwendete PHP Version ist mit dieser Erweiterung nicht kompatibel.',
	'WRONG_PHPBB_VERSION' 		=> 'Die verwendete phpBB Version ist mit dieser Erweiterung nicht kompatibel.',
	'WRONG_EXTENSION_VERSION' 	=> 'Die verwendete Version der Erweiterung <strong>%s</strong> ist mit dieser Erweiterung nicht kompatibel.',
	'MISSING_DEPENDENCIES' 		=> 'Es fehlen Abhängigkeiten, die von dieser Erweiterung benötigt werden. Bitte verwende composer, um die fehlenden Abhängigkeiten zu installieren oder verwende ein vollständiges Installationspaket.',
	'MISSING_EXTENSION'			=> 'Damit diese Erweiterung installiert werden kann muss die Erweiterung <strong>%s</strong> installiert sein.',
));
