<?php
/**
 *
 * @package hookup
 * @copyright (c) 2015 Martin Beckmann
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 * See https://www.phpbb.com/community/viewtopic.php?f=461&t=2259916
 */


namespace gn36\hookup\notification;

class base extends \phpbb\notification\type\base
{
	/** @var \gn36\hookup\functions\hookup */
	protected $hookup;

	protected $language_key = 'HOOKUP_NOTIFY_BASE';
	public static $notification_option = array(
		'lang' 	=> 'HOOKUP_NOTIFY_OPTION',
		'group'	=> 'NOTIFICATION_GROUP_MISCELLANEOUS',
	);

	/**
	 * Notification Type Base Constructor
	 *
	 * @param \phpbb\user_loader $user_loader
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\cache\driver\driver_interface $cache
	 * @param \phpbb\user $user
	 * @param \phpbb\auth\auth $auth
	 * @param \phpbb\config\config $config
	 * @param string $phpbb_root_path
	 * @param string $php_ext
	 * @param string $notification_types_table
	 * @param string $notifications_table
	 * @param string $user_notifications_table
	 * @return \phpbb\notification\type\base
	 */
	public function __construct(\phpbb\user_loader $user_loader, \phpbb\db\driver\driver_interface $db, \phpbb\cache\driver\driver_interface $cache, $user, \phpbb\auth\auth $auth, \phpbb\config\config $config, \gn36\hookup\functions\hookup $hookup, $phpbb_root_path, $php_ext, $notification_types_table, $notifications_table, $user_notifications_table)
	{
		parent::__construct($user_loader, $db, $cache, $user, $auth, $config, $phpbb_root_path, $php_ext, $notification_types_table, $notifications_table, $user_notifications_table);
		$this->hookup = $hookup;
	}

	public function get_type()
	{
		return 'gn36.hookup.notification.type.base';
	}

	public function is_available()
	{
		return false;
	}

	public static function get_item_id($notification_data)
	{
		return $notification_data['topic_id'];
	}

	public static function get_item_parent_id($notification_data)
	{
		return (!empty($notification_data['forum_id']) ? $notification_data['forum_id'] : 0);
	}

	public function find_users_for_notification($notification_data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users' => array(),
		), $options);

		// We usually wish to notify the users who are listed in a hookup:
		$this->hookup->load_hookup($notification_data['topic_id']);

		$users = $this->hookup->hookup_users;

		if(empty($users))
		{
			// Maybe the data was entered by someone else?
			return array();
		}
		$users = array_unique($users);

		return $this->check_user_notification_options($users, $options);
	}

	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_id'));
		//return "<img class='avatar' alt='hookup' src='{$this->phpbb_root_path}ext/gn36/hookup/fixtures/notify_icon.jpg' />";
	}

	public function get_title()
	{
		$username = $this->user_loader->get_username($this->get_data('user_id'), 'no_profile');
		return $this->user->lang($this->language_key, $username, $this->get_data('topic_title'), $this->get_data('date'), $this->get_data('yes'), $this->get_data('no'), $this->get_data('maybe'));
	}

	function users_to_query()
	{
		return array($this->get_data('user_id'));
	}

	public function get_url()
	{
		return append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}?t={$this->get_data('topic_id')}");
	}

	public function get_redirect_url()
	{
		return $this->get_url();
	}

	public function get_email_template()
	{
		//return '@gn36_hookup/mail_notify';
		return false;
	}

	//public function get_reference()

	public function get_email_template_variables()
	{
		// TODO: EMAIL_SIG
		return array(
			'USERNAME' => $this->user_loader->get_username($this->get_data('user_id'), 'username'),
			'TOPIC_TITLE' => $this->get_data('topic_title'),
			'SITENAME' => $this->config['sitename'],
			'U_TOPIC' => generate_board_url() . "{$this->phpbb_root_path}viewtopic.{$this->php_ext}?t={$this->get_data('topic_id')}",
		);
	}

	public function create_insert_array($notification_data, $pre_create_data = array())
	{
		// This should be the user who has taken the action:
		if(isset($notification_data['user_id']))
		{
			$this->set_data('user_id', $notification_data['user_id']);
		}
		else
		{
			$this->set_data('user_id', $this->user->data['user_id']);
		}

		if(isset($notification_data['date']))
		{
			$this->set_data('date', $notification_data['date']);
		}
		else
		{
			$this->set_data('date', 0);
		}

		if(isset($notification_data['yes']))
		{
			$this->set_data('yes', $notification_data['yes']);
			$this->set_data('no', $notification_data['no']);
			$this->set_data('maybe', $notification_data['maybe']);
		}
		else
		{
			$this->set_data('yes', 0);
			$this->set_data('no', 0);
			$this->set_data('maybe', 0);
		}

		$this->set_data('topic_id', $notification_data['topic_id']);
		if(isset($notification_data['forum_id']))
		{
			$this->set_data('forum_id', $notification_data['forum_id']);
		}
		else
		{
			$this->set_data('forum_id', 0);
		}
		$this->set_data('topic_title', $notification_data['topic_title']);

		return parent::create_insert_array($notification_data, $pre_create_data);
	}

}