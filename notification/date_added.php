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

class date_added extends base
{
	protected $language_key = 'HOOKUP_NOTIFY_INVITED';
	public static $notification_option = array(
		'lang' 	=> 'HOOKUP_NOTIFY_INVITED_OPTION',
		'group'	=> 'NOTIFICATION_GROUP_HOOKUP',
	);

	public function get_type()
	{
		return 'gn36.hookup.notification.type.date_added';
	}

	public function is_available()
	{
		return $this->auth->acl_getf_global('f_hookup');
	}

	public function find_users_for_notification($notification_data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users' => array(),
		), $options);

		$users = array($this->get_data('invited_user'));

		if(empty($users))
		{
			// This should not happen really - this means we invited nobody.
			return array();
		}
		$users = array_unique($users);

		return $this->check_user_notification_options($users, $options);
	}

	public function get_title()
	{
		$username = $this->user_loader->get_username($this->get_data('user_id'), 'no_profile');
		return $this->user->lang($this->language_key, $username, $this->get_data('topic_title'));
	}

	public function get_email_template()
	{
		return '@gn36_hookup/hookup_added';
	}

	//public function get_reference()

	public function get_email_template_variables()
	{
		$vars = parent::get_email_template_variables();
		$vars['USERNAME'] = $this->get_data('invited_user');
	}

	public function create_insert_array($notification_data, $pre_create_data = array())
	{
		$this->set_data('invited_user', $notification_data['invited_user']);

		parent::create_insert_array($notification_data, $pre_create_data);
	}
}