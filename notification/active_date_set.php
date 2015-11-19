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

class active_date_set extends base
{
	protected $language_key = 'HOOKUP_NOTIFY_ACTIVE_DATE_SET';
	public static $notification_option = array(
		'lang' 	=> 'HOOKUP_NOTIFY_ACTIVE_DATE_SET_OPTION',
		'group'	=> 'NOTIFICATION_GROUP_HOOKUP',
	);

	public function get_type()
	{
		return 'gn36.hookup.notification.type.active_date_set';
	}

	public function is_available()
	{
		return $this->auth->acl_getf_global('f_hookup');
	}

	public function get_email_template()
	{
		return '@gn36_hookup/hookup_active_date';
	}
}
