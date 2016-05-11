<?php

/**
 *
 * @package rgn
 * @copyright (c) 2015 Martin Beckmann
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\cron;

//TODO
class hookup_weekly_reset extends \phpbb\cron\task\base
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\log\log_interface */
	protected $log;

	/** @var \gn36\hookup\functions\hookup */
	protected $hookup;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string */
	protected $dates_table;

	/** @var int */
	protected $run_interval;

	public function __construct(\phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\log\log_interface $log, \gn36\hookup\functions\hookup $hookup, \phpbb\event\dispatcher_interface $phpbb_dispatcher, \phpbb\notification\manager $notification_manager, $root_path, $php_ext, $hookup_dates_table)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->hookup = $hookup;
		$this->notification_manager = $notification_manager;
		$this->dispatcher = $phpbb_dispatcher;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->dates_table = $hookup_dates_table;
		$this->run_interval = $config['hookup_weekly_reset_gc'];
	}

	/**
	 * Run this cronjob (and delete prunable tasks)
	 * @see \phpbb\cron\task\task::run()
	 */
	public function run()
	{
		$now = time();

		$sql = 'SELECT t.topic_id, t.forum_id, t.topic_title, d.date_time, d.text FROM ' . TOPICS_TABLE . ' t, ' . $this->dates_table . ' d  WHERE t.topic_id = d.topic_id AND t.hookup_autoreset = 1 AND t.hookup_enabled = 1';
		$result = $this->db->sql_query($sql);

		$date_list = array();
		$text_list = array();
		$topic_data = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['text'] == null)
			{
				$date_list[$row['topic_id']][] = $row['date_time'];
			}
			else
			{
				$text_list[$row['topic_id']][] = $row['text'];
			}
			$topic_data[$row['topic_id']] = array(
				'topic_title' 	=> $row['topic_title'],
				'topic_id'		=> $row['topic_id'],
				'forum_id'		=> $row['forum_id'],
			);
		}

		$hookup = $this->hookup;
		foreach ($date_list as $topic_id => $date_array)
		{
			sort($date_array);

			$hookup->load_hookup($topic_id);
			$dates_added = 0;

			if (count($date_array) < 3)
			{
				for ($i = count($date_array); $i < 3; $i++)
				{
					sort($date_array);
					$old_time = $date_array[count($date_array) - 1];
					$new_time = $old_time;

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

					$date_array[] = $new_time + $dst_add;
					$hookup->hookup_dates[] = array(
						'date_time'	=> $new_time + $dst_add,
						'text'		=> null,
						);
					$dates_added++;
				}
			}

			if ($date_array[count($date_array) - 2] < $now)
			{
				//If the second last entry has already passed

				$hookup->remove_date($date_array[0]);
				$old_time = $date_array[count($date_array) - 1];
				$new_time = $old_time;

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

				$date_array[] = $new_time + $dst_add;
				$hookup->hookup_dates[] = array('date_time' => $new_time + $dst_add, 'text' => null);
				$dates_added++;
			}

			$hookup->submit();
			if ($dates_added)
			{
				$this->notification_manager->add_notifications('gn36.hookup.notification.type.date_added_rotation', $topic_data[$topic_id]);
			}
		}

		$this->config->set('hookup_weekly_reset_last_gc', $now, true);
	}

	/**
	 * Returns whether this cron job can run
	 * @see \phpbb\cron\task\base::is_runnable()
	 * @return bool
	 */
	public function is_runnable()
	{
		return isset($this->config['hookup_weekly_reset_last_gc']);
	}

	/**
	 * Should this cron job run now because enough time has passed since last run?
	 * @see \phpbb\cron\task\base::should_run()
	 * @return bool
	 */
	public function should_run()
	{
		$now = time();

		// Run at most every day
		return $now > $this->config['hookup_weekly_reset_last_gc'] + $this->run_interval;
	}

}
