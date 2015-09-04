<?php

/**
 *
 * @package hookup
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use gn36\hookup\functions\hookup;

class viewtopic implements EventSubscriberInterface
{

	/**
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_assign_template_vars_before'	=> 'show_hookup_viewtopic',
		);
	}

	/** @var \gn36\hookup\functions\hookup */
	protected $hookup;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var \phpbb\event\dispatcher_interface */
	protected $phpbb_dispatcher;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $phpEx;

	/** @var string */
	protected $hookup_path;

	/**
	 * Constructor
	 *
	 * @param \gn36\hookup\functions\hookup $hookup
	 * @param \phpbb\template\template $template
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\user $user
	 * @param \phpbb\auth\auth $auth
	 * @param \phpbb\request\request_interface $request
	 * @param \phpbb\event\dispatcher_interface $phpbb_dispatcher
	 * @param \phpbb\notification\manager $notification_manager
	 * @param string $phpbb_root_path
	 * @param string $phpEx
	 * @param string $hookup_path
	 */
	 function __construct(\gn36\hookup\functions\hookup $hookup, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, \phpbb\event\dispatcher_interface $phpbb_dispatcher, \phpbb\notification\manager $notification_manager, $phpbb_root_path, $phpEx, $hookup_path)
	 {
		$this->hookup = $hookup;
		$this->template = $template;
		$this->db = $db;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->phpEx = $phpEx;
		$this->hookup_path = $hookup_path;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
		$this->notification_manager = $notification_manager;
	}

	/**
	 * Process all hookup stuff in viewtopic
	 *
	 * @param \phpbb\event\data $event
	 */
	public function show_hookup_viewtopic($event)
	{
		// Check auth
		if (!$this->auth->acl_get('f_hookup', $event['forum_id']) && !$this->auth->acl_get('m_edit', $event['forum_id']))
		{
			return;
		}

		if (!$this->hookup->load_hookup($event['topic_id']))
		{
			// No hookup for this topic
			return;
		}

		// Load Language file
		$this->user->add_lang_ext('gn36/hookup', 'hookup');

		// Now process all submits, if any

		$hookup_errors = $this->process_submit($event);

		// If the hookup is disabled, then return now (also if we just disabled it)
		if (!$this->hookup->hookup_enabled)
		{
			return;
		}

		// Set the hookup as "viewed" for the current user:
		$this->hookup->set_user_data($this->user->data['user_id'], 0);
		$notifications_read = array(
			'gn36.hookup.notification.type.invited',
			'gn36.hookup.notification.type.user_added',
			'gn36.hookup.notification.type.date_added',
			'gn36.hookup.notification.type.active_date_set',
			'gn36.hookup.notification.type.active_date_reset',
		);
		$this->notification_manager->mark_notifications_read($notifications_read, $event['topic_id'], $this->user->data['user_id']);

		// We actually need to delete these to be able to send them again...
		// There must be a better solution to this, but there is no unique single value ID that can be used for this
		// We would need a combination of two numeric values to generate a unique ID (user_id <-> topic_id)
		// TODO

		// This will submit all changes to the db, including the ones processed in $this->process_submit();
		$this->hookup->submit();

		// Some frequently used data:
		$forum_id = $event['forum_id'];
		$topic_id = $event['topic_id'];
		$is_owner  = $event['topic_data']['topic_poster'] == $this->user->data['user_id'] || $this->auth->acl_get('m_edit', $event['forum_id']);
		$is_member = isset($this->hookup->hookup_users[$this->user->data['user_id']]);
		$viewtopic_url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->phpEx}?f=$forum_id&t=$topic_id");

		// TODO: populate lists for adding groups, deleting dates and users
		if ($is_owner)
		{
			// Populate group list
			$sql = 'SELECT group_id, group_name, group_type
				FROM ' . GROUPS_TABLE . '
				WHERE group_type <> ' . GROUP_HIDDEN . ' AND ' . $this->db->sql_in_set('group_name', array('GUESTS', 'BOTS'), true);
			$result = $this->db->sql_query($sql);

			$s_group_list = '';
			while ($row = $this->db->sql_fetchrow($result))
			{
				$s_group_list .= '<option value="' . $row['group_id'] . '"' . ($row['group_type'] == GROUP_SPECIAL ? ' class="sep"' : '') .'>';
				$s_group_list .= ($row['group_type'] == GROUP_SPECIAL ? $this->user->lang['G_' . $row['group_name']] : $row['group_name']);
				$s_group_list .= '</option>';
			}
			$this->db->sql_freeresult($result);

			$this->template->assign_var('S_GROUP_LIST', $s_group_list);
		}

		if (count($this->hookup->hookup_dates) == 0)
		{
			$hookup_errors[] = $this->user->lang['HOOKUP_NO_DATES'];
		}
		if (count($this->hookup->hookup_users) == 0)
		{
			$hookup_errors[] = $this->user->lang['HOOKUP_NO_USERS'];
		}
		$active_date_date = '-';
		if (isset($this->hookup->hookup_dates[$this->hookup->hookup_active_date]))
		{
			$active_date_date = $this->user->format_date($this->hookup->hookup_dates[$this->hookup->hookup_active_date]['date_time']);
			if ($this->hookup->hookup_dates[$this->hookup->hookup_active_date]['text'] != null)
			{
				$active_date_date = $this->hookup->hookup_dates[$this->hookup->hookup_active_date]['text'];
			}
		}
		$this->template->assign_vars(array(
			'S_HAS_HOOKUP'		=> true,
			'S_IS_HOOKUP_OWNER' => $is_owner,
			'S_IS_HOOKUP_MEMBER'=> $is_member,
			'S_HAS_DATES'		=> empty($this->hookup->hookup_dates) ? false : true,
			'S_HAS_USERS'		=> empty($this->hookup->hookup_users) ? false : true,
			'S_IS_SELF_INVITE'	=> $this->hookup->hookup_self_invite,
			'S_HOOKUP_ACTION'	=> $viewtopic_url,
			'S_ACTIVE_DATE'		=> $this->hookup->hookup_active_date,
			'ACTIVE_DATE_DATE'	=> $active_date_date,
			'S_NUM_DATES'		=> count($this->hookup->hookup_dates),
			'S_NUM_DATES_PLUS_1'=> count($this->hookup->hookup_dates) + 1,
			'U_UNSET_ACTIVE'	=> $viewtopic_url . '&amp;set_active=0',
			'U_FIND_USERNAME'	=> append_sid("{$this->phpbb_root_path}memberlist.{$this->phpEx}", 'mode=searchuser&amp;form=ucp&amp;field=usernames'),
			'UA_FIND_USERNAME'	=> append_sid("{$this->phpbb_root_path}memberlist.{$this->phpEx}", 'mode=searchuser&form=ucp&field=usernames', false),
			'USER_COMMENT'		=> isset($this->hookup->hookup_users[$this->user->data['user_id']])? $this->hookup->hookup_users[$this->user->data['user_id']]['comment'] : '',
			'HOOKUP_ERRORS'		=> (count($hookup_errors) > 0) ? implode('<br />', $hookup_errors) : false,
			'HOOKUP_YES'		=> hookup::HOOKUP_YES,
			'HOOKUP_MAYBE'		=> hookup::HOOKUP_MAYBE,
			'HOOKUP_NO'			=> hookup::HOOKUP_NO,
			'HOOKUP_UNSET'		=> hookup::HOOKUP_UNSET,
			'L_HOOKUP_YES'		=> $this->user->lang['HOOKUP_STATUS'][hookup::HOOKUP_YES],
			'L_HOOKUP_NO'		=> $this->user->lang['HOOKUP_STATUS'][hookup::HOOKUP_NO],
			'L_HOOKUP_MAYBE'	=> $this->user->lang['HOOKUP_STATUS'][hookup::HOOKUP_MAYBE],
			'L_HOOKUP_UNSET'	=> $this->user->lang['HOOKUP_STATUS'][hookup::HOOKUP_UNSET],
			//one letter versions for summaries
			'L_HOOKUP_Y'		=> $this->user->lang['HOOKUP_STATUS'][hookup::HOOKUP_YES]{0},
			'L_HOOKUP_N'		=> $this->user->lang['HOOKUP_STATUS'][hookup::HOOKUP_NO]{0},
			'L_HOOKUP_M'		=> $this->user->lang['HOOKUP_STATUS'][hookup::HOOKUP_MAYBE]{0},
			'S_EXT_PATH'		=> $this->hookup_path,
			'S_LANG_NAME'		=> $this->user->lang_name,
		));

		// Output dates
		foreach ($this->hookup->hookup_dates as $hookup_date)
		{
			$yes_count = $this->hookup->hookup_available_sums[$hookup_date['date_id']][hookup::HOOKUP_YES];
			$maybe_count = $this->hookup->hookup_available_sums[$hookup_date['date_id']][hookup::HOOKUP_MAYBE];
			$no_count = $this->hookup->hookup_available_sums[$hookup_date['date_id']][hookup::HOOKUP_NO];
			//$total_count = $yes_count + $maybe_count + $no_count; //unset_count?
			$total_count = count($this->hookup->hookup_users);
			$unset_count = $total_count - ($yes_count + $maybe_count + $no_count);

			$yes_percent = $total_count > 0 ? round(($yes_count / $total_count) * 100) : 0;
			$maybe_percent = $total_count > 0 ? round(($maybe_count / $total_count) * 100) : 0;
			$no_percent = $total_count > 0 ? round(($no_count / $total_count) * 100) : 0;
			$unset_percent = 100 - ($yes_percent + $maybe_percent + $no_percent);
			// More than 100%
			if ($unset_percent < 0)
			{
				if ($maybe_percent > 0)
				{
					$maybe_percent--;
				}
				else
				{
					$no_percent--;
				}
			}
			$full_date = $this->user->format_date($hookup_date['date_time']);
			$short_date = $this->user->format_date($hookup_date['date_time'], $this->user->lang['HOOKUP_DATEFORMAT']);
			if ($hookup_date['text'] != null)
			{
				$short_date = $full_date = $hookup_date['text'];
			}

			$this->template->assign_block_vars('date', array(
				'ID'			=> $hookup_date['date_id'],
				'DATE'			=> $short_date,
				'FULL_DATE'		=> $full_date,
				'YES_COUNT'		=> $yes_count,
				'YES_PERCENT'	=> $yes_percent,
				'MAYBE_COUNT'	=> $maybe_count,
				'MAYBE_PERCENT'	=> $maybe_percent,
				'NO_COUNT'		=> $no_count,
				'NO_PERCENT'	=> $no_percent,
				'UNSET_COUNT'	=> $unset_count,
				'UNSET_PERCENT'	=> $unset_percent,
				'S_IS_ACTIVE'	=> $hookup_date['date_id'] == $this->hookup->hookup_active_date,
				'U_SET_ACTIVE'	=> $viewtopic_url . '&amp;set_active=' . $hookup_date['date_id'],
			));
		}

		// Output details
		if (!empty($this->hookup->hookup_users))
		{
			// Fetch User details
			$sql = 'SELECT user_id, username, user_colour FROM ' . USERS_TABLE .
				' WHERE ' . $this->db->sql_in_set('user_id', array_keys($this->hookup->hookup_users));
			$result = $this->db->sql_query($sql);
			$user_details = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$user_details[$row['user_id']] = $row;
			}

		}
		foreach ($this->hookup->hookup_users as $hookup_user)
		{
			$is_self = ($hookup_user['user_id'] == $this->user->data['user_id']);

			$this->template->assign_block_vars('user', array(
				'ID'		=> $hookup_user['user_id'],
				'NAME'		=> $user_details[$hookup_user['user_id']]['username'],
				'COMMENT'	=> isset($hookup_user['comment']) ? $hookup_user['comment'] : '',
				'USERNAME_FULL'	=> get_username_string('full', $hookup_user['user_id'], $user_details[$hookup_user['user_id']]['username'], $user_details[$hookup_user['user_id']]['user_colour']),
				'IS_SELF'	=> $is_self
			));

			foreach ($this->hookup->hookup_dates as $hookup_date)
			{
				$available = isset($this->hookup->hookup_availables[$hookup_user['user_id']][$hookup_date['date_id']])
				? $this->hookup->hookup_availables[$hookup_user['user_id']][$hookup_date['date_id']]
				: hookup::HOOKUP_UNSET;

				$this->template->assign_block_vars('user.date', array(
					'ID'				=> $hookup_date['date_id'],
					'AVAILABLE'			=> $this->user->lang['HOOKUP_STATUS'][$available],
					'STATUS_YES'		=> ($available == hookup::HOOKUP_YES),
					'STATUS_NO'			=> ($available == hookup::HOOKUP_NO),
					'STATUS_MAYBE'		=> ($available == hookup::HOOKUP_MAYBE),
					'STATUS_UNSET'		=> ($available == hookup::HOOKUP_UNSET),
					'S_SELECT_NAME'		=> 'available['.$hookup_date['date_id'].']',
					'S_IS_ACTIVE'		=> $hookup_date['date_id'] == $this->hookup->hookup_active_date,
				));
			}
		}

	}

	/**
	 * Processes hookup set activedate requests
	 *
	 * @param \phpbb\event\data $event
	 * @param bool $is_owner
	 */
	protected function process_set_activedate($event, $is_owner)
	{
		$set_active_set = $this->request->is_set('set_active', \phpbb\request\request_interface::POST) || $this->request->is_set('set_active', \phpbb\request\request_interface::GET);
		$set_active 	= $this->request->variable('set_active', 0);

		if (!$set_active_set)
		{
			return array();
		}

		if (!$is_owner)
		{
			return array($this->user->lang('NOT_AUTH_HOOKUP'));
		}

		if ($set_active && !isset($this->hookup->hookup_dates[$set_active]))
		{
			trigger_error('NO_DATE');
		}

		$active_date_formatted =  '-';
		if ($set_active != 0)
		{
			$active_date_formatted = $this->user->format_date($this->hookup->hookup_dates[$set_active]['date_time']);
			if ($this->hookup->hookup_dates[$set_active]['text'] != null)
			{
				$active_date_formatted = $this->hookup->hookup_dates[$set_active]['text'];
			}
		}
		$topic_id = $event['topic_id'];
		$forum_id = $event['forum_id'];
		$viewtopic_url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->phpEx}?f=$forum_id&t=$topic_id");

		if (confirm_box(true))
		{
			$title_prefix = $this->request->variable('title_prefix', false);
			$send_email = $this->request->variable('send_email', false);
			$post_reply = $this->request->variable('post_reply', false);

			$topic_data = $event['topic_data'];
			$new_string = '';
			if ($set_active != 0)
			{
				if ($this->hookup->hookup_dates[$set_active]['text'] != null)
				{
					$new_string = '[' . $this->hookup->hookup_dates[$set_active]['text'] . '] ';
				}
				else
				{
					$new_string = '[' . $this->user->format_date($this->hookup->hookup_dates[$set_active]['date_time'], $this->user->lang['HOOKUP_DATEFORMAT_TITLE']) . '] ';
				}
			}
			$new_title = preg_replace('#^(\\[.+?\\] )?#', $new_string, $event['topic_data']['topic_title']);

			/**
			 * Perform additional actions when active date is set
			 *
			 * @event gn36.hookup.set_activedate_confirmed
			 * @var bool	title_prefix	User wants to have a title prefix added
			 * @var bool	send_email		User wants E-Mails to be sent
			 * @var bool	post_reply		User wants to post a reply to the topic
			 * @var int		set_active		Date selected as active (0 means reset of active date)
			 * @var string	new_title		The new topic title that will be present after the update
			 * @var int 	topic_id		ID of the topic of the hookup
			 * @var int 	forum_id		ID of the forum the topic is in
			 * @var array 	topic_data		Topic data as received from core.viewtopic_assign_template_vars_before
			 * @since 1.0.0-a1
			 */
			$vars = array(
				'title_prefix',
				'send_email',
				'post_reply',
				'set_active',
				'new_title',
				'topic_id',
				'forum_id',
				'topic_data',
			);
			extract($this->phpbb_dispatcher->trigger_event('gn36.hookup.set_activedate_confirmed', compact($vars)));

			//insert active date (short format) into topic title. this will use language
			//and timezone of the "active maker" but the alternative would be
			//to query the HOOKUP_DATES table every time we need the topic title
			if ($set_active == 0 || $title_prefix)
			{
				$sql = 'UPDATE ' . TOPICS_TABLE . '
						SET hookup_active_date = ' . (int) $set_active . ",
							topic_title = '" . $this->db->sql_escape($new_title) . "'
									WHERE topic_id = $topic_id";
				$this->db->sql_query($sql);

				$sql = "UPDATE " . POSTS_TABLE . "
						SET post_subject='" . $this->db->sql_escape($new_title) . "'
								WHERE post_id = {$event['topic_data']['topic_first_post_id']}";
				$this->db->sql_query($sql);
			}
			else
			{
				//only set hookup_active_date
				$sql = 'UPDATE ' . TOPICS_TABLE . '
						SET hookup_active_date = ' . (int) $set_active . "
								WHERE topic_id = $topic_id";
				$this->db->sql_query($sql);
			}

			$this->hookup->update_available_sums();

			//notify all members about active date
			if (!empty($this->hookup->hookup_users))
			{
				if ($set_active)
				{
					$notify_data = array(
						'user_id' 		=> $this->user->data['user_id'],
						'date_id' 		=> $set_active,
						'date'			=> $this->hookup->hookup_dates[$set_active]['date_time'],
						'text'			=> $this->hookup->hookup_dates[$set_active]['text'],
						'topic_title' 	=> $event['topic_data']['topic_title'],
						'topic_id' 		=> $event['topic_id'],
						'forum_id'		=> $event['forum_id'],
						'yes'			=> $this->hookup->hookup_available_sums[$set_active][\gn36\hookup\functions\hookup::HOOKUP_YES],
						'no'			=> $this->hookup->hookup_available_sums[$set_active][\gn36\hookup\functions\hookup::HOOKUP_NO],
						'maybe'			=> $this->hookup->hookup_available_sums[$set_active][\gn36\hookup\functions\hookup::HOOKUP_MAYBE],
					);

					$this->notification_manager->add_notifications('gn36.hookup.notification.type.active_date_set', $notify_data);
					$this->notification_manager->update_notifications('gn36.hookup.notification.type.active_date_set', $notify_data);
					$this->notification_manager->delete_notifications('gn36.hookup.notification.type.active_date_reset', $event['topic_id']);
				}
				else
				{
					$notify_data = array(
						'user_id' 		=> $this->user->data['user_id'],
						'topic_title' 	=> isset($new_title) ? $new_title : $event['topic_data']['topic_title'],
						'topic_id' 		=> $event['topic_id'],
						'forum_id'		=> $event['forum_id'],
					);

					$this->notification_manager->delete_notifications('gn36.hookup.notification.type.active_date_set', $event['topic_id']);
					$this->notification_manager->add_notifications('gn36.hookup.notification.type.active_date_reset', $notify_data);
					$this->notification_manager->update_notifications('gn36.hookup.notification.type.active_date_reset', $notify_data);
				}
			}

			//post reply to this topic. Again this can only be in the "active maker"s language
			if ($set_active && $post_reply)
			{
				$message = $this->user->lang['SET_ACTIVE_POST_TEMPLATE'];
				$active_date = $this->user->format_date($this->hookup->hookup_dates[$set_active]['date_time'], $this->user->lang['HOOKUP_DATEFORMAT_POST']);
				if ($this->hookup->hookup_dates[$set_active]['text'] != null)
				{
					$active_date = $this->hookup->hookup_dates[$set_active]['text'];
				}
				$message = str_replace('{ACTIVE_DATE}', $active_date, $message);

				//TODO: functions_post_oo!
				//$post = new post($topic_id);
				//$post->post_text = $message;
				//$post->submit();
			}

			meta_refresh(3, $viewtopic_url);
			$message = ($set_active != 0 ? sprintf($this->user->lang['ACTIVE_DATE_SET'], $active_date_formatted) : $this->user->lang['ACTIVE_DATE_UNSET']) . '<br /><br />' . sprintf($this->user->lang['RETURN_TOPIC'], '<a href="' . $viewtopic_url . '">', '</a>');
			trigger_error($message);
		}
		else
		{
			$s_hidden_fields = build_hidden_fields(array(
				't'			=> $topic_id,
				'set_active'=> $set_active
			));
			if ($set_active != 0)
			{
				confirm_box(false, sprintf($this->user->lang['SET_ACTIVE_CONFIRM'], $active_date_formatted), $s_hidden_fields, '@gn36_hookup/hookup_active_date_confirm.html');
			}
			else
			{
				confirm_box(false, 'UNSET_ACTIVE', $s_hidden_fields);
			}
		}
		return array();
	}

	/**
	 * Process hookup self invite
	 *
	 * @param \phpbb\event\data $event
	 * @param bool $is_owner
	 * @param bool $is_member
	 */
	protected function process_selfinvite($event, $is_owner, $is_member)
	{
		$hookup_errors = array();

		// Check for self-invite / leave
		// TODO: Do this with AJAX
		$invite_self = $this->request->variable('invite_self', '');
		if (($this->hookup->hookup_self_invite || $is_owner)  && $invite_self == 'join' && !$is_member)
		{
			$this->hookup->add_user($this->user->data['user_id']);
			return $hookup_errors;
		}
		if (($this->hookup->hookup_self_invite || $is_owner) && $invite_self == 'leave' && $is_member)
		{
			if (confirm_box(true))
			{
				$this->hookup->remove_user($this->user->data['user_id']);
				return $hookup_errors;
			}
			else
			{
				$s_hidden_fields = build_hidden_fields(array(
					't'				=> $event['topic_id'],
					'invite_self'	=> 'leave',
				));
				confirm_box(false, $this->user->lang['HOOKUP_INVITE_SELF_LEAVE_CONFIRM'], $s_hidden_fields);
			}
		}

		return $hookup_errors;
	}

	/**
	 * Process adding hookup users
	 *
	 * @param \phpbb\event\data $event
	 * @param bool $is_owner
	 */
	protected function process_add_user($event, $is_owner)
	{
		$add_users = $this->request->variable('usernames', '', true);
		$add_groups = $this->request->variable('add_groups', array(0));

		if (empty($add_users) && empty($add_groups))
		{
			return array();
		}

		if (!$is_owner)
		{
			return array($this->user->lang('NOT_AUTH_HOOKUP'));
		}

		$hookup_errors = array();

		// Add users

		if (!empty($add_users))
		{
			// Cleanup Usernames
			$usernames = array_unique(explode("\n", $add_users));
			$usernames = array_map('utf8_clean_string', $usernames);

			//TODO: Prevent anonymous and bots
			$sql = 'SELECT user_id, username, user_type, user_permissions, user_lang, user_email, user_jabber, user_notify_type
				FROM ' . USERS_TABLE . '
				WHERE ' . $this->db->sql_in_set('username_clean', $usernames);
			$result = $this->db->sql_query($sql);
			$new_users = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$new_users[$row['user_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			$userids_to_add = array_diff(array_keys($new_users), array_keys($this->hookup->hookup_users));
			$userids_already_added = array_intersect(array_keys($new_users), array_keys($this->hookup->hookup_users));

		}

		// Add groups

		if (!empty($add_groups))
		{
			$sql = 'SELECT u.user_id, u.username, u.user_type, u.user_permissions, u.user_lang, u.user_email, u.user_jabber, u.user_notify_type
				FROM ' . USER_GROUP_TABLE . ' ug
				JOIN ' . USERS_TABLE . ' u
					ON (u.user_id = ug.user_id)
				WHERE ' . $this->db->sql_in_set('ug.group_id', $add_groups) . '
					AND ug.user_pending = 0';
			$result = $this->db->sql_query($sql);

			$new_users = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$new_users[$row['user_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			$userids_to_add = array_diff(array_keys($new_users), array_keys($this->hookup->hookup_users));
		}

		// Add & notify
		//now that we have the user_ids and data, add the users
		if (isset($userids_to_add) && count($userids_to_add) > 0)
		{
			//check if users have read permission
			$user_auth = new \phpbb\auth\auth();
			foreach ($userids_to_add as $key => $user_id)
			{
				$user_auth->acl($new_users[$user_id]);
				if (!$user_auth->acl_get('f_read', $event['forum_id']) || !$user_auth->acl_get('f_hookup', $event['forum_id']))
				{
					$hookup_errors[] = sprintf($this->user->lang['USER_CANNOT_READ_FORUM'], $new_users[$user_id]['username']);
					unset($userids_to_add[$key]);
				}
			}

			//insert users into database
			foreach ($userids_to_add as $user_id)
			{
				//no need to notify the user about new dates when he hasn't visited the
				//hookup yet and thus not even entered his available info for the first dates
				$this->hookup->add_user($user_id, '', 1);
			}

			$this->hookup->submit(false);

			foreach ($userids_to_add as $user_id)
			{
				// Notification
				$notify_data = array(
					'user_id' 		=> $this->user->data['user_id'],
					'invited_user' 	=> $user_id,
					'topic_title' 	=> $event['topic_data']['topic_title'],
					'topic_id' 		=> $event['topic_id'],
					'forum_id'		=> $event['forum_id'],
				);

				$this->notification_manager->add_notifications('gn36.hookup.notification.type.invited', $notify_data);
				$this->notification_manager->update_notifications('gn36.hookup.notification.type.user_added', $notify_data);
			}
		}

		//generate error messages for users that are already members
		if (isset($userids_already_added) && count($userids_already_added) > 0)
		{
			foreach ($userids_already_added as $userid)
			{
				$hookup_errors[] = sprintf($this->user->lang['HOOKUP_USER_EXISTS'], $new_users[$userid]['username']);
			}
		}

		return $hookup_errors;
	}

	/**
	 * Process enable/disable
	 *
	 * @param \phpbb\event\data $event
	 * @param bool $is_owner
	 */
	protected function process_disable($event, $is_owner)
	{
		$action = $this->request->variable('delete_hookup', 'no');

		if ($action != 'disable' && $action != 'delete')
		{
			return array();
		}

		if (!$is_owner)
		{
			return array($this->user->lang('NOT_AUTH_HOOKUP'));
		}

		if (confirm_box(true))
		{
			if ($action == 'disable')
			{
				$this->hookup->hookup_enabled = false;
				$this->hookup->submit(false);
				// TODO: is there a way to hide notifications?
				$this->notification_manager->delete_notifications(array(
					'gn36.hookup.notification.type.invited',
					'gn36.hookup.notification.type.user_added',
					'gn36.hookup.notification.type.date_added',
					'gn36.hookup.notification.type.active_date_set',
					'gn36.hookup.notification.type.active_date_reset',
				), $event['topic_id']);
			}
			else if ($action == 'delete')
			{
				$this->hookup->delete();
				$this->hookup->submit(false);
				$this->notification_manager->delete_notifications(array(
					'gn36.hookup.notification.type.invited',
					'gn36.hookup.notification.type.user_added',
					'gn36.hookup.notification.type.date_added',
					'gn36.hookup.notification.type.active_date_set',
					'gn36.hookup.notification.type.active_date_reset',
				), $event['topic_id']);
			}
		}
		else
		{
			$s_hidden_fields = build_hidden_fields(array(
				't'				=> $event['topic_id'],
				'delete_hookup'	=> $action
			));
			confirm_box(false, $this->user->lang['DELETE_HOOKUP_' . strtoupper($action) . '_CONFIRM'], $s_hidden_fields);
		}

		return array();
	}

	/**
	 * Process user/date deletes
	 *
	 * @param \phpbb\event\data $event
	 * @param bool $is_owner
	 */
	protected function process_deletes($event, $is_owner)
	{
		$delete_user_ids = $this->request->variable('delete_user', array(0));
		$delete_date_ids = $this->request->variable('delete_date', array(0));

		if (empty($delete_date_ids) && empty($delete_user_ids))
		{
			return array();
		}

		if (!$is_owner)
		{
			return array($this->user->lang('NOT_AUTH_HOOKUP'));
		}

		if (confirm_box(true))
		{
			foreach ($delete_date_ids as $date)
			{
				$this->hookup->remove_date(0, (int) $date);
			}

			foreach ($delete_user_ids as $user_id)
			{
				$this->hookup->remove_user((int) $user_id);
			}
			//TODO: Remove notifications
			// Remove notifications for removed users
			//$this->notification_manager->delete_notifications('gn36.hookup.notification.type.invited', $notify_data);
		}
		else
		{
			$s_hidden_fields = build_hidden_fields(array(
				't'				=> $event['topic_id'],
				'delete_date'	=> $delete_date_ids,
				'delete_user'	=> $delete_user_ids,
				//'available'		=> $available,
			));
			if (count($delete_date_ids) > 0 && count($delete_user_ids) > 0)
			{
				$question = $this->user->lang(array('HOOKUP_DELETE_CONFIRM', 'UANDD'), $this->user->lang('USERS', count($delete_user_ids)), $this->user->lang('DATES', count($delete_date_ids)));
			}
			else if (count($delete_date_ids))
			{
				$question = $this->user->lang(array('HOOKUP_DELETE_CONFIRM', 'DATES'), $this->user->lang('DATES', count($delete_date_ids)));
			}
			else
			{
				$question = $this->user->lang(array('HOOKUP_DELETE_CONFIRM', 'USERS'), $this->user->lang('USERS', count($delete_user_ids)));
			}
			confirm_box(false, $question, $s_hidden_fields);
		}

		return array();
	}

	/**
	 * Process adding dates
	 * @param \phpbb\event\data $event
	 * @param bool $is_member_or_owner
	 */
	protected function process_add_date($event, $is_member_or_owner)
	{
		$add_dates = $this->request->variable('add_date', '', true);

		if (empty($add_dates))
		{
			return array();
		}

		if (!$is_member_or_owner)
		{
			return array($this->user->lang('NOT_AUTH_HOOKUP'));
		}

		$hookup_errors = array();
		$add_dates = array_map("trim", explode("\n", $add_dates));

		//replace german date formats
		$add_dates = preg_replace('#(\\d{1,2})\\. ?(\\d{1,2})\\. ?(\\d{2})[,:]?[,: ]#', '20$3-$2-$1 ', $add_dates);
		$add_dates = preg_replace('#(\\d{1,2})\\. ?(\\d{1,2})\\. ?(\\d{4})[,:]?[,: ]#', '$3-$2-$1 ', $add_dates);
		$date_added = false;

		foreach ($add_dates as $date)
		{
			$text_match = preg_match('#\#(.*)#', $date, $text);
			//strtotime uses the local (server) timezone, so parse manually and use gmmktime to ignore any timezone
			if (!preg_match('#(\\d{4})-(\\d{1,2})-(\\d{1,2}) (\\d{1,2}):(\\d{2})#', $date) && !$text_match)
			{
				$hookup_errors[] = "$date: {$this->user->lang['INVALID_DATE']}";
			}
			else
			{
				$hookup_continue = true;
				if ($text_match)
				{
					$text = trim($text[1]);
					$date_time = '0';
				}
				else
				{
					$text = null;
				$date_time = $this->user->get_timestamp_from_format('Y-m-d H:i', $date);

				if ($date_time < time())
				{
						$hookup_continue = false;
					$hookup_errors[] = "$date: {$this->user->lang['CANNOT_ADD_PAST']}";
				}
				}
				if ($hookup_continue)
				{
					//check for duplicate
					if (!$this->hookup->add_date($date_time, $text))
					{
						$hookup_errors[] = sprintf($this->user->lang['DATE_ALREADY_ADDED'], $this->user->format_date($date_time, $text));
					}
					else
					{
						$date_added = true;
					}
				}
			}
		};

		if ($date_added)
		{
			// Notification
			$notify_data = array(
				'user_id' 		=> $this->user->data['user_id'],
				'topic_title' 	=> $event['topic_data']['topic_title'],
				'topic_id' 		=> $event['topic_id'],
				'forum_id'		=> $event['forum_id'],
			);

			$this->notification_manager->update_notifications('gn36.hookup.notification.type.date_added', $notify_data);
		}

		return $hookup_errors;
	}

	/**
	 * Process status changes
	 * @param \phpbb\event\data $event
	 * @param bool $is_member
	 */
	protected function process_status($event, $is_member)
	{
		$availables = $this->request->variable('available', array(0 => 0));

		if (!$this->request->is_set_post('available'))
		{
			return array();
		}

		if (!$is_member)
		{
			return array($this->user->lang('NO_HOOKUP_MEMBER'));
		}

		foreach ($availables as $date_id => $available)
		{
			//ignore HOOKUP_UNSET and other invalid values
			if (!is_numeric($date_id) || !isset($this->hookup->hookup_dates[$date_id]) || !in_array($available, array(hookup::HOOKUP_YES, hookup::HOOKUP_NO, hookup::HOOKUP_MAYBE)))
			{
				continue;
			}

			$this->hookup->set_user_date($this->user->data['user_id'], $date_id, $available);
		}

		$this->hookup->update_available_sums();

		$this->hookup->set_user_data($this->user->data['user_id'], 0, $this->request->variable('comment', '', true));

		return array();
	}

	/**
	 * Processes all submitted data in viewtopic into the hookup object without sending changes to database
	 * Don't forget to run $this->hookup->submit() afterwards!
	 *
	 * @param \phpbb\event\data $event
	 * @return array errors
	 */
	protected function process_submit($event)
	{
		$is_owner  = $event['topic_data']['topic_poster'] == $this->user->data['user_id'] || $this->auth->acl_get('m_edit', $event['forum_id']);
		$is_member = isset($this->hookup->hookup_users[$this->user->data['user_id']]);

		$hookup_errors = $this->process_selfinvite($event, $is_owner, $is_member);

		$is_member = isset($this->hookup->hookup_users[$this->user->data['user_id']]);

		// If we are neither member nor owner, we can't submit anything else anyways, so return
		if (!$is_member && !$is_owner)
		{
			return $hookup_errors;
		}

		$hookup_errors = array_merge($hookup_errors, $this->process_add_user($event, $is_owner));
		$hookup_errors = array_merge($hookup_errors, $this->process_add_date($event, ($is_member || $is_owner)));
		$hookup_errors = array_merge($hookup_errors, $this->process_disable($event, $is_owner));
		$hookup_errors = array_merge($hookup_errors, $this->process_deletes($event, $is_owner));
		$hookup_errors = array_merge($hookup_errors, $this->process_status($event, $is_member));
		$hookup_errors = array_merge($hookup_errors, $this->process_set_activedate($event, $is_owner));

		return $hookup_errors;
	}
}
