<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Controller;

/**
 * Common abstract class for controllers.
 */
abstract class CommonController implements ControllerInterface
{
	/**
	 * @var \odTimeTracker\Db\MyPdo $db
	 */
	protected $db;

	/**
	 * @var array $config
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config)
	{
		$this->db = new \odTimeTracker\Db\MyPdo(
			$config['db']['dsn'],
			$config['db']['user'],
			$config['db']['password']
		);

		$this->config = $config['application'];
	}
}
