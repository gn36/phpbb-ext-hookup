<?php
/**
*
* hookup mod [English]
*
* @package language
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
	'LOCALE'				=> 'en',
	'HOOKUP'				=> 'Meeting planner',
	'HOOKUP_DESC'			=> 'This topic has a meeting planner attached. The planner can be used to find a time to meet. Please enter your availability next to your username into the table. The creator of the meeting planner or a moderator can then use the data to determine the active date.',
	'ADD_HOOKUP'			=> 'Meeting',
	'ADD_HOOKUP_DESC'		=> 'Attach a meeting to this topic',
	'ADD_HOOKUP_REACTIVATE'	=> 'reactivate meeting',
	'ADD_HOOKUP_REACTIVATE_EXPLAIN' => 'Data of a previously disabled meeting planner is available. If you reactivate it, the previously added dates and users will be available again.',
	'HOOKUP_STATUS'			=> array(\gn36\hookup\functions\hookup::HOOKUP_YES => 'Yes', \gn36\hookup\functions\hookup::HOOKUP_NO => 'No', \gn36\hookup\functions\hookup::HOOKUP_MAYBE => 'Maybe', \gn36\hookup\functions\hookup::HOOKUP_UNSET => '-'),
	'HOOKUP_DATEFORMAT'		=> 'd M H:i', //d M h:i a // this is used for the column headings so it should be short
	'HOOKUP_DATEFORMAT_TITLE' => 'd.m.Y H:i', //this is used for the topic title
	'HOOKUP_DATEFORMAT_POST' => 'l, d.m.Y H:i', //this is used for the post when the active date is set
	'HOOKUP_DATEFORMAT_CALENDAR' => '%Y-%m-%d %H:%M',
	'HOOKUP_ADD_USERS'		=> 'Invite users',
	'HOOKUP_ADD_GROUPS'		=> 'Invite groups',
	'HOOKUP_ADD_DATES'		=> 'Propose new dates',
	'HOOKUP_ADD_DATES_EXPLAIN'=> 'Here you can propose new dates or options with descriptive text. Enter one date per line in DD.MM.YYYY hh:mm, YYYY-MM-DD hh:mm format or prepend a # to enter an option in text format.',
	'HOOKUP_ADD_DATEFORMAT'	=> ' (yyyy-mm-dd hh:mm)', //shown only for non js users (js users use the calendar)
	'CLEAR'					=> 'Clear',
	'CLEAR_TITLE'			=> 'Clears the selected date(s)',
	'UNSET_ACTIVE'			=> 'Unset active date',
	'SET_ACTIVE'			=> 'Set active',
	'SET_ACTIVE_EXPLAIN'	=> 'Set this date as the active date',
	'SET_ACTIVE_CONFIRM'	=> 'Are you sure you want to make %s the active date?',
	'UNSET_ACTIVE_CONFIRM'	=> 'Are you sure you want to unset the active date and reopen the meeting planner?',
	'ACTIVE_DATE_SET'		=> 'The active date has been set to %s.',
	'ACTIVE_DATE_UNSET'		=> 'The active date has been unset.',
	'ACTIVE_DATE'			=> 'Active date',
	'SHOW_ALL_DATES'		=> 'Show all dates',
	'HIDE_ALL_DATES'		=> 'Hide date list',
	'NO_DATE'				=> 'Date does not exist!',
	'INVALID_DATE'			=> 'Invalid date/option. Please enter date in DD.MM.YYYY HH:MM, YYYY-MM-DD HH:MM or prepend a # to enter an option in text format.',
	'CANNOT_ADD_PAST'		=> 'Cannot add a date in the past',
	'SUM'					=> 'Sum',
	'HOOKUP_NO_DATES'		=> 'No dates have been added yet.',
	'HOOKUP_NO_USERS'		=> 'No users have been invited yet.',
	'HOOKUP_USER_EXISTS'	=> 'The user %s is already a member of this meeting planner.',
	'HOOKUP_USERS_EXIST'	=> 'The selected users are already members of this meeting planner.',
	'USERNAMES_EXPLAIN'		=> 'Here, you can add new users to the list. Multiple users can be entered, be sure to start a new line for each user.',
	'HOOKUP_ADD_GROUPS_EXPLAIN'=> 'Here, you can add complete groups to the list. Multiple groups can be selected, every user in all selected groups will be added.',
	'HOOKUP_OVERVIEW'		=> 'Meeting planner-overview',
	'DATE_ALREADY_ADDED'	=> 'The date %1s has already been added to this meeting planner',
	'HOOKUP_DELETE_EXPLAIN'	=> 'Here you can delete individual users, dates or the whole meeting planner',
	'DELETE_HOOKUP'			=> 'Delete meeting planner',
	'DELETE_WHOLE_HOOKUP'	=> 'Delete whole meeting planner',
	'DELETE_HOOKUP_NO'		=> 'Don’t delete anything',
	'DELETE_HOOKUP_DISABLE'	=> 'Disable only',
	'DELETE_HOOKUP_DISABLE_EXPLAIN' => 'The meeting planner will not be displayed in the topic anymore, but the data (users, dates, available information) will be kept in the database.',
	'DELETE_HOOKUP_DISABLE_CONFIRM'	=> 'Do you really want to disable the meeting planner? The data (users, dates, available information) will be kept in the database and the meeting planner can be reactivated anytime',
	'DELETE_HOOKUP_DELETE'	=> 'Delete all data',
	'DELETE_HOOKUP_DELETE_EXPLAIN' => 'All data related to this meeting planner will be deleted.',
	'DELETE_HOOKUP_DELETE_CONFIRM'	=> 'Do you really want to delete this meeting planner? All stored data (users, dates, available information) will be lost and cannot be restored.',
	'DELETE_USERS'			=> 'Delete individual users',
	'DELETE_DATES'			=> 'Delete individual dates',
	'HOOKUP_DELETE_VIEWTOPIC_EXPLAIN' => 'This topic already contains an active meeting planner. To delete individual dates/users or the whole meeting planner, use the <em>delete</em> tab in the topic view',
	'HOOKUP_DELETE_CONFIRM'	=> array(
		'USERS' => 'Do you really want to delete %s?',
		'DATES'	=> 'Do you really want to delete %s?',
		'UANDD' => 'Do you really want to delete %s and %s?',
	),
	'DATES' => array(
		1 => '1 date',
		2 => '%d dates',
	),
	'USERS' => array(
		1 => '1 user',
		2 => '%d users',
	),
	//'ADDED_AT_BY'			=> 'added at %1s by %2s',
	'OPEN_CALENDAR'			=> 'Open calendar',
	'CLOSE_CALENDAR'		=> 'Close calendar',
	'USER_CANNOT_READ_FORUM'=> 'The user %s doesn’t have the permission to read this forum.',
	'USER_CANNOT_HOOKUP'	=> 'The user %s is not permitted to use meeting planners in this forum.',
	'SET_ACTIVE_TITLE_PREFIX'=>'Prepend topic title with active date',
	'SET_ACTIVE_SEND_EMAIL'	=> 'Notify members of the meeting planner via e-mail about the active date',
	'SET_ACTIVE_POST_REPLY'	=> 'Add a reply with a notice about the active date to this topic',
	'SET_ACTIVE_POST_TEMPLATE'=> "The active date has been set: [b]{ACTIVE_DATE}[/b]",
	'HOOKUP_SELF_INVITE'	=> 'Self-invite',
	'HOOKUP_SELF_INVITE_DESC' => 'Anyone who is interested may add himself to the member list.',
	'HOOKUP_SELF_INVITE_EXPLAIN' => 'If there is a large number of potentially interested users but only a few of them are actually interested, you can use this option to allow interests to invite themselves as member of the meeting planner.',
	'HOOKUP_INVITE_SELF'	=> 'Participate',
	'HOOKUP_INVITE_SELF_DESC' => 'Yes, I want to be part of this meeting planner.',
	'HOOKUP_INVITE_SELF_EXPLAIN' => 'This is an open meeting planner, anyone who is interested may add themselves as member. If you want to participate, use the following button.',
	'HOOKUP_INVITE_SELF_EXPLAIN_GUEST' => 'This is an open meeting planner, anyone who is interested may add themselves as member. You need to login first if you want to use this feature.',
	'HOOKUP_INVITE_SELF_LEAVE'	=> 'Cancel membership',
	'HOOKUP_INVITE_SELF_LEAVE_DESC'	=> 'Click here to cancel your membership in this meeting planner.',
	'HOOKUP_INVITE_SELF_LEAVE_EXPLAIN' => 'You are currently a member of this meeting planner. Use the following button if you don’t want to participate anymore.',
	'HOOKUP_INVITE_SELF_LEAVE_CONFIRM' => 'Do you really want to cancel your membership in this meeting planner?',
	'HOOKUP_INVITE_MYSELF'	=> 'Invite myself',
	'COMMENT'				=> 'Comment',
	'COMMENT_EXPLAIN'		=> 'You can optionally enter a comment which clarifies your entries in the list above. This is especially useful for "maybe" entries. If you enter a comment, a comment symbol will be shown behind your username in the list and a popup will show your message.',
	'HOOKUP_AUTORESET'		=> 'Weekly reset',
	'HOOKUP_AUTORESET_DESC'	=> 'With this setup you can set the meeting planner to automatically reset once a week.<br /> By doing this, the first entry of the planner is repeated every week and old dates are automatically deleted.',
	'HOOKUP_AUTORESET_ACTIVE_DESC' => 'This meeting planner will add new dates automatically each week. The oldest dates are deleted.',
	'RUN'					=> 'Execute',
));
