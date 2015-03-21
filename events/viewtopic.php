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

class viewtopic implements EventSubscriberInterface
{

	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_assign_template_vars_before'	=> 'show_hookup_viewtopic',
		);
	}

	/** @var \gn36\hookup\functions\hookup */
	protected $hookup;

	function __construct(\gn36\hookup\functions\hookup $hookup)
	{
		$this->hookup = $hookup;
	}

	public function show_hookup_viewtopic($event)
	{
		if(!$this->hookup->load_hookup($event['topic_id']))
		{
			// No hookup for this topic
			return;
		}
	}
}