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
	'MISSING_DEPENDENCIES' 		=> 'Damit diese Erweiterung installiert werden kann muss das Paket <strong>%s</strong> in der Erweiterung enthalten sein. Bitte verwende composer, um die fehlenden Abhängigkeiten zu installieren oder verwende ein vollständiges Installationspaket.',
	'MISSING_EXTENSION'			=> 'Damit diese Erweiterung installiert werden kann muss die Erweiterung <strong>%s</strong> installiert sein.',
	// Overload enable message to explain the use of the extension:
	'EXTENSION_ENABLE_SUCCESS'	=> 'Die Erweiterung wurde erfolgreich aktiviert.<br /><br /> Diese Erweiterung hat <strong>keine eigene Konfigurationsseite</strong> im Admin-Bereich. <br /><br /> Du kannst mit der <strong>neuen Berechtigung</strong> »Kann Terminplaner hinzufügen« für jedes Forum steuern, ob und welche Benutzer einen Terminplaner an ein Thema hängen dürfen. <br /><br /> Du kannst anschließend <strong>Terminplaner erstellen</strong>, indem du beim Erstellen eines neuen Themas das entsprechende <strong>Häkchen setzt</strong> oder nachträglich den ersten Beitrag bearbeitest.',
));
