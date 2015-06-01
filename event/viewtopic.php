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

	function __construct(\gn36\hookup\functions\hookup $hookup, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\user $user)
	{
		$this->hookup = $hookup;
		$this->template = $template;
		$this->db = $db;
		$this->user = $user;
	}

	public function show_hookup_viewtopic($event)
	{
		if(!$this->hookup->load_hookup($event['topic_id']))
		{
			// No hookup for this topic
			return;
		}

		// Load Language file
		$this->user->add_lang_ext('gn36/hookup', 'hookup');

		$this->template->assign_vars(array(
			'S_HAS_HOOKUP'		=> true,
			'S_IS_HOOKUP_OWNER' => $event['topic_data']['topic_poster'] == $this->user->data['user_id'],
			'S_IS_HOOKUP_MEMBER'=> isset($this->hookup->hookup_users[$this->user->data['user_id']]),
			'S_HAS_DATES'		=> !empty($this->hookup->hookup_dates),
			'S_HAS_USERS'		=> !empty($this->hookup->hookup_users),
			'S_IS_SELF_INVITE'	=> $this->hookup->hookup_self_invite,
			'S_HOOKUP_ACTION'	=> $viewtopic_url,
			'S_ACTIVE_DATE'		=> $this->hookup->hookup_active_date,
			//TODO:
			'ACTIVE_DATE_DATE'	=> isset($datelist[$topic_data['hookup_active_date']]) ? $user->format_date($datelist[$topic_data['hookup_active_date']]['date_time']) : '-',
			'S_NUM_DATES'		=> count($datelist),
			'S_NUM_DATES_PLUS_1'=> count($datelist)+1,
			'U_UNSET_ACTIVE'	=> $viewtopic_url . '&amp;set_active=0',
			'U_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=ucp&amp;field=usernames'),
			'UA_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&form=ucp&field=usernames', false),
			'HOOKUP_ERRORS'		=> (count($hookup_errors) > 0) ? implode('<br />', $hookup_errors) : false,

			'HOOKUP_YES'		=> hookup::HOOKUP_YES,
			'HOOKUP_MAYBE'		=> hookup::HOOKUP_MAYBE,
			'HOOKUP_NO'			=> hookup::HOOKUP_NO,
			'HOOKUP_UNSET'		=> hookup::HOOKUP_UNSET,
			'L_HOOKUP_YES'		=> $user->lang['HOOKUP_STATUS'][hookup::HOOKUP_YES],
			'L_HOOKUP_NO'		=> $user->lang['HOOKUP_STATUS'][hookup::HOOKUP_NO],
			'L_HOOKUP_MAYBE'	=> $user->lang['HOOKUP_STATUS'][hookup::HOOKUP_MAYBE],
			'L_HOOKUP_UNSET'	=> $user->lang['HOOKUP_STATUS'][hookup::HOOKUP_UNSET],
			//one letter versions for summaries
			'L_HOOKUP_Y'		=> $user->lang['HOOKUP_STATUS'][hookup::HOOKUP_YES]{0},
			'L_HOOKUP_N'		=> $user->lang['HOOKUP_STATUS'][hookup::HOOKUP_NO]{0},
			'L_HOOKUP_M'		=> $user->lang['HOOKUP_STATUS'][hookup::HOOKUP_MAYBE]{0},
		));

		// Output dates
		foreach($this->hookup->hookup_dates as $hookup_date)
		{
			$yes_count = $this->hookup->available_sums[$hookup_date['date_id']][hookup::HOOKUP_YES];
			$maybe_count = $this->hookup->available_sums[$hookup_date['date_id']][hookup::HOOKUP_MAYBE];
			$no_count = $this->hookup->available_sums[$hookup_date['date_id']][hookup::HOOKUP_NO];
			//$total_count = $yes_count + $maybe_count + $no_count; //unset_count?
			$total_count = count($userlist);
			$unset_count = $total_count - ($yes_count + $maybe_count + $no_count);

			$yes_percent = $total_count > 0 ? round(($yes_count / $total_count) * 100) : 0;
			$maybe_percent = $total_count > 0 ? round(($maybe_count / $total_count) * 100) : 0;
			$no_percent = $total_count > 0 ? round(($no_count / $total_count) * 100) : 0;
			$unset_percent = 100 - ($yes_percent + $maybe_percent + $no_percent);

			$template->assign_block_vars('date', array(
				'ID'			=> $hookup_date['date_id'],
				'DATE'			=> $user->format_date($hookup_date['date_time'], $user->lang['HOOKUP_DATEFORMAT']),
				'FULL_DATE'		=> $user->format_date($hookup_date['date_time']),
				//'ADDED_AT_BY'		=> sprintf($user->lang['ADDED_AT_BY'], $user->format_date($hookup_date['added_at']), $hookup_date['added_by_name']),
				'YES_COUNT'		=> $yes_count,
				'YES_PERCENT'	=> $yes_percent,
				'MAYBE_COUNT'	=> $maybe_count,
				'MAYBE_PERCENT'	=> $maybe_percent,
				'NO_COUNT'		=> $no_count,
				'NO_PERCENT'	=> $no_percent,
				'UNSET_COUNT'	=> $unset_count,
				'UNSET_PERCENT'	=> $unset_percent,
				'S_IS_ACTIVE'	=> $hookup_date['date_id'] == $topic_data['hookup_active_date'],
				'U_SET_ACTIVE'	=> $viewtopic_url . '&amp;set_active=' . $hookup_date['date_id'],
			));
		}

		// Output details
		foreach($this->hookup->hookup_users as $hookup_user)
		{
			$is_self = ($hookup_user['user_id'] == $this->user->data['user_id']);

			$template->assign_block_vars('user', array(
				'ID'		=> $hookup_user['user_id'],
				'NAME'		=> $hookup_user['username'],
				'COMMENT'	=> isset($comments[$hookup_user['user_id']]) ? $comments[$hookup_user['user_id']] : '',
				'USERNAME_FULL'	=> get_username_string('full', $hookup_user['user_id'], $hookup_user['username'], $hookup_user['user_colour']),
				'IS_SELF'	=> $is_self
			));

			foreach($this->hookup->hookup_dates as $hookup_date)
			{
				$available = isset($this->hookup->hookup_availables[$hookup_user['user_id']][$hookup_date['date_id']])
				? $this->hookup->hookup_availables[$hookup_user['user_id']][$hookup_date['date_id']]
				: HOOKUP_UNSET;

				$template->assign_block_vars('user.date', array(
					'ID'				=> $hookup_date['date_id'],
					'AVAILABLE'			=> $this->user->lang['HOOKUP_STATUS'][$available],
					'STATUS_YES'		=> ($available == HOOKUP_YES),
					'STATUS_NO'			=> ($available == HOOKUP_NO),
					'STATUS_MAYBE'		=> ($available == HOOKUP_MAYBE),
					'STATUS_UNSET'		=> ($available == HOOKUP_UNSET),
					'S_SELECT_NAME'		=> 'available['.$hookup_date['date_id'].']',
					'S_IS_ACTIVE'		=> $hookup_date['date_id'] == $hookup->hookup_active_date,
				));
			}
		}

	}
}
