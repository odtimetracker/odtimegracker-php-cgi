<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Controller;

/**
 * Simple interface for controllers.
 */
interface ControllerInterface
{
	/**
	 * Retrieve value of parameter if given name. If parameter is not found 
	 * returns empty string.
	 *
	 * @param string $name
	 * @param string $default (Optional.)
	 * @return string
	 */
	public function getParam($name, $default = '');

	/**
	 * Retrieve value of POST parameter if given name. If parameter 
	 * is not found returns empty string.
	 *
	 * @param string $name
	 * @param string $default (Optional.)
	 * @return string
	 */
	public function getPostParam($name, $default = '');
}
