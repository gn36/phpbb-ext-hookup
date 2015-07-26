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

class posting implements EventSubscriberInterface
{

	static public function getSubscribedEvents()
	{
		return array(
			//'core.posting_modify_submit_post_after'	=> 'submit_post',
			'core.submit_post_modify_sql_data'	=> 'submit_post',
			'core.posting_modify_template_vars'	=> 'posting_display_template',
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

	function __construct(\gn36\hookup\functions\hookup $hookup, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request)
	{
		$this->hookup = $hookup;
		$this->template = $template;
		$this->db = $db;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
	}

	/**
	 * Stores the hookup data given in posting.php if necessary.
	 *
	 * @param unknown $event
	 */
	public function submit_post($event)
	{
		// Check permissions
		if (!$this->auth->acl_get('f_hookup', $event['data']['forum_id']) && !$this->auth->acl_get('m_edit', $event['data']['forum_id']))
		{
			return;
		}

		// We store only if we are creating a new topic or editing the first post of an existing one
		if (($event['post_mode'] != 'post' && $event['post_mode'] != 'edit_topic' && $event['post_mode'] != 'edit_first_post'))
		{
			return;
		}

		$sql_data = $event['sql_data'];
		$hookup_enabled = $this->request->is_set_post('hookup_enabled');

		if ($event['post_mode'] == 'edit')
		{
			$this->hookup->load_hookup($event['data']['topic_id']);

			$no_data = empty($this->hookup->hookup_users) && empty($this->hookup->hookup_dates) && empty($this->hookup->hookup_availables);

			// Only honor user setting on enable/disable if the hookup is inactive or not set
			if ($this->hookup->hookup_enabled || $no_data)
			{
				$hookup_enabled = $this->hookup->hookup_enabled;
			}

		}

		$sql_data[TOPICS_TABLE]['sql'] = array_merge($sql_data[TOPICS_TABLE]['sql'], array(
			'hookup_enabled' => $hookup_enabled,
			'hookup_self_invite' => $this->request->is_set_post('hookup_self_invite'),
			'hookup_autoreset' => $this->request->is_set_post('hookup_autoreset'),
		));

		$event['sql_data'] = $sql_data;

	}

	/**
	 * Displays hookup data in posting.php
	 *
	 * @param unknown $event
	 */
	public function posting_display_template($event)
	{
		// Check permissions
		if (!$this->auth->acl_get('f_hookup', $event['forum_id']) && !$this->auth->acl_get('m_edit', $event['forum_id']))
		{
			return;
		}

		// Check for first post
		if (isset($event['post_data']['topic_first_post_id']) && (!isset($event['post_data']['post_id']) || $event['post_data']['topic_first_post_id'] != $event['post_data']['post_id']))
		{
			return;
		}

		$this->user->add_lang_ext('gn36/hookup', 'hookup');

		if (isset($event['topic_id']) && $event['topic_id'])
		{
			$this->hookup->load_hookup($event['topic_id']);
		}

		$is_inactive = !empty($this->hookup->hookup_users) || !empty($this->hookup->hookup_dates);
		$is_inactive = $is_inactive && $this->hookup->hookup_enabled == false;

		$this->template->assign_vars(array(
			'S_HOOKUP_ALLOWED' 				=> true,
			'S_TOPIC_HAS_HOOKUP' 			=> $this->hookup->hookup_enabled,
			'S_TOPIC_HAS_INACTIVE_HOOKUP' 	=> $is_inactive,
			'S_HOOKUP_CHECKED' 				=> $this->hookup->hookup_enabled ? "checked='checked'" : '',
			'S_HOOKUP_SELF_INVITE_CHECKED' 	=> $this->hookup->hookup_self_invite ?  "checked='checked'" : '',
			'S_HOOKUP_AUTORESET_CHECKED' 	=> $this->hookup->hookup_autoreset ? "checked='checked'" : '',
		));
	}
}
