<?php
/**
*
* hookup mod [French]
*
* @package language
* @version $Id: hookup.php 1 2009-04-24 23:56:57Z joas $
* @copyright (c) 2006-2008 Pyramide (Frank Dreyer), (c) 2008-2015 gn#36 (Martin Beckmann)
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

// Define categories and permission types
$lang = array_merge($lang, array(
	'HOOKUP'				=> 'Planification de réunions',
	'HOOKUP_DESC'			=> 'Une planification de réunion est attachée à ce sujet. La planification permet de définir la meilleur date d’une réunions en fonction des disponibilités qu’auront donnés les participants. L’organisateur de la planification pourra, en temps utile, "fixer" la date de la réunion.',
	'ADD_HOOKUP'			=> 'Ajouter une planification',
	'ADD_HOOKUP_DESC'		=> 'Attacher une planification de réunion à ce sujet.',
	'ADD_HOOKUP_REACTIVATE'	=> 'Réactiver une réunion',
	'ADD_HOOKUP_REACTIVATE_EXPLAIN' => 'Les données d’une précédante planification de réunion qui a été désactivée seront à nouveau disponibles. Si vous les réactivez, les dates et les utilisateurs seront à nouveau disponibles.',
	'HOOKUP_STATUS'			=> array(\gn36\hookup\functions\hookup::HOOKUP_YES => 'Oui', \gn36\hookup\functions\hookup::HOOKUP_NO => 'Non', \gn36\hookup\functions\hookup::HOOKUP_MAYBE => 'Peut-être', \gn36\hookup\functions\hookup::HOOKUP_UNSET => '-'),
	'HOOKUP_DATEFORMAT'		=> 'd.m.Y', //d M h:i a // this is used for the column headings so it should be short
	'HOOKUP_DATEFORMAT_TITLE' => 'l d F Y à H:i', //this is used for the topic title
	'HOOKUP_DATEFORMAT_POST' => 'l d F Y à H:i', //this is used for the post when the active date is set
	'HOOKUP_DATEFORMAT_CALENDAR' => '%Y-%m-%d %H:%M',
	'HOOKUP_ADD_USERS'		=> 'Inviter un utilisateur',
	'HOOKUP_ADD_GROUPS'		=> 'Inviter un groupe d’utilisateurs',
	'HOOKUP_ADD_DATES'		=> 'Proposer une nouvelle date',
	'HOOKUP_ADD_DATES_EXPLAIN'=> 'Ici, vous pouvez proposer une ou plusieurs nouvelles dates. Inscivez une date par ligne au format JJ.MM.AAAA hh:mm ou AAAA-MM-JJ hh:mm.',
	'HOOKUP_ADD_DATEFORMAT'	=> ' (dd-mm-yyyy hh:mm)', //shown only for non js users (js users use the calendar)
	'CLEAR'					=> 'Effacer',
	'CLEAR_TITLE'			=> 'Effacer la date sélectionnée',
	'UNSET_ACTIVE'			=> 'Ne fixer aucune date',
	'SET_ACTIVE'			=> 'Fixer',
	'SET_ACTIVE_CONFIRM'	=> 'Etes-vous sûrs de vouloir faire du %s la date de la réunion?',
	'UNSET_ACTIVE_CONFIRM'	=> 'Etes-vous sûrs de renoncer à fixer une date de réunion et de revenir à la planification?',
	'ACTIVE_DATE_SET'		=> 'La date de réunion a été fixée au %s.',
	'ACTIVE_DATE_UNSET'		=> 'Aucune date n’a été fixée.',
	'ACTIVE_DATE'			=> 'Date de la réunion',
	'SHOW_ALL_DATES'		=> 'Voir toutes les dates',
	'HIDE_ALL_DATES'		=> 'Cacher les dates',
	'NO_DATE'				=> 'Il n’y a pas de dates!',
	'INVALID_DATE'			=> 'Date invalide. S’il vous plait, entrez les dates au format JJ.MM.AAAA hh:mm ou AAAA-MM-JJ hh:mm.',
	'CANNOT_ADD_PAST'		=> 'Vous ne pouvez pas entrer de dates dépassées',
	'SUM'					=> 'Calculation',
	'HOOKUP_NO_DATES'		=> 'Aucune date n’a été sélectionnée pour le moment.',
	'HOOKUP_NO_USERS'		=> 'Aucun utilisateur n’a été invité pour le moment.',
	'HOOKUP_USER_EXISTS'	=> '%s est déjà membre de cette planification.',
	'HOOKUP_USERS_EXIST'	=> 'Les utilisateurs sélectionnés sont déjà membres de cette planification.',
	'USERNAMES_EXPLAIN'		=> 'Ici, vous pouvez ajouter de nouveaux utilisateurs à la liste. De multiples utilisateurs peuvent être ajoutés. Veuillez insérer un utilisateur par ligne.',
	'HOOKUP_ADD_GROUPS_EXPLAIN'=> 'Ici, vous pouvez ajouter des groupes d’utilisateurs à la liste. De multiples groupes peuvent être ajoutés, Tous les utilisateurs des groupes sélectionnés seront ajoutés.',
	'HOOKUP_OVERVIEW'		=> 'Vue d’ensemble de la planification',
	'DATE_ALREADY_ADDED'	=> 'La date %1s a déjà été ajoutée à cette planification.',
	'HOOKUP_DELETE_EXPLAIN'	=> 'Ici vous pouvez supprimer des utilisateurs ou des dates de la présente planification.',
	'DELETE_HOOKUP'			=> 'Désactiver la plannification de réunion',
	'DELETE_WHOLE_HOOKUP'	=> 'Suppression totale de cette planification de réunion',
	'DELETE_HOOKUP_NO'		=> 'Ne rien supprimer',
	'DELETE_HOOKUP_DISABLE'	=> 'Juste mettre hors-service',
	'DELETE_HOOKUP_DISABLE_EXPLAIN' => 'Cette planification de réunion ne sera plus affichée dans le sujet, mais les données (utilisateurs, dates, et autres informations) seront conservées dans la base de données.',
	'DELETE_HOOKUP_DISABLE_CONFIRM'	=> 'Voulez-vous vraiment mettre hors-service cette planification de réunion? Gardez à l’esprit que les données étant maintenues, vous pourrez réactiver cette planification à tout moment.',
	'DELETE_HOOKUP_DELETE'	=> 'Supprimer toutes les données',
	'DELETE_HOOKUP_DELETE_EXPLAIN' => 'Toutes les données liées à cette planification de réunion seront supprimées.',
	'DELETE_HOOKUP_DELETE_CONFIRM'	=> 'Voulez-vous vraiment supprimer cette planification de réunion? Gardez à l’esprit que les données seront définitivement perdues !',
	'DELETE_USERS'			=> 'Supprimer un/des utilisateur(s)',
	'DELETE_DATES'			=> 'Supprimer une/des date(s)',
	'HOOKUP_DELETE_VIEWTOPIC_EXPLAIN' => 'Une date de réunion à été fixée dans ce sujet. Pour supprimer individuellement date ou utilisateur ou la planification dans son ensemble, allez dans l’onglet <em>Supprimer</em> sur la page de visualisation du sujet.',
	'HOOKUP_DELETE_CONFIRM'	=> array(
		'USERS' => 'Voulez-vous vraiment supprimer %s?',
		'DATES'	=> 'Voulez-vous vraiment supprimer %s?',
		'UANDD' => 'Voulez-vous vraiment supprimer %s et %s?',
	),
	'DATES' => array(
		1 => '1 date',
		2 => '%d dates',
	),
	'USERS' => array(
		1 => '1 utilisateur',
		2 => '%d utilisateurs',
	),
	//'ADDED_AT_BY'			=> 'Ajouté le %1s par %2s',
	'OPEN_CALENDAR'			=> 'Ouvrir le calendrier',
	'USER_CANNOT_READ_FORUM'=> '%s n’a pas la permission de lire ce forum.',
	'SET_ACTIVE_TITLE_PREFIX'=>'Ajouter la date active au titre du sujet.',
	'SET_ACTIVE_SEND_EMAIL'	=> 'Avertir, par e-mail, les membres de cette planification de la date qui à été fixée.',
	'SET_ACTIVE_POST_REPLY'	=> 'Ajoutez une réponse avec mention de la date active à ce sujet.',
	'SET_ACTIVE_POST_TEMPLATE'=> 'La réunion à été fixée au: [b]{ACTIVE_DATE}[/b]',
	'HOOKUP_SELF_INVITE'	=> 'Les utilisateurs s’ajoutent eux-mêmes',
	'HOOKUP_SELF_INVITE_DESC' => 'Les utilisateurs qui sont intéressés peuvent s’ajouter eux-mêmes à la liste de membres.',
	'HOOKUP_SELF_INVITE_EXPLAIN' => 'S’il y a un grand nombre d’utilisateurs potentiellement intéressés mais seulement quelques-uns d’entre eux sont réellement intéressés, vous pouvez utiliser cette option pour permettre aux intéressés de s’inviter eux-mêmes comme membres de la présente planification de réunion.',
	'HOOKUP_INVITE_SELF'	=> 'Participer',
	'HOOKUP_INVITE_SELF_DESC' => 'Oui, je veux faire partie de cette planification de réunion.',
	'HOOKUP_INVITE_SELF_EXPLAIN' => 'Ceci est une planification de réunion ouverte. Une personne qui est intéressé peut s’ajouter elle-même comme membre. Si vous voulez participer, utiliser le bouton suivant.',
	'HOOKUP_INVITE_SELF_EXPLAIN_GUEST' => 'Ceci est une planification de réunion ouverte. Pour y participer, vous devez d’abord vous connecter à ce forum.',
	'HOOKUP_INVITE_SELF_LEAVE'	=> 'Annuler son adhésion',
	'HOOKUP_INVITE_SELF_LEAVE_DESC'	=> 'Cliquez ici pour annuler votre adhésion à cette planification de réunion.',
	'HOOKUP_INVITE_SELF_LEAVE_EXPLAIN' => 'Vous êtes actuellement membre de cette planification de réunion. Utilisez le bouton suivant si vous ne voulez plus y participer.',
	'HOOKUP_INVITE_SELF_LEAVE_CONFIRM' => 'Voulez-vous vraiment annuler votre adhésion?',
	'HOOKUP_INVITE_MYSELF'	=> 'Invitez-moi',
	'COMMENT'				=> 'Commentaire',
	'HOOKUP_AUTORESET'		=> 'Répéter chaque semaine',
	'HOOKUP_AUTORESET_DESC'	=> 'Avec cette option, la planification sera automatiquement réinitialisée chaque semaine. En activant cette option, la première<br />sélection sera automatiquement reporté à la semaine suivante et les anciennes dates seront automatiquements supprimées.',
	'RUN'					=> 'Exécuter',
));
