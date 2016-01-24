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
class install_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('gn36/hookup');
	}

	public function test_validate_posting()
	{
		// Check whether we can still call posting.php in edit mode (even though login prevents any useful use)
		$crawler = self::request('GET', 'posting.php?mode=edit&f=2&p=1');
		$this->assertContains('Username', $crawler->filter('dt')->text());

		// The same thing with login, check for hookup buttons
		$this->login();
		$crawler = self::request('GET', 'posting.php?mode=edit&f=2&p=1');
		$this->assertContains('Meeting planner', $crawler->text());
		$this->assert_find_one_checkbox($crawler, 'hookup_enabled');
		$this->assert_find_one_checkbox($crawler, 'hookup_self_invite');
		$this->assert_find_one_checkbox($crawler, 'hookup_autoreset');
	}

	public function test_validate_viewtopic()
	{
		// Check whether we can still call viewtopic.php without errors
		$crawler = self::request('GET', 'viewtopic.php?f=2&p=1');
		$this->assertContains('Welcome', $crawler->filter('h2')->text());
	}
}
