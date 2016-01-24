<?php

/**
 *
 * @package testing
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

use \gn36\hookup\functions\hookup;

class gn36_hookup_hookup_test extends phpbb_database_test_case
{
	static protected function setup_extensions()
	{
		return array('gn36/hookup');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/hookup_entries.xml');
	}

	public function loadProvider()
	{
		return array(
			'full' => array(3, true,
				// Topic data
				array('hookup_enabled' => 1, 'hookup_autoreset' => 1, 'hookup_active_date' => 0, 'hookup_self_invite' => 0),
				// Dates date => (date, date_time, text)
				array(
					2 => array('date_id' => 2, 'date_time' => 0, 'text' => null),
				),
				// Members (user, comment, notify)
				array(
					2 => array('user_id' => 2, 'comment' => 'commentdata', 'notify_status' => 0),
				),
				// Entries user => (date => available)
				array(
					2 => array(2 => 1),
				),
				// Available sums
				array(array(hookup::HOOKUP_YES => 1, hookup::HOOKUP_MAYBE => 0, hookup::HOOKUP_NO => 0)),
			),
			'enabled_and_date' => array(1, true,
				array('hookup_enabled' => 1, 'hookup_autoreset' => 0, 'hookup_active_date' => 0, 'hookup_self_invite' => 0),
				array(
					1 => array('date_id' => 1, 'date_time' => 1, 'text' => null),
				),
				array(),
				array(),
				array(array(hookup::HOOKUP_YES => 0, hookup::HOOKUP_MAYBE => 0, hookup::HOOKUP_NO => 0)),
			),
			'disabled' => array(5, true,
				array('hookup_enabled' => 0, 'hookup_autoreset' => 0, 'hookup_active_date' => 0, 'hookup_self_invite' => 0),
				array(),
				array(),
				array(),
				array(),
			),
			'nonexistent' => array(10, false,
				array('hookup_enabled' => 0, 'hookup_autoreset' => 0, 'hookup_active_date' => 0, 'hookup_self_invite' => 0),
				array(),
				array(),
				array(),
				array(),
			),
		);
	}

	public function test_construct()
	{
		$hookup = $this->get_hookup();
		$this->assertInstanceOf('\gn36\hookup\functions\hookup', $hookup);
	}

	/**
	 * @dataProvider loadProvider
	 */
	public function test_load($topic_id, $returnvalue, $topicdata, $dates, $users, $entries, $available_sums)
	{
		$hookup = $this->get_hookup();

		// Check return value:
		$this->assertEquals($returnvalue, $hookup->load_hookup($topic_id));

		// Check loaded data:
		$this->assertEquals($topicdata, array(
			'hookup_enabled' 		=> $hookup->hookup_enabled,
			'hookup_autoreset' 		=> $hookup->hookup_autoreset,
			'hookup_active_date' 	=> $hookup->hookup_active_date,
			'hookup_self_invite' 	=> $hookup->hookup_self_invite,
		));
		$this->assertEquals($returnvalue ? $topic_id : 0, 	$hookup->topic_id);
		$this->assertEquals($dates, 	$hookup->hookup_dates);
		$this->assertEquals($users, 	$hookup->hookup_users);
		$this->assertEquals($entries, 	$hookup->hookup_availables);
	}

	public function test_set_user_date()
	{
		$hookup = $this->get_hookup();

		$this->assertFalse($hookup->set_user_date(2, 1, 1));
		$this->assertEquals(array(), $hookup->hookup_availables);

		$hookup->hookup_users = array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => ''));
		$this->assertFalse($hookup->set_user_date(2, 1, 1));
		$this->assertEquals(array(), $hookup->hookup_availables);

		$hookup->hookup_dates = array(1 => array('date_time' => 1, 'text' => null));
		$this->assertTrue($hookup->set_user_date(2, 1, 1));
		$this->assertFalse($hookup->set_user_date(2, 0, 1));
		$this->assertEquals(array(2 => array(1 => 1)), $hookup->hookup_availables);
	}

	public function test_add_groups()
	{
		$hookup = $this->get_hookup();

		$hookup->add_groups(0);
		$this->assertEmpty($hookup->hookup_users);
		$hookup->add_groups(1);
		$this->assertEquals(array(
			2 => array(
				'user_id' 		=> 2,
				'notify_status' => 0,
				'comment' 		=> ''
			),
		), $hookup->hookup_users);

		$hookup->add_groups(2);
		$this->assertEquals(array(
			2 => array(
				'user_id' 		=> 2,
				'notify_status' => 0,
				'comment' 		=> ''
			),
			4 => array(
				'user_id' 		=> 4,
				'notify_status' => 0,
				'comment' 		=> ''
			),
		), $hookup->hookup_users);

		// Reset and try adding both at the same time:
		$hookup = $this->get_hookup();
		$this->assertEmpty($hookup->hookup_users);
		$hookup->add_groups(array(1,2));
		$this->assertEquals(array(
			2 => array(
				'user_id' 		=> 2,
				'notify_status' => 0,
				'comment' 		=> ''
			),
			4 => array(
				'user_id' 		=> 4,
				'notify_status' => 0,
				'comment' 		=> ''
			),
		), $hookup->hookup_users);
	}

	public function test_add_user()
	{
		$hookup = $this->get_hookup();

		$hookup->add_user(2);
		$hookup->add_user(3, 'a');
		$hookup->add_user(4, '', 1);
		$hookup->add_user(5, 'b', 2);

		$this->assertEquals(array(
			2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => ''),
			3 => array('user_id' => 3, 'notify_status' => 0, 'comment' => 'a'),
			4 => array('user_id' => 4, 'notify_status' => 1, 'comment' => ''),
			5 => array('user_id' => 5, 'notify_status' => 2, 'comment' => 'b'),
		), $hookup->hookup_users);
	}

	public function test_add_date()
	{
		$hookup = $this->get_hookup();

		$this->assertTrue($hookup->add_date(1));
		$this->assertFalse($hookup->add_date(1));
		$this->assertTrue($hookup->add_date(0, 'a'));
		$this->assertFalse($hookup->add_date(0, 'a'));
		$this->assertFalse($hookup->add_date(0, ''));
		$this->assertFalse($hookup->add_date('0', ''));

		$this->assertEquals(array(
			array('date_time' => 1, 'text' => null),
			array('date_time' => 0, 'text' => 'a'),
		), $hookup->hookup_dates);
	}

	public function test_get_date_id()
	{
		//TODO: This does not check for newly entered dates without actual ID.
		$hookup = $this->get_hookup();

		$this->assertNull($hookup->get_date_id(1));
		$hookup->hookup_dates = array(
			2 => array('date_id' => 2, 'date_time' => 1, 'text' => null),
			3 => array('date_id' => 3, 'date_time' => 0, 'text' => 'a'),
		);
		$this->assertFalse($hookup->get_date_id(3));
		$this->assertEquals(2, $hookup->get_date_id(1));
		$this->assertFalse($hookup->get_date_id(0));
		$this->assertEquals(3, $hookup->get_date_id('a'));
	}

	public function setUserDataProvider()
	{
		return array(
			array(2, null, null, false, array(), array()),
			array(2, 0, '', false, array(), array()),
			array(2, 1, '', false, array(), array()),
			array(2, 0, 'a', false, array(), array()),
			array(2, 0, null, true,
				array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => '')),
				array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => '')),
			),
			array(2, 1, null, true,
				array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => '')),
				array(2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => '')),
			),
			array(2, null, null, true,
				array(2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => 'a')),
				array(2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => 'a')),
			),
			array(2, null, 'b', true,
				array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => '')),
				array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => 'b')),
			),
			array(2, null, 'b', true,
				array(2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => '')),
				array(2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => 'b')),
			),
			array(2, 1, null, true,
				array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => '')),
				array(2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => '')),
			),
			array(2, 1, null, true,
				array(2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => 'a')),
				array(2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => 'a')),
			),
			array(2, 1, null, true,
				array(
					2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => ''),
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
				array(
					2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => ''),
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
			),
			array(2, null, 'a', true,
				array(
					2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => ''),
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
				array(
					2 => array('user_id' => 2, 'notify_status' => 0, 'comment' => 'a'),
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
			),
		);
	}

	/**
	 * @dataProvider setUserDataProvider
	 */
	public function test_set_user_data($user_id, $notify_status, $comment, $retval, $users_before, $users_after)
	{
		$hookup = $this->get_hookup();
		$hookup->hookup_users = $users_before;
		$this->assertEquals($retval, $hookup->set_user_data($user_id, $notify_status, $comment));
		$this->assertEquals($users_after, $hookup->hookup_users);
	}

	public function removeDateProvider()
	{
		return array(
			'nothing' => array(0, 0, array(), array(), array(), array()),
			'keep1' => array(0, 0,
				array(
					1 => array('date_id' => 1, 'date_time' => 1, 'text' => '')
				),
				array(
					1 => array('date_id' => 1, 'date_time' => 1, 'text' => '')
				),
				array(), array(),
			),
			'keep2' => array(0, 0,
				array(
					1 => array('date_id' => 1, 'date_time' => 1, 'text' => ''),
					2 => array('date_id' => 2, 'date_time' => 2, 'text' => ''),
				),
				array(
					1 => array('date_id' => 1, 'date_time' => 1, 'text' => ''),
					2 => array('date_id' => 2, 'date_time' => 2, 'text' => ''),
				),
				array(), array(),
			),
			'del_date' => array(2, 0,
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
					2 => array('date_id' => 2, 'date_time' => 3, 'text' => ''),
				),
				array(
					2 => array('date_id' => 2, 'date_time' => 3, 'text' => ''),
				),
				array(
					5 => array(1 => 1),
					6 => array(2 => 1),
				),
				array(
					5 => array(),
					6 => array(2 => 1),
				),
			),
			'keep_full' => array(0, 0,
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
					2 => array('date_id' => 2, 'date_time' => 3, 'text' => ''),
				),
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
					2 => array('date_id' => 2, 'date_time' => 3, 'text' => ''),
				),
				array(
					5 => array(1 => 1),
					6 => array(2 => 1),
				),
				array(
					5 => array(1 => 1),
					6 => array(2 => 1),
				),
			),
			'del_id' => array(0, 1,
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
					2 => array('date_id' => 2, 'date_time' => 3, 'text' => ''),
				),
				array(
					2 => array('date_id' => 2, 'date_time' => 3, 'text' => ''),
				),
				array(
					5 => array(1 => 1),
					6 => array(2 => 1),
				),
				array(
					5 => array(),
					6 => array(2 => 1),
				),
			),
			'del_date2' => array(3, 0,
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
					2 => array('date_id' => 2, 'date_time' => 3, 'text' => ''),
				),
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
				),
				array(
					5 => array(1 => 1),
					6 => array(2 => 1),
				),
				array(
					5 => array(1 => 1),
					6 => array(),
				),
			),
			'del_date3' => array(3, 0,
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
					2 => array('date_time' => 3, 'text' => ''),
				),
				array(
					1 => array('date_id' => 1, 'date_time' => 2, 'text' => ''),
				),
				array(
					5 => array(1 => 1),
					6 => array(2 => 1),
				),
				array(
					5 => array(1 => 1),
					6 => array(),
				),
			),
		);
	}

	/**
	 * @dataProvider removeDateProvider
	 */
	public function test_remove_date($date, $date_id, $dates_before, $dates_after, $availables_before, $availables_after)
	{
		$hookup = $this->get_hookup();

		$hookup->hookup_dates = $dates_before;
		$hookup->hookup_availables = $availables_before;
		$hookup->remove_date($date, $date_id);
		$this->assertEquals($dates_after, $hookup->hookup_dates);
		$this->assertEquals($availables_after, $hookup->hookup_availables);
	}

	public function removeUserProvider()
	{
		return array(
			'empty' => array(2, array(), array(), array(), array()),
			'keep' => array(0,
				array(
					2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => ''),
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
				array(
					2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => ''),
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
				array(
					2 => array(1 => 1),
					3 => array(2 => 1),
				),
				array(
					2 => array(1 => 1),
					3 => array(2 => 1),
				),
			),
			'del' => array(2,
				array(
					2 => array('user_id' => 2, 'notify_status' => 1, 'comment' => ''),
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
				array(
					3 => array('user_id' => 3, 'notify_status' => 1, 'comment' => 'x'),
				),
				array(
					2 => array(1 => 1),
					3 => array(2 => 1),
				),
				array(
					3 => array(2 => 1),
				),
			),
		);
	}

	/**
	 * @dataProvider removeUserProvider
	 * @param int $user_id
	 * @param array $users_before
	 * @param array $users_after
	 * @param array $availables_before
	 * @param array $availables_after
	 */
	public function test_remove_user($user_id, $users_before, $users_after, $availables_before, $availables_after)
	{
		$hookup = $this->get_hookup();
		$hookup->hookup_users = $users_before;
		$hookup->hookup_availables = $availables_before;
		$hookup->remove_user($user_id);
		$this->assertEquals($users_after, $hookup->hookup_users);
		$this->assertEquals($availables_after, $hookup->hookup_availables);
	}

	public function submitProvider()
	{
		// TODO
		return array(
			'full' => array(3, true,
				'data' => array(
					// Topic data
					array('hookup_enabled' => 1, 'hookup_autoreset' => 0, 'hookup_active_date' => 1, 'hookup_self_invite' => 1),
					// Dates date => (date, date_time, text)
					array(
						2 => array('date_id' => 2, 'date_time' => 0, 'text' => null),
					),
					// Members (user, comment, notify)
					array(
						2 => array('user_id' => 2, 'comment' => 'commentdata', 'notify_status' => 0),
					),
					// Entries user => (date => available)
					array(
						2 => array(2 => 1),
					),
				),
				'add_date' => array(
					array('date_time' => 1),
					array('text' => 'abc'),
				),
				// $reload_data = true, $return_changes = false, $force_run = false
				'parameters' => array(true, false, false),
			),
		);
	}

	/**
	 * @dataProvider submitProvider
	 */
	public function test_submit($topic_id, $returnvalue, $data, $parameters)
	{
		$hookup = $this->get_hookup();
		// This only tests whether the function actually runs
		$hookup->submit();

		//$hookup->load_hookup($topic_id);
		//TODO
		$this->markTestIncomplete();
	}

	public function test_delete()
	{
		//TODO
		$this->markTestIncomplete();
	}

	public function test_delete_in_db()
	{
		//TODO
		$this->markTestIncomplete();
	}

	public function test_merge()
	{
		//TODO
		$this->markTestIncomplete();
	}

	private function get_hookup()
	{
		$db = $this->new_dbal();
		$this->db = $db;

		return new hookup($db, 'phpbb_hookup_members', 'phpbb_hookup_dates', 'phpbb_hookup_available');
	}
}