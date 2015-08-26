<?php

/**
 *
 * @package hookup
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\migrations;

class v_1_0_0_compat30x extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array();
	}

	public function effectively_installed()
	{
		return isset($this->config['hookup_last_run']);
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table('hookup_available') => array(
					'COLUMNS' => array(
						'date_id' 		=> array('UINT:11', 0),
						'topic_id'		=> array('UINT:11', 0),
						'user_id'		=> array('UINT:11', 0),
						'available'		=> array('UINT:6', 1),
					),
					'PRIMARY_KEY' => array('date_id', 'user_id'),
					'KEYS' => array(
						'date_id' 		=> array('INDEX', 'date_id'),
						'topic_id' 		=> array('INDEX', 'topic_id'),
						'tu_id' => array('INDEX', array('topic_id', 'user_id')),
					),
				),
				$this->table('hookup_dates') => array(
					'COLUMNS' => array(
						'date_id' 		=> array('UINT:11', null, 'auto_increment'),
						'topic_id'		=> array('UINT:11', 0),
						'date_time'		=> array('UINT:11', 0),
						'text'			=> array('VCHAR:255', null),
					),
					'PRIMARY_KEY' => 'date_id',
					'KEYS' => array(
						'date_id' 	=> array('INDEX', 'date_id'),
						'topic_id' 	=> array('INDEX', 'topic_id'),
					),
				),
				$this->table('hookup_members') => array(
					'COLUMNS' => array(
						'topic_id'		=> array('UINT:11', 0),
						'user_id'		=> array('UINT:11', 0),
						'notify_status'	=> array('UINT:1', 0),
						'comment'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY' => array('topic_id', 'user_id'),
					'KEYS' => array(
						'user_id' 	=> array('INDEX', 'user_id'),
						'topic_id' 	=> array('INDEX', 'topic_id'),
					),
				),
			),

			'add_columns' => array(
				TOPICS_TABLE => array(
					'hookup_enabled' 		=> array('UINT:1', 0),
					'hookup_active_date' 	=> array('UINT:11', null),
					'hookup_self_invite' 	=> array('UINT:1', 0),
					'hookup_autoreset' 		=> array('UINT:1', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table('hookup_available'),
				$this->table('hookup_dates'),
				$this->table('hookup_members'),
			),
			'drop_columns' => array(
				TOPICS_TABLE => array(
					'hookup_enabled',
					'hookup_active_date',
					'hookup_self_invite',
					'hookup_autoreset',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
				array('permission.add', array('f_hookup', false, 'f_read')),
				array('config.add', array('hookup_last_run', '0')),
				array('config.add', array('hookup_interval', '86400')),
		);
	}

	private function table($name)
	{
		return $this->table_prefix . $name;
	}
}
