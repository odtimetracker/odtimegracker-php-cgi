<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

/**
 * Common mapper.
 */
class CommonMapper
{
	/**
	 * @var \Blog\Db\MyPdo $db
	 */
	protected $db;

	/**
	 * Constructor.
	 *
	 * @param \Blog\Db\MyPdo $db
	 * @return void
	 */
	public function __construct(\odTimeTracker\Db\MyPdo $db)
	{
		$this->db = $db;
	}
}
