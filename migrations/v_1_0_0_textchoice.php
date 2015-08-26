<?php

/**
 *
 * @package hookup
 * @copyright (c) 2015 kilianr
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\migrations;

class v_1_0_0_textchoice extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\gn36\hookup\migrations\v_1_0_0_compat30x');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table('hookup_dates') => array(
					'text'			=> array('VCHAR:255', null),
				),
			),
		);
	}
}
