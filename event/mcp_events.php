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
class mcp_events implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.delete_topics_after_query' => 'delete_topic',
			'core.move_posts_after' => 'move_posts',
		);
	}

	/** @var \gn36\hookup\functions\hookup */
	protected $hookup;

	/**
	 * Constructor
	 */
	public function __construct(\gn36\hookup\functions\hookup $hookup)
	{
		$this->hookup = $hookup;
	}

	/**
	* Delete the hookup which belongs to the topic by deleting all parts.
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function delete_topic($event)
	{
		$this->hookup->delete_in_db($event['topic_ids'], false);
	}

	/**
	 * Move the hookup to a new topic if necessary or merge it if one already exists
	 *
	 * @param unknown $event
	 */
	public function move_posts($event)
	{
		// parameters: src_topic_ids, dest_topic_ids
		$this->hookup->merge_in_db($event['topic_ids'], $event['topic_id']);
	}
}
