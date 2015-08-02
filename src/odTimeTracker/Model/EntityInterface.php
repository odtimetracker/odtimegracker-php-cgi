<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

/**
 * Simple interface for our model entities.
 */
interface EntityInterface
{
	/**
	 * Set entity with given data.
	 *
	 * @param array $data
	 * @return void
	 */
	public function exchangeArray(array $data);

	/**
	 * Retrieve entity as array.
	 *
	 * @return array
	 */
	public function getArrayCopy();
}
