<?php
/**
*
* @package hookup
* @copyright (c) 2008 Martin Beckmann (gn#36) http://www.goose-necks.de
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
		if(!$row = $db->sql_fetchrow($result))
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
		while($row = $db->sql_fetchrow($result))
		{
			$this->hookup_users[$row['user_id']] = $row;
		}

		//load dates for this hookup
		$sql = 'SELECT date_id, date_time
				FROM ' . $this->hookup_dates_table . '
				WHERE topic_id=' . $topic_id . '
				ORDER BY date_time ASC';
		$result = $db->sql_query($sql);
		//associative array date_id => date_row
		$this->hookup_dates = array();
		while($row = $db->sql_fetchrow($result))
		{
			$this->hookup_dates[$row['date_id']] = $row;
		}

		//load available info
		$this->hookup_available_sums = array();
		foreach($this->hookup_dates as $date)
		{
			$this->hookup_available_sums[$date['date_id']] = array(hookup::HOOKUP_YES=>0, hookup::HOOKUP_MAYBE=>0, hookup::HOOKUP_NO=>0);
		}

		$sql = 'SELECT date_id, user_id, available
				FROM ' . $this->hookup_available_table . '
				WHERE topic_id=' . $topic_id;
		$result = $db->sql_query($sql);
		$this->hookup_availables = array();
		while($row = $db->sql_fetchrow($result))
		{
			$this->hookup_availables[$row['user_id']][$row['date_id']] = $row['available'];
			$this->hookup_available_sums[$row['date_id']][$row['available']]++;
		}

		return true;
	}

	public function add_groups($group_ids)
	{
		$db = $this->db;

		if(!$group_ids)
		{
			return;
		}
		if(!is_array($group_ids))
		{
			$group_ids = array($group_ids);
		}
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
			if(!isset($this->hookup_users[$row['user_id']]))
			{
				$this->hookup_users[$row['user_id']] = array('user_id' => $row['user_id'],
					'notify_status' => 0,
					'comment' => ''
				);
			}
		}
		$db->sql_freeresult($result);
	}

	public function add_date($date)
	{
		foreach($this->hookup_dates as $key => $entry)
		{
			if($entry['date_time'] == $date)
			{
				//this entry allready exists
				return;
			}
		}
		//Doesn't exist, so add:
		$this->hookup_dates[] = array('date_time' => $date);
	}

	public function get_date_id($date)
	{
		if(!$this->hookup_dates)
		{
			return NULL;
		}

		foreach($this->hookup_dates as $key => $entry)
		{
			if($entry['date_time'] == $date)
			{
				//This is the entry we are looking for:
				return $key;
			}
		}

		return false;
	}

	public function set_user($user_id, $date_id, $value = hookup::HOOKUP_MAYBE, $comment = '')
	{
		$this->hookup_users[$user_id] = array('user_id' => $user_id,
			'notify_status' => 0,
			'comment' => $comment,
			);
		if(isset($this->hookup_dates[$date_id]))
		{
			$this->hookup_availables[$user_id][$date_id] = $value;
		}
		else
		{
			return false;
		}
		return true;
	}

	public function remove_date($date = 0, $date_id = 0)
	{
		if(!$this->hookup_dates || (!$date && !$date_id))
		{
			return;
		}
		if($date)
		{
			foreach($this->hookup_dates as $key => $entry)
			{
				if($entry['date_time'] == $date)
				{
					//This entry needs to be removed
					$this->_remove_date_from_userlist($entry['date_id']);
					unset($this->hookup_dates[$key]);
				}
			}
		}
		else
		{
			if(isset($this->hookup_dates[$date_id]))
			{
				$this->_remove_date_from_userlist($date_id);
				unset($this->hookup_dates[$date_id]);
			}
		}
	}

	protected function _remove_date_from_userlist($date_id)
	{
		foreach($this->hookup_availables as $key => $date_array)
		{
			if(isset($date_array[$date_id]))
			{
				unset($this->hookup_availables[$key][$date_id]);
			}
		}
	}

	/**
	 * Stores the changes made in the database. Does NOT notify any users. Returns an array of the changes made.
	 *
	 * @return array changes
	 */
	public function submit($reload_data = true, $return_changes = false)
	{
		$db = $this->db;
		$changed = array();
		$topic_id = $this->topic_id;

		//For checking for differences, just load the old version from database:
		if($return_changes)
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

		//start with updating the topic:
		$row = array(
			'hookup_enabled' => $this->hookup_enabled,
			'hookup_self_invite' => $this->hookup_self_invite,
			'hookup_active_date' => $this->hookup_active_date,
			'hookup_autoreset' => $this->hookup_autoreset,
			);
		$sql = 'UPDATE ' . TOPICS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $row) . " WHERE topic_id = $topic_id";
		$db->sql_query($sql);
		if(!$db->sql_affectedrows())
		{
			//the topic does not exist?
			$sql = 'SELECT topic_id FROM ' . TOPICS_TABLE . ' WHERE topic_id = ' . $topic_id;
			$result = $db->sql_query($sql);
			if(!$db->sql_fetchrow($result))
			{
				return false;
			}
		}

		//Now update the users
		$sql = 'DELETE FROM ' . $this->hookup_members_table . " WHERE topic_id = $topic_id";
		$db->sql_query($sql);
		foreach($this->hookup_users as $user_id => $user)
		{
			//Insert:
			$user['topic_id'] = $topic_id;
			$sql = 'INSERT INTO ' . $this->hookup_members_table . ' ' . $db->sql_build_array('INSERT', $user);
			$db->sql_query($sql);
		}

		//Update the dates:
		$sql = 'DELETE FROM ' . $this->hookup_dates_table . " WHERE topic_id = $topic_id";
		$db->sql_query($sql);
		foreach($this->hookup_dates as $date_id => $date)
		{
			//Insert (uses old ID if available):
			if(isset($date['date_id']) && !$date['date_id'])
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
		if($this->hookup_availables)
		{
			foreach($this->hookup_availables as $user_id => $availables)
			{
				foreach($availables as $date_id => $available)
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

		//Now update this object:
		if($reload_data)
		{
			$this->load_hookup($topic_id);
		}

		//Done, return changes:

		return $return_changes ? $changed : true;
	}
}

?>