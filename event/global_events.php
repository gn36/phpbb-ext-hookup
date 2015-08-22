<?php
/**
 *
 * @package hookup
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class global_events implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'		=> 'load_global_lang',
			'core.user_add_after'	=> 'notification_add',
		);
	}

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	public function __construct(\phpbb\user $user, \phpbb\notification\manager $notification_manager)
	{
		$this->user = $user;
		$this->notification_manager = $notification_manager;
	}

	public function load_global_lang($event)
	{
		$this->user->add_lang_ext('gn36/hookup', 'global');
	}

	public function notification_add($event)
	{
		$notifications_data = array(
			array(
				'item_type'	=> 'gn36.hookup.notification.type.active_date_set',
				'method'	=> 'notification.method.email',
			),
			array(
				'item_type'	=> 'gn36.hookup.notification.type.active_date_reset',
				'method'	=> 'notification.method.email',
			),
			array(
				'item_type'	=> 'gn36.hookup.notification.type.date_added',
				'method'	=> 'notification.method.email',
			),
			array(
				'item_type'	=> 'gn36.hookup.notification.type.invited',
				'method'	=> 'notification.method.email',
			),
		);

		foreach ($notifications_data as $subscription)
		{
			$this->notification_manager->add_subscription($subscription['item_type'], 0, $subscription['method'], $user_id);
		}
	}
}
