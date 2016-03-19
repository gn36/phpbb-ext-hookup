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
	'WRONG_PHP_VERSION' 		=> 'Votre version de PHP est incompatible avec cette extension.',
	'WRONG_PHPBB_VERSION' 		=> 'Votre version de phpBB est incompatible avec cette extension.',
	'WRONG_EXTENSION_VERSION' 	=> 'La version de l\'extension <strong>%s</strong> est incompatible avec cette extension.',
	'MISSING_DEPENDENCIES' 		=> 'Dépendances de cette extension sont manquantes. S\'il vous plaît utiliser composer pour installer les dépendances manquantes ou utiliser un package d\'installation complète.',
	'MISSING_EXTENSION'			=> 'Pour installer cette extension, l\'extension <strong>%s</strong> est nécessaire.',
));
