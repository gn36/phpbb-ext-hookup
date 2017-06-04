<?php
/**
*
* hookup mod [French]
* @Translated by phpbb-fr.com community with help of phpBB-fr.com Translation Team
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
	'LOCALE'				=> 'fr',
	'HOOKUP'				=> 'Planificateur d’événement',
	'HOOKUP_DESC'			=> 'Ce sujet possède un planificateur d’événement. Utilisez-le pour organiser des réunions ou toutes sortes d’événements. Entrez votre disponibilité à côté de votre nom d’utilisateur dans la table. Le créateur du planificateur d’événement ou un modérateur peut ensuite utiliser les données pour déterminer la date active.',
	'ADD_HOOKUP'			=> 'Ajouter un événement',
	'ADD_HOOKUP_DESC'		=> 'Attacher un planificateur d’événement à ce sujet.',
	'ADD_HOOKUP_REACTIVATE'	=> 'Réactiver le planificateur',
	'ADD_HOOKUP_REACTIVATE_EXPLAIN' => 'Les données d’un précédent événement planifié sont disponibles. Si vous les réactivez, toutes informations relatives aux dates et aux participants seront à nouveau disponibles.',
	'HOOKUP_STATUS'			=> array(\gn36\hookup\functions\hookup::HOOKUP_YES => 'Oui', \gn36\hookup\functions\hookup::HOOKUP_NO => 'Non', \gn36\hookup\functions\hookup::HOOKUP_MAYBE => 'Peut-être', \gn36\hookup\functions\hookup::HOOKUP_UNSET => '-'),
	'HOOKUP_DATEFORMAT'		=> 'j.n.y H:i', //d M h:i a // this is used for the column headings so it should be short
	'HOOKUP_DATEFORMAT_TITLE' => 'l d F Y à H:i', //this is used for the topic title
	'HOOKUP_DATEFORMAT_POST' => 'l d F Y à H:i', //this is used for the post when the active date is set
	'HOOKUP_DATEFORMAT_CALENDAR' => '%d.%m.%Y %H:%M',
	'HOOKUP_ADD_USERS'		=> 'Inviter un membre',
	'HOOKUP_ADD_GROUPS'		=> 'Inviter un groupe d’utilisateurs',
	'HOOKUP_ADD_DATES'		=> 'Propositions de dates',
	'HOOKUP_ADD_DATES_EXPLAIN'=> 'Ici, vous pouvez proposer une ou plusieurs nouvelles dates. Inscrivez une date par ligne au format <strong>JJ.MM.AAAA hh:mm</strong> ou <strong>AAAA-MM-JJ hh:mm</strong>.',
	'HOOKUP_ADD_DATEFORMAT'	=> ' (JJ.MM.AAAA hh:mm)', //shown only for non js users (js users use the calendar)
	'CLEAR'					=> 'Effacer',
	'CLEAR_TITLE'			=> 'Effacer la date sélectionnée',
	'UNSET_ACTIVE'			=> 'Ne pas fixer de date',
	'SET_ACTIVE'			=> 'Valider cette date',
	'SET_ACTIVE_EXPLAIN'	=> 'Valider cette date comme date de l’événement',
	'SET_ACTIVE_CONFIRM'	=> 'Êtes-vous sûr de vouloir choisir la date du « %s » comme date de l’événement ?',
	'UNSET_ACTIVE_CONFIRM'	=> 'Êtes-vous sûr de vouloir annuler cette date et rouvrir le planificateur d’événement ?',
	'ACTIVE_DATE_SET'		=> 'La date de l’événement a été fixée au %s.',
	'ACTIVE_DATE_UNSET'		=> 'Aucune date n’a été définie.',
	'ACTIVE_DATE'			=> 'Date de l’événement',
	'SHOW_ALL_DATES'		=> 'Voir toutes les dates',
	'HIDE_ALL_DATES'		=> 'Cacher les dates',
	'NO_DATE'				=> 'Il n’y a pas de dates !',
	'INVALID_DATE'			=> 'Date invalide. S’il vous plait, entrez les dates au format JJ.MM.AAAA hh:mm ou AAAA-MM-JJ hh:mm.',
	'CANNOT_ADD_PAST'		=> 'Vous ne pouvez pas ajouter une date échue.',
	'SUM'					=> 'Total',
	'HOOKUP_NO_DATES'		=> 'Aucune date n’a été ajoutée.',
	'HOOKUP_NO_USERS'		=> 'Aucun membre n’a été invité.',
	'HOOKUP_USER_EXISTS'	=> '%s est déjà membre de cet événement.',
	'HOOKUP_USERS_EXIST'	=> 'Les membres sélectionnés ont déjà été ajoutés à cet événement.',
	'USERNAMES_EXPLAIN'		=> 'Depuis cet onglet, vous pouvez ajouter de nouveaux membres à la liste des participants. Vous pouvez ajouter plusieurs membres en une fois, en saisissant chaque nom sur une nouvelle ligne.',
	'HOOKUP_ADD_GROUPS_EXPLAIN'=> 'Ici, vous pouvez ajouter des groupes d’utilisateurs à la liste. De multiples groupes peuvent être ajoutés, Tous les membres des groupes sélectionnés seront individuellement ajoutés à la liste des participants.',
	'HOOKUP_OVERVIEW'		=> 'Vue d’ensemble de la planification',
	'DATE_ALREADY_ADDED'	=> 'La date %1s a déjà été ajoutée à cette planification.',
	'HOOKUP_DELETE_EXPLAIN'	=> 'Depuis cet onglet, vous pouvez retirer des participants ou des dates, voire désactiver ou supprimer entièrement le planificateur d’événement.',
	'DELETE_HOOKUP'			=> 'Désactiver le planificateur d’événement',
	'DELETE_WHOLE_HOOKUP'	=> 'Suppression du planificateur d’événement',
	'DELETE_HOOKUP_NO'		=> 'Ne rien supprimer',
	'DELETE_HOOKUP_DISABLE'	=> 'Seulement désactiver',
	'DELETE_HOOKUP_DISABLE_EXPLAIN' => 'Ce planificateur d’événement n’apparaitra plus dans ce sujet, mais les données (participants, dates et autres informations) seront conservées dans la base de données.',
	'DELETE_HOOKUP_DISABLE_CONFIRM'	=> 'Voulez-vous désactiver cet événement ? Les données de cet événement seront conservées. Vous pourrez réactiver cet événement ultérieurement.',
	'DELETE_HOOKUP_DELETE'	=> 'Supprimer toutes les données',
	'DELETE_HOOKUP_DELETE_EXPLAIN' => 'Toutes les données liées à ce planificateur d’événement seront supprimées.',
	'DELETE_HOOKUP_DELETE_CONFIRM'	=> 'Voulez-vous vraiment supprimer ce planificateur d’événement ? Gardez à l’esprit que les données seront définitivement perdues !',
	'DELETE_USERS'			=> 'Supprimer un ou plusieurs participants',
	'DELETE_DATES'			=> 'Supprimer une ou plusieurs dates',
	'HOOKUP_DELETE_VIEWTOPIC_EXPLAIN' => 'Ce sujet possède déjà un planificateur d’événement. Pour supprimer une date, un participant ou le planificateur d’événement, allez dans l’onglet <em>Supprimer</em> présent sur la page de visualisation du sujet.',
	'HOOKUP_DELETE_CONFIRM'	=> array(
		'USERS' => 'Voulez-vous supprimer %s ?',
		'DATES'	=> 'Voulez-vous supprimer %s ?',
		'UANDD' => 'Voulez-vous supprimer %s et %s ?',
	),
	'DATES' => array(
		1 => '1 date',
		2 => '%d dates',
	),
	'USERS' => array(
		1 => '1 participant',
		2 => '%d participants',
	),
	//'ADDED_AT_BY'			=> 'Ajouté le %1s par %2s',
	'OPEN_CALENDAR'			=> 'Ouvrir le calendrier',
	'CLOSE_CALENDAR'		=> 'Fermé le calendrier',
	'USER_CANNOT_READ_FORUM'=> '%s n’est pas autorisé à lire ce forum.',
	'USER_CANNOT_HOOKUP'	=> '%s n’est pas autorisé à utiliser les planificateurs d’événement de ce forum.',
	'SET_ACTIVE_TITLE_PREFIX'=>'Ajouter cette date devant le titre du sujet.',
	'SET_ACTIVE_SEND_EMAIL'	=> 'Notifier par e-mail les participants de cet événement qu’une date a été choisie.',
	'SET_ACTIVE_POST_REPLY'	=> 'Ajouter une réponse dans ce sujet indiquant la date choisie pour cet événement.',
	'SET_ACTIVE_POST_TEMPLATE'=> 'La date de l’événement à fixée au : [b]{ACTIVE_DATE}[/b].',
	'HOOKUP_SELF_INVITE'	=> 'Auto-invitation',
	'HOOKUP_SELF_INVITE_DESC' => 'N’importe quel membre du forum peut s’ajouter à la liste des participants.',
	'HOOKUP_SELF_INVITE_EXPLAIN' => 'S’il y a un grand nombre de membres potentiellement intéressés, mais seulement quelques-uns d’entre eux le sont réellement, vous pouvez utiliser cette option pour permettre à ces derniers de s’auto-inviter en tant que participant de l’événement.',
	'HOOKUP_INVITE_SELF'	=> 'Y participer',
	'HOOKUP_INVITE_SELF_DESC' => 'Oui, je veux participer à cet événement.',
	'HOOKUP_INVITE_SELF_EXPLAIN' => 'Ceci est un événement ouvert. Quiconque étant intéressé par cet événement peut s’y ajouter. Utilisez le bouton suivant pour y participer.',
	'HOOKUP_INVITE_SELF_EXPLAIN_GUEST' => 'Ceci est un événement ouvert. Quiconque étant intéressé par cet événement peut s’y ajouter, mais vous devez d’abord vous connecter sur ce forum.',
	'HOOKUP_INVITE_SELF_LEAVE'	=> 'Annuler son adhésion',
	'HOOKUP_INVITE_SELF_LEAVE_DESC'	=> 'Cliquez ici pour annuler votre adhésion à cet événement.',
	'HOOKUP_INVITE_SELF_LEAVE_EXPLAIN' => 'Vous êtes actuellement membre de cet événement. Utilisez le bouton suivant si vous ne voulez plus y participer.',
	'HOOKUP_INVITE_SELF_LEAVE_CONFIRM' => 'Voulez-vous vraiment annuler votre participation à cette événement ?',
	'HOOKUP_INVITE_MYSELF'	=> 'Invitez-moi',
	'COMMENT'				=> 'Commentaire',
	'COMMENT_EXPLAIN'		=> 'You can optionally enter a comment which clarifies your entries in the list above. This is especially useful for "maybe" entries. If you enter a comment, a comment symbol will be shown behind your username in the list and a popup will show your message.',
	'HOOKUP_AUTORESET'		=> 'Réinitialiser chaque semaine',
	'HOOKUP_AUTORESET_DESC'	=> 'En activant cette option, la planification de l’événement sera automatiquement réinitialisée chaque semaine.<br />La première date proposée sera automatiquement reportée à la semaine suivante et toutes les autres dates seront automatiquement supprimées.',
	'HOOKUP_AUTORESET_ACTIVE_DESC' => 'Ce planificateur d’événement ajoutera automatiquement de nouvelle date chaque semaine. Les anciennes dates seront effacées.',
	'RUN'					=> 'Exécuter',
));
