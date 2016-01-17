<?php

/**
 *
 * @package hookup
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\migrations;

class v_1_0_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\gn36\hookup\migrations\v_1_0_0_compat30x');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('gn36_hookup_reset_last_run', 0)),
			array('config.remove', array('hookup_last_run')),
			array('config.remove', array('hookup_interval')),
			array('custom', array(array($this, 'notification_mails'))),
		);
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array($this, 'remove_notifications'))),
		);
	}

	public function notification_mails()
	{
		// Insert a notification for each user out there
		$sqlary = array(
			"INSERT INTO " . USER_NOTIFICATIONS_TABLE . " (item_type, item_id, method, notify, user_id) SELECT 'gn36.hookup.notification.type.active_date_set', 0, 'notification.method.email', 1, user_id FROM " . USERS_TABLE . " WHERE user_type IN (0,3);",
			"INSERT INTO " . USER_NOTIFICATIONS_TABLE . " (item_type, item_id, method, notify, user_id) SELECT 'gn36.hookup.notification.type.active_date_reset', 0, 'notification.method.email', 1, user_id FROM " . USERS_TABLE . " WHERE user_type IN (0,3);",
			"INSERT INTO " . USER_NOTIFICATIONS_TABLE . " (item_type, item_id, method, notify, user_id) SELECT 'gn36.hookup.notification.type.date_added', 0, 'notification.method.email', 1, user_id FROM " . USERS_TABLE . " WHERE user_type IN (0,3);",
			"INSERT INTO " . USER_NOTIFICATIONS_TABLE . " (item_type, item_id, method, notify, user_id) SELECT 'gn36.hookup.notification.type.invited', 0, 'notification.method.email', 1, user_id FROM " . USERS_TABLE . " WHERE user_type IN (0,3);",
		);
		//$this->db->sql_return_on_error(true);
		foreach ($sqlary as $sql)
		{
			$this->db->sql_query($sql);
		}
		//$this->db->sql_return_on_error(false);
	}

	public function remove_notifications()
	{
		$sql = "DELETE FROM " . USER_NOTIFICATIONS_TABLE . " WHERE item_type " . $this->db->sql_like_expression('gn36.hookup.' . $this->db->get_any_char());
		$this->db->sql_query($sql);
	}
}
