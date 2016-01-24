<?php

/**
*
* @package testing
* @copyright (c) 2016 gn#36
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace gn36\hookup\tests\functional;

/**
* @group functional
*/
class use_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('gn36/hookup');
	}

	public function create_hookup_data()
	{
		return array(
			// Useful cases
			'no_hookup' => array(
				array(
					'hookup_enabled' 		=> 0,
					'hookup_self_invite'	=> 0,
					'hookup_autoreset' 		=> 0,
				),
				false,
				'no_hookup',
			),
			'hookup_only' => array(
				array(
					'hookup_enabled' 		=> 1,
					'hookup_self_invite' 	=> 0,
					'hookup_autoreset' 		=> 0,
				),
				true,
				'hookup_only',
			),
			'hookup_self' => array(
				array(
					'hookup_enabled' 		=> 1,
					'hookup_self_invite'	=> 1,
					'hookup_autoreset' 		=> 0,
				),
				true,
				'hookup_self',
			),
			'hookup_autoreset' => array(
				array(
					'hookup_enabled' 		=> 1,
					'hookup_self_invite'	=> 0,
					'hookup_autoreset' 		=> 1,
				),
				true,
				'hookup_autoreset',
			),
			'all' => array(
				array(
					'hookup_enabled' 		=> 1,
					'hookup_self_invite'	=> 1,
					'hookup_autoreset' 		=> 1,
				),
				true,
				'all',
			),
			// Nonsense cases:
			'autoreset_only' => array(
				array(
					'hookup_enabled' 		=> 0,
					'hookup_self_invite'	=> 0,
					'hookup_autoreset' 		=> 1,
				),
				false,
				'autoreset_only',
			),
			'self_only' => array(
				array(
					'hookup_enabled' 		=> 0,
					'hookup_self_invite'	=> 1,
					'hookup_autoreset' 		=> 0,
				),
				false,
				'self_only',
			),
			'self_autoreset' => array(
				array(
					'hookup_enabled' 		=> 0,
					'hookup_self_invite'	=> 1,
					'hookup_autoreset' 		=> 1,
				),
				false,
				'self_autoreset',
			),
		);
	}

	/**
	 * @dataProvider create_hookup_data
	 */
	public function test_create_hookup($hookup_data, $result, $title)
	{
		$topicdata = $this->setup_hookup_topic($hookup_data, $title);
		$this->assertNotNull($topicdata);
		$this->assertTrue(isset($topicdata['topic_id']) && $topicdata['topic_id']);

		/** @var $db \phpbb\db\driver\driver_interface **/
		$db = $this->get_db();
		$sql = 'SELECT hookup_enabled, hookup_self_invite, hookup_autoreset FROM ' . TOPICS_TABLE . ' WHERE topic_id = ' . $topicdata['topic_id'];
		$result = $db->sql_query($sql);
		$this->assertNotFalse($row = $db->sql_fetchrow($result));
		$this->assertEquals($hookup_data, $row);
	}

	/**
	 * @dataProvider create_hookup_data
	 * @depends test_create_hookup
	 */
	public function test_validate_viewtopic_login($hookup, $result, $title)
	{
		$this->login();

		$topic = $this->setup_hookup_topic($hookup, $title);
		$this->assertNotNull($topic);
		$this->assertTrue(isset($topic['topic_id']) && $topic['topic_id']);

		$crawler 	= self::request('GET', "viewtopic.php?f=2&t={$topic['topic_id']}");

		if ($result)
		{
			$this->assertContains('Meeting planner', implode(' ', $crawler->filter('h2')->each(function($node, $i){ return $node->text(); })));
			if ($hookup['hookup_autoreset'])
			{
				$this->assertContains('Weekly reset', implode(' ', $crawler->filter('h3')->each(function($node, $i){ return $node->text(); })));
			}
			else
			{
				$this->assertNotContains('Weekly reset', implode(' ', $crawler->filter('h3')->each(function($node, $i){ return $node->text(); })));
			}

			if ($hookup['hookup_self_invite'])
			{
				$this->assertContains('Participate', implode(' ', $crawler->filter('h3')->each(function($node, $i){ return $node->text(); })));
				$this->assertContains('invite_self', implode(' ', $crawler->filter('form')->each(function($node, $i){ return $node->html();})));
				$this->assertContains('part of this meeting planner', implode(' ', $crawler->filter('form')->each(function($node, $i){ return $node->html();})));
			}
			else
			{
				$this->assertNotContains('Participate', implode(' ', $crawler->filter('h3')->each(function($node, $i){ return $node->text(); })));
				// This actually can still be in there because the admin has permission to invite users
				//$this->assertNotContains('invite_self', implode(' ', $crawler->filter('form')->each(function($node, $i){ return $node->html();})));
				$this->assertNotContains('part of this meeting planner', implode(' ', $crawler->filter('form')->each(function($node, $i){ return $node->html();})));
			}
		}
		else
		{
			$this->assertNotContains('Meeting planner', implode(' ', $crawler->filter('h2')->each(function($node, $i){ return $node->text(); })));
			$this->assertNotContains('Weekly reset', implode(' ', $crawler->filter('h3')->each(function($node, $i){ return $node->text(); })));
			$this->assertNotContains('Participate', implode(' ', $crawler->filter('h3')->each(function($node, $i){ return $node->text(); })));
			$this->assertNotContains('invite_self', implode(' ', $crawler->filter('input')->each(function($node, $i){ return $node->html();})));
			$this->assertNotContains('part of this meeting planner', implode(' ', $crawler->filter('input')->each(function($node, $i){ return $node->html();})));
		}

	}

	/**
	 * Create a topic that contains a hookup
	 *
	 * @param array $hookup_data
	 * @param string $subject
	 * @return null|array topic_id, post_id
	 */
	protected function setup_hookup_topic($hookup_data, $subject = 'Test subject')
	{
		$do_logout = false;
		if (!$this->sid)
		{
			$do_logout = true;
			$this->login();
		}

		// We use checkboxes, so there is no data if the boxes are unchecked
		foreach ($hookup_data as $key => $value)
		{
			if (!$value)
			{
				unset($hookup_data[$key]);
			}
		}

		$result = $this->create_topic(2, $subject, 'Test message', $hookup_data);

		if ($do_logout)
		{
			$this->logout();
		}

		return $result;
	}
}
