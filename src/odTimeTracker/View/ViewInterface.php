<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\View;

/**
 * Interface for view class.
 */
interface ViewInterface
{
	/**
	 * Render view.
	 *
	 * @return void
	 */
	public function render();
}
