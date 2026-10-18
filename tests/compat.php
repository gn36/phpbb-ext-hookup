<?php
/**
 *
 * @package testing
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace gn36\hookup\tests;

/**
 * Assertions that behave the same on all PHPUnit versions used by phpBB 3.2.x (4.x) and 3.3.x (9.x).
 * PHPUnit 9 no longer accepts strings for assertContains(), while assertStringContainsString() does not exist in PHPUnit 4.
 */
trait compat
{
	public function assertTextContains($needle, $haystack, $message = '')
	{
		$this->assertThat($haystack, $this->stringContains($needle), $message);
	}

	public function assertTextNotContains($needle, $haystack, $message = '')
	{
		$this->assertThat($haystack, $this->logicalNot($this->stringContains($needle)), $message);
	}
}
