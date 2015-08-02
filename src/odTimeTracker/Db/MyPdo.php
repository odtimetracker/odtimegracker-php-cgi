<?php
/**
 * odtimetracker-php-cgi
 * 
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Db;

/**
 * Helper class accessing {@see \PDO}.
 */
class MyPdo
{
	/**
	 * @var string $dsn
	 */
	protected $dsn;

	/**
	 * @var string $user
	 */
	protected $user;

	/**
	 * @var string $password
	 */
	protected $password;

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * Constructor.
	 *
	 * @param string $dsn
	 * @param string $user (Optional.)
	 * @param string $password (Optional.)
	 * @return void
	 */
	public function __construct($dsn, $user = '', $password = '')
	{
		$this->dsn = $dsn;
		$this->user = $user;
		$this->password = $password;
		$this->pdo = new \PDO(
			$this->dsn,
			$this->user,
			$this->password
		);

		if ($this->isMysql()) {
			$this->pdo->exec('set names utf8');
		}
		else if ($this->isSqlite()) {
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
	}

	/**
	 * Retrieve instance of PDO.
	 *
	 * @return \PDO
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Returns `TRUE` if target database is MySQL.
	 *
	 * @return boolean
	 */
	public function isMysql()
	{
		return (strpos($this->dsn, 'mysql') !== false);
	}

	/**
	 * Returns `TRUE` if target database is SQLite.
	 *
	 * @return boolean
	 */
	public function isSqlite()
	{
		return (strpos($this->dsn, 'sqlite:') !== false);
	}
}
