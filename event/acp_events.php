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
class acp_events implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'			=> 'add_permissions',
		);
	}

	/**
	* Add permissions for setting topic based posts per page settings.
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function add_permissions($event)
	{

		$event['permissions'] = array_merge($event['permissions'], array(
			// Forum perms
			'f_hookup'	=> array('lang' => 'ACL_F_HOOKUP', 'cat' => 'content'),
		));

	}
}
