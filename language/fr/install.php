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
	// Overload enable message to explain the use of the extension:
	'EXTENSION_ENABLE_SUCCESS'	=> 'L\'extension a été activée avec succès. <br/> <br/> Cette extension a <strong> aucune page de configuration spéciale </ strong> dans ACP. <br/> <br/> Vous pouvez utiliser la <strong> nouvelle autorisation </strong> »Peut ajouter un planificateur d’événement.« pour contrôler pour chaque forum, que les utilisateurs peuvent ou ne peuvent pas joindre un planificateur de réunion à un sujet dans ce forum. <br/> <br/> Vous pouvez alors <strong> créer un planificateur de réunion </strong> en cochant la case appropriée sous la zone de publication postale lors de la création d\'un nouveau sujet ou de la modification du premier article d\'un sujet existant.',
));
