<?php
/**
*
* @package hookup
* @copyright (c) 2008 Martin Beckmann (gn#36) http://phpbb.gn36.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace gn36\hookup\functions;

/**
 * helps managing hookups in bots in an easy way.
 *
 */
class hookup
{
	const HOOKUP_UNSET = 0;
	const HOOKUP_YES   = 1;
	const HOOKUP_NO    = 2;
	const HOOKUP_MAYBE = 3;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	protected $hookup_members_table;
	protected $hookup_dates_table;
	protected $hookup_available_table;

	var $topic_id;
	var $hookup_enabled = 0;
	var $hookup_active_date = 0;
	var $hookup_self_invite = 0;
	var $hookup_autoreset = 0;
	var $hookup_dates;
	var $hookup_users;
	var $hookup_availables;

	var $hookup_available_sums;

	public function __construct(\phpbb\db\driver\driver_interface $db, $hookup_members_table, $hookup_dates_table, $hookup_available_table)
	{
		$this->hookup_dates = array();
		$this->hookup_users = array();
		$this->hookup_availables = array();

		$this->db = $db;
		$this->hookup_members_table		= $hookup_members_table;
		$this->hookup_dates_table		= $hookup_dates_table;
		$this->hookup_available_table	= $hookup_available_table;
	}

	/**
	 * Loads all available data on a hookup from the database. Returns false if the topic does not exist.
	 *
	 * @param int $topic_id
	 * @return bool success
	 */
	public function load_hookup($topic_id)
	{
		$db = $this->db;

		$this->topic_id = $topic_id;
		$this->hookup_dates = array();
		$this->hookup_users = array();
		$this->hookup_availables = array();

		$sql = 'SELECT hookup_enabled, hookup_active_date, hookup_self_invite, hookup_autoreset FROM ' . TOPICS_TABLE . ' WHERE topic_id = ' . $topic_id;
		$result = $db->sql_query($sql);
		if (!$row = $db->sql_fetchrow($result))
		{
			//Thema existiert nicht:
			return false;
		}
		$this->hookup_active_date = $row['hookup_active_date'];
		$this->hookup_enabled = $row['hookup_enabled'];
		$this->hookup_self_invite = $row['hookup_self_invite'];
		$this->hookup_autoreset = $row['hookup_autoreset'];

		//load users
		$sql = 'SELECT user_id, notify_status, comment
				FROM ' . $this->hookup_members_table . '
				WHERE topic_id=' . $topic_id;
		$result = $db->sql_query($sql);
		//associative array user_id => user_row
		$this->hookup_users = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$this->hookup_users[$row['user_id']] = $row;
		}

		//load dates for this hookup
		$sql = 'SELECT date_id, date_time, text
				FROM ' . $this->hookup_dates_table . '
				WHERE topic_id=' . $topic_id . '
				ORDER BY date_time ASC';
		$result = $db->sql_query($sql);
		//associative array date_id => date_row
		$this->hookup_dates = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$this->hookup_dates[$row['date_id']] = $row;
		}

		//load available info
		$this->hookup_available_sums = array();
		foreach ($this->hookup_dates as $date)
		{
			$this->hookup_available_sums[$date['date_id']] = array(hookup::HOOKUP_YES=>0, hookup::HOOKUP_MAYBE=>0, hookup::HOOKUP_NO=>0);
		}

		$sql = 'SELECT date_id, user_id, available
				FROM ' . $this->hookup_available_table . '
				WHERE topic_id=' . $topic_id;
		$result = $db->sql_query($sql);
		$this->hookup_availables = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$this->hookup_availables[$row['user_id']][$row['date_id']] = $row['available'];
			$this->hookup_available_sums[$row['date_id']][$row['available']]++;
		}

		return true;
	}

	/**
	 * Adds whole groups to hookup. No notification as of yet.
	 * @param array|integer $group_ids
	 */
	public function add_groups($group_ids)
	{
		$db = $this->db;

		if (!$group_ids)
		{
			return;
		}
		if (!is_array($group_ids))
		{
			$group_ids = array($group_ids);
		}
		array_map('intval', $group_ids);

		//get some userdata
		$sql = 'SELECT u.user_id
			FROM ' . USER_GROUP_TABLE . ' ug
			JOIN ' . USERS_TABLE . ' u
				ON (u.user_id = ug.user_id)
			WHERE ' . $db->sql_in_set('ug.group_id', $group_ids) . '
				AND ug.user_pending = 0';
		$result = $db->sql_query($sql);

		$new_users = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if (!isset($this->hookup_users[$row['user_id']]))
			{
				$this->hookup_users[$row['user_id']] = array(
					'user_id' 		=> $row['user_id'],
					'notify_status' => 0,
					'comment' 		=> ''
				);
			}
		}
		$db->sql_freeresult($result);
	}

	/**
	 * Add a user to the hookup. If user already exists, resets comment and notify_status to given values.
	 * @param int $user_id
	 * @param string $comment
	 * @param int $notify_status
	 */
	public function add_user($user_id, $comment = '', $notify_status = 0)
	{
		$this->hookup_users[$user_id] = array(
			'user_id' 		=> (int) $user_id,
			'notify_status' => (int) $notify_status,
			'comment' 		=> $comment,
		);
	}

	/**
	 * Add a new date to the hookup
	 * @param int $date
	 * @param string $text
	 */
	public function add_date($date, $text = null)
	{
		foreach ($this->hookup_dates as $key => $entry)
		{
			if (($text == null && $entry['date_time'] == $date) || ($date == '0' && $entry['text'] == $text))
			{
				//this entry allready exists
				return false;
			}
		}
		//Doesn't exist, so add:
		$this->hookup_dates[] = array(
			'date_time'	=> $date,
			'text'		=> $text,
		);

		return true;
	}

	/**
	 * Return index of given date in list. Returns Null if list is empty, false if entry not found.
	 * @param int $date
	 * @return NULL|int|boolean
	 */
	public function get_date_id($date)
	{
		if (!$this->hookup_dates)
		{
			return null;
		}

		foreach ($this->hookup_dates as $key => $entry)
		{
			if ($entry['date_time'] == $date || $entry['text'] == $date)
			{
				//This is the entry we are looking for:
				return $key;
			}
		}

		return false;
	}

	/**
	 * Updates the availability sums based upon the data in the arrays.
	 *
	 * This is done automatically when data is loaded from the db.
	 */
	public function update_available_sums()
	{
		$this->hookup_available_sums = array();

		foreach ($this->hookup_dates as $date_id => $data)
		{
			$this->hookup_available_sums[$date_id] = array(hookup::HOOKUP_YES=>0, hookup::HOOKUP_MAYBE=>0, hookup::HOOKUP_NO=>0);
		}

		foreach ($this->hookup_availables as $user_id => $date_list)
		{
			foreach ($date_list as $date_id => $availability)
			{
				$this->hookup_available_sums[$date_id][$availability]++;
			}
		}
	}

	/**
	 * Update a users status on a specific date
	 *
	 * @param int $user_id
	 * @param int $date_id
	 * @param int $value
	 * @return boolean
	 */
	public function set_user_date($user_id, $date_id, $value = hookup::HOOKUP_MAYBE)
	{
		if (!isset($this->hookup_users[$user_id]))
		{
			return false;
		}

		if (isset($this->hookup_dates[$date_id]))
		{
			$this->hookup_availables[$user_id][$date_id] = $value;
		}
		else
		{
			return false;
		}
		return true;
	}

	/**
	 * Set User basic data
	 * @param int $user_id
	 * @param string $notify_status
	 * @param string $comment
	 * @return boolean
	 */
	public function set_user_data($user_id, $notify_status = null, $comment = null)
	{
		if (!isset($this->hookup_users[$user_id]))
		{
			return false;
		}

		if ($notify_status !== null)
		{
			$this->hookup_users[$user_id]['notify_status'] = $notify_status;
		}

		if ($comment !== null)
		{
			$this->hookup_users[$user_id]['comment'] = $comment;
		}

		return true;
	}

	/**
	 * Removes a date from the list
	 *
	 * @param number $date
	 * @param number $date_id
	 */
	public function remove_date($date = 0, $date_id = 0)
	{
		if (!$this->hookup_dates || (!$date && !$date_id))
		{
			return;
		}
		if ($date)
		{
			foreach ($this->hookup_dates as $key => $entry)
			{
				if ($entry['date_time'] == $date || $entry['text'] == $date)
				{
					//This entry needs to be removed
					$this->_remove_date_from_userlist($entry['date_id']);
					unset($this->hookup_dates[$key]);
				}
			}
		}
		else
		{
			if (isset($this->hookup_dates[$date_id]))
			{
				$this->_remove_date_from_userlist($date_id);
				unset($this->hookup_dates[$date_id]);
			}
		}
	}

	/**
	 * Helper for remove_date
	 * @param unknown $date_id
	 */
	protected function _remove_date_from_userlist($date_id)
	{
		foreach ($this->hookup_availables as $key => $date_array)
		{
			if (isset($date_array[$date_id]))
			{
				unset($this->hookup_availables[$key][$date_id]);
			}
		}
	}

	/**
	 * Remove user from hookup
	 *
	 * @param int $user_id
	 */
	public function remove_user($user_id)
	{
		if (!isset($this->hookup_users[$user_id]))
		{
			return;
		}

		unset($this->hookup_users[$user_id]);
		unset($this->hookup_availables[$user_id]);
	}

	/**
	 * Stores the changes made in the database. Does NOT notify any users. Returns an array of the changes made.
	 *
	 * @param string $reload_data
	 * @param string $return_changes
	 * @param boolean $force_run
	 * @return boolean|array
	 */
	public function submit($reload_data = true, $return_changes = false, $force_run = false)
	{
		$db = $this->db;
		$changed = array();
		$topic_id = $this->topic_id;

		//For checking for differences, just load the old version from database:
		if ($return_changes)
		{
			$old = new hookup();
			$old->load_hookup($this->topic_id);
			$changed = ($this->hookup_enabled == $old->hookup_enabled) ? $changed : array_merge($changed, array('hookup_enabled'));
			$changed = ($this->hookup_self_invite == $old->hookup_self_invite) ? $changed : array_merge($changed, array('hookup_self_invite'));
			$changed = ($this->hookup_active_date == $old->hookup_active_date) ? $changed : array_merge($changed, array('hookup_active_date'));
			$changed = ($this->hookup_dates == $old->hookup_dates) ? $changed : array_merge($changed, array('hookup_dates'));
			$changed = ($this->hookup_availables == $old->hookup_availables) ? $changed : array_merge($changed, array('hookup_availables'));
			$changed = ($this->hookup_users == $old->hookup_users) ? $changed : array_merge($changed, array('hookup_users'));
			$changed = ($this->hookup_autoreset == $old->hookup_autoreset) ? $changed : array_merge($changed, array('hookup_autoreset'));
		}
		else
		{
			$changed = array();
		}

		// This should all be one transaction:
		$db->sql_transaction('begin');

		//start with updating the topic:
		$row = array(
			'hookup_enabled' => $this->hookup_enabled,
			'hookup_self_invite' => $this->hookup_self_invite,
			'hookup_active_date' => $this->hookup_active_date,
			'hookup_autoreset' => $this->hookup_autoreset,
			);
		$sql = 'UPDATE ' . TOPICS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $row) . " WHERE topic_id = $topic_id";

		// This might fail if the topic does not exist anymore
		$db->sql_query($sql);
		if (!$db->sql_affectedrows() && !$force_run)
		{
			//the topic does not exist?
			$sql = 'SELECT topic_id FROM ' . TOPICS_TABLE . ' WHERE topic_id = ' . $topic_id;
			$result = $db->sql_query($sql);
			if (!$db->sql_fetchrow($result))
			{
				$db->sql_transaction('rollback');
				return false;
			}
		}

		//Now update the users
		$sql = 'DELETE FROM ' . $this->hookup_members_table . " WHERE topic_id = $topic_id";
		$db->sql_query($sql);
		foreach ($this->hookup_users as $user_id => $user)
		{
			//Insert:
			$user['topic_id'] = $topic_id;
			$sql = 'INSERT INTO ' . $this->hookup_members_table . ' ' . $db->sql_build_array('INSERT', $user);
			$db->sql_query($sql);
		}

		//Update the dates:
		$sql = 'DELETE FROM ' . $this->hookup_dates_table . " WHERE topic_id = $topic_id";
		$db->sql_query($sql);

		foreach ($this->hookup_dates as $date_id => $date)
		{

			//Insert (uses old ID if available):
			if (isset($date['date_id']) && !$date['date_id'])
			{
				unset($date['date_id']);
			}
			$date['topic_id'] = $topic_id;
			$sql = 'INSERT INTO ' . $this->hookup_dates_table . ' ' . $db->sql_build_array('INSERT', $date);
			$db->sql_query($sql);
		}

		//Update the entries for availability:
		$sql = 'DELETE FROM ' . $this->hookup_available_table . " WHERE topic_id = $topic_id";
		$db->sql_query($sql);
		if ($this->hookup_availables)
		{
			foreach ($this->hookup_availables as $user_id => $availables)
			{
				foreach ($availables as $date_id => $available)
				{
					$rows[] = array('user_id' => $user_id,
						'topic_id' => $topic_id,
						'date_id' => $date_id,
						'available' => $available
						);
				}
			}
			$db->sql_multi_insert($this->hookup_available_table, $rows);
		}

		// Done, finish the transaction:
		$db->sql_transaction('commit');

		//Now update this object:
		if ($reload_data)
		{
			$this->load_hookup($topic_id);
		}

		//Done, return changes:

		return $return_changes ? $changed : true;
	}

	/**
	 * Delete the hookup data (to disable only, set hookup_enabled = false)
	 *
	 * This is equivalent to emptying the dates, users and available arrays.
	 */
	public function delete()
	{
		$this->hookup_enabled = false;
		$this->hookup_availables = array();
		$this->hookup_dates = array();
		$this->hookup_users = array();
		$this->hookup_available_sums = array();
		$this->hookup_autoreset = false;
		$this->hookup_self_invite = false;

		// This deletes the data (and also works if the topic does not exist anymore :)
		return $this->submit(false, false, true);

	}

	/**
	 * Efficiently delete one or multiple hookups directly in DB.
	 *
	 * @param array $topic_ids
	 */
	public function delete_in_db($topic_ids, $update_topics = true)
	{
		if (!is_array($topic_ids))
		{
			$topic_ids = array($topic_ids);
		}
		$sql = "DELETE FROM {$this->hookup_members_table} WHERE " . $this->db->sql_in_set('topic_id', $topic_ids);
		$this->db->sql_query($sql);

		$sql = "DELETE FROM {$this->hookup_dates_table} WHERE " . $this->db->sql_in_set('topic_id', $topic_ids);
		$this->db->sql_query($sql);

		$sql = "DELETE FROM {$this->hookup_available_table} WHERE " . $this->db->sql_in_set('topic_id', $topic_ids);
		$this->db->sql_query($sql);

		if ($update_topics)
		{
			$sql = "UPDATE " . TOPICS_TABLE . " SET hookup_enabled = 0 WHERE " . $this->db->sql_in_set('topic_id', $topic_ids);
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Merge hookup on topic merge.
	 *
	 * By default, only empty topics are merged.
	 *
	 * @param array|int $src_topic_ids
	 * @param array|int $dest_topic_ids
	 * @param bool $force_move
	 */
	public function merge_in_db($src_topic_ids, $dest_topic_ids, $force_move = false)
	{
		if (!is_array($src_topic_ids))
		{
			$src_topic_ids = array($src_topic_ids);
		}

		if (!$force_move)
		{
			$sql = 'SELECT topic_id
				FROM ' . POSTS_TABLE . '
				WHERE ' . $this->db->sql_in_set('topic_id', $src_topic_ids);
			$result = $this->db->sql_query($sql);

			// We don't merge topics that have posts left
			$dont_merge = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$dont_merge[] = $row['topic_id'];
			}
			$merge = array_diff($src_topic_ids, $dont_merge);
			$this->db->sql_freeresult($result);
		}
		else
		{
			$merge = $src_topic_ids;
		}

		if (is_array($dest_topic_ids))
		{
			// There must be exactly one destination for each src
			if (count($src_topic_ids) != count($dest_topic_ids))
			{
				throw new \phpbb\exception\runtime_exception('ILLEGAL_MERGE_COUNT');
			}

			foreach ($dest_topic_ids as $index => $destination)
			{
				if (in_array($src_topic_ids[$index], $merge))
				{
					$src_hookup = new hookup($this->db, $this->hookup_members_table, $this->hookup_dates_table, $this->hookup_available_table);
					if ($src_hookup->load_hookup($src_topic_ids[$index]))
					{
						$src_hookup->merge($destination);
					}
				}
			}
			return;
		}

		// hopefully, we never merge a huge number of topics at once:
		foreach ($merge as $src)
		{
			$src_hookup = new hookup($this->db, $this->hookup_members_table, $this->hookup_dates_table, $this->hookup_available_table);
			if ($src_hookup->load_hookup($src))
			{
				$src_hookup->merge($dest_topic_ids);
			}
		}

	}

	/**
	 * Merge hookup into different topic
	 *
	 * @param int $to_topic_id
	 */
	public function merge($to_topic_id)
	{
		if ($this->topic_id == $to_topic_id && $to_topic_id != 0)
		{
			return true;
		}

		if ($to_topic_id == 0)
		{
			$this->delete();
		}

		// Load destination hookup first:
		$dest = new hookup($this->db, $this->hookup_members_table, $this->hookup_dates_table, $this->hookup_available_table);

		// Only do this if the topic exists
		if ($dest->load_hookup($to_topic_id))
		{
			$dest->hookup_enabled = ($dest->hookup_enabled || $this->hookup_enabled);
			$dest->hookup_active_date = ($dest->hookup_active_date ? $dest->hookup_active_date : $this->hookup_active_date);
			$dest->hookup_autoreset = ($dest->hookup_enabled ? $dest->hookup_autoreset : $this->hookup_autoreset);
			$dest->hookup_self_invite = ($dest->hookup_enabled ? $dest->hookup_self_invite : $this->hookup_self_invite);
			foreach ($this->hookup_dates as $date)
			{
				$dest->add_date($date['date_time'], $date['text']);
			}
			foreach ($this->hookup_users as $user)
			{
				$dest->add_user($user['user_id'], $user['comment'], $user['notify_status']);
			}
			// We need to store and reload to ensure to have date ids
			$dest->submit();

			foreach ($this->hookup_availables as $user => $availables)
			{
				foreach ($availables as $date => $available)
				{
					$dest->set_user_date($user, $dest->get_date_id($this->hookup_dates[$date]['date_time']), $available);
				}
			}

			$this->delete();
			$dest->submit(false);
			$this->load_hookup($to_topic_id);

			return true;
		}

		return false;
	}

}
