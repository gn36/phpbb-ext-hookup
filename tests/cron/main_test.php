<?php

/**
 *
 * @package testing
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

class gn36_hookup_main_test extends phpbb_database_test_case
{
	static protected function setup_extensions()
	{
		return array('gn36/hookup');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/hookup_entries.xml');
	}

	public function setUp()
	{
		parent::setUp();

		//$this->db = $this->new_dbal();

		$this->cache = $this->getMockBuilder('\phpbb\cache\service')->disableOriginalConstructor()->getMock();
		$this->log = $this->getMockBuilder('\phpbb\log\log')->disableOriginalConstructor()->getMock();
	}

	public function runProvider()
	{
		$now = time();
		$old_time = 1;
		do
		{
			$new_time = $old_time + 604800;
			//Daylight savings time adjustments:
			if ((date('I', $new_time) && date('I', $old_time)) || (!date('I', $new_time) && !date('I', $old_time)))
			{
				$dst_add = 0;
			}
			else if (date('I', $new_time))
			{
				//New time is in DST, but old is not
				//Since from Winter to DST there is a loss of an hour, that needs to be subtracted:
				$dst_add = -3600;
			}
			else
			{
				//New time not in DST, but old is
				//Since from DST to winter, there is a gain of an hour, that needs to be added:
				$dst_add = 3600;
			}
			$old_time = $new_time;
		} while ($new_time < $now);

		$old_time = $new_time + $dst_add;
		$new_time = $old_time + 604800;

		// Second date is one week from first, unless DST messes us up again
		if ((date('I', $new_time) && date('I', $old_time)) || (!date('I', $new_time) && !date('I', $old_time)))
		{
			$dst_add = 0;
		}
		else if (date('I', $new_time))
		{
			//New time is in DST, but old is not
			//Since from Winter to DST there is a loss of an hour, that needs to be subtracted:
			$dst_add = -3600;
		}
		else
		{
			//New time not in DST, but old is
			//Since from DST to winter, there is a gain of an hour, that needs to be added:
			$dst_add = 3600;
		}

		return array(
			// This should stay the same
			array(1, array(array('date_time' => 1))),
			// Here, the oldest should be replaced by the newest date + whatever
			array(2, array(
				array('date_time' => 2),
				array('date_time' => 3),
				array('date_time' => $old_time + 2),
			)),
			// Here, we should fill up to 3:
			array(3, array(
				array('date_time' => 1),
				array('date_time' => $old_time),
				array('date_time' => $new_time + $dst_add),
			)),
		);
	}

	public function test_construct()
	{
		$task = $this->get_task();
		$this->assertInstanceOf('\phpbb\cron\task\base', $task);
	}

	public function test_is_runnable()
	{
		$task = $this->get_task();
		$this->assertTrue($task->is_runnable());
	}

	public function test_should_run()
	{
		// 1: Has not run ever
		$task = $this->get_task();
		$this->assertTrue($task->should_run());

		// 2: Has just run
		$task = $this->get_task(time() - 1);
		$this->assertTrue(!$task->should_run());
	}

	/**
	 * @dataProvider runProvider
	 */
	public function test_run($topic_id, $expected)
	{
		$task = $this->get_task();
		$task->run();

		// Make sure, the first date is still there:
		$sql = "SELECT date_time FROM phpbb_hookup_dates WHERE topic_id = $topic_id ORDER BY date_time ASC";
		$this->assertSqlResultEquals($expected, $sql);
	}

	private function get_task($last_run = 0)
	{
		global $phpbb_root_path, $phpEx;

		$db = $this->new_dbal();
		$this->db = $db;

		$this->config = new \phpbb\config\config(array(
			'gn36_hookup_reset_last_run' => $last_run,
		));

		//TODO: Replace by mock
		$hookup = new \gn36\hookup\functions\hookup($db, 'phpbb_hookup_members', 'phpbb_hookup_dates', 'phpbb_hookup_available');

		return new \gn36\hookup\cron\weekly_reset($this->cache, $this->config, $db, $this->log, $hookup, $phpbb_root_path, $phpEx, 'phpbb_hookup_dates', 84600);
	}
}