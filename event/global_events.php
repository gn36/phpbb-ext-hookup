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
			'core.user_setup'	=> 'load_global_lang',
		);
	}

	/** @var \phpbb\user */
	protected $user;

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	public function load_global_lang($event)
	{
		$this->user->add_lang_ext('gn36/hookup', 'global');
	}
}
