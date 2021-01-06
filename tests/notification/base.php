<?php
/**
 *
 * @package testing
 * @copyright (c) 2016 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace gn36\hookup\tests\notification;

abstract class gn36_hookup_tests_notification_base extends \phpbb_tests_notification_base
{
	protected function get_notification_types()
	{
		return array(
			'gn36.hookup.notification.type.active_date_reset',
			'gn36.hookup.notification.type.active_date_set',
			'gn36.hookup.notification.type.base',
			'gn36.hookup.notification.type.date_added',
			'gn36.hookup.notification.type.invited',
			'gn36.hookup.notification.type.user_added',
		);
	}

	static protected function setup_extensions()
	{
		return array('gn36/hookup');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/hookup_entries.xml');
	}
}
