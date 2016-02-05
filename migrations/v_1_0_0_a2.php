<?php

/**
 *
 * @package hookup
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\migrations;

class v_1_0_0_a2 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\gn36\hookup\migrations\v_1_0_0');
	}

	public function update_data()
	{
		return array(
			// Compatibility for cron status extension
			array('config.add', array('hookup_weekly_reset_gc', 86400)),
			array('config.add', array('hookup_weekly_reset_last_gc', $this->config['gn36_hookup_reset_last_run'])),
			array('config.remove', array('gn36_hookup_reset_last_run')),
		);
	}
}
