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
		);
	}
}
