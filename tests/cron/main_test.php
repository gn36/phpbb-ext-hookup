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

		$this->db = $this->new_dbal();

		$this->cache = $this->getMockBuilder('\phpbb\cache\service')->disableOriginalConstructor()->getMock();
		$this->log = $this->getMockBuilder('\phpbb\log\log')->disableOriginalConstructor()->getMock();
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

	public function test_run()
	{
		$task = $this->get_task();
		$task->run();
		//TODO
	}

	private function get_task($last_run = 0)
	{
		global $phpbb_root_path, $phpEx;
		//$pastebin_path = dirname(__FILE__) . '/../../';
		//$db = $this->new_dbal();
		$this->db = $db;

		$this->config = new \phpbb\config\config(array(
			'gn36_hookup_reset_last_run' => $last_run,
		));

		//TODO: Replace by mock
		$hookup = new \gn36\hookup\functions\hookup($db, 'phpbb_hookup_members', 'phpbb_hookup_dates', 'phpbb_hookup_available');

		return new \gn36\hookup\cron\weekly_reset($this->cache, $this->config, $db, $this->log, $hookup, $phpbb_root_path, $phpEx, 'phpbb_hookup_dates', 84600);
	}
}