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

	/**
	 * Retrieve value of parameter if given name. If parameter is not found 
	 * returns empty string.
	 *
	 * @param string $name
	 * @param string $default (Optional.)
	 * @return string
	 */
	public function getParam($name, $default = '')
	{
		$ret = filter_input(INPUT_GET, $name);
		return ($ret === false || is_null($ret)) ? $default : $ret;
	}

	/**
	 * Retrieve value of POST parameter if given name. If parameter 
	 * is not found returns empty string.
	 *
	 * @param string $name
	 * @param string $default (Optional.)
	 * @return string
	 */
	public function getPostParam($name, $default = '')
	{
		$ret = filter_input(INPUT_POST, $name);
		return ($ret === false || is_null($ret)) ? $default : $ret;
	}
}
