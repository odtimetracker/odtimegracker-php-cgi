<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Db;

/**
 * Tests for {@see \odTimeTracker\Db\MyPdo}.
 */
class MyPdoTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \odTimeTracker\Db\MyPdo $testObjMySql
	 */
	protected $testObjMySql;

	/**
	 * @var \odTimeTracker\Db\MyPdo $testObjSqlite
	 */
	protected $testObjSqlite;

	protected function setUp()
	{
		$this->testObjMySql = new MyPdo('mysql:host=localhost;dbname=odtimetracker;charset=utf8', 'root', 'root');
		$this->testObjSqlite = new MyPdo('sqlite::memory:');
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * @covers Blog\Db\MyPdo::getPdo
	 */
	public function testGetPdo()
	{
		$this->assertInstanceOf('odTimeTracker\Db\MyPdo', $this->testObjMySql);
		$this->assertInstanceOf('odTimeTracker\Db\MyPdo', $this->testObjSqlite);
	}

	/**
	 * @covers Blog\Db\MyPdo::isMysql
	 */
	public function testIsMysql()
	{
		$this->assertTrue($this->testObjMySql->isMysql());
		$this->assertFalse($this->testObjSqlite->isMysql());
	}

	/**
	 * @covers Blog\Db\MyPdo::isSqlite
	 */
	public function testIsSqlite()
	{
		$this->assertFalse($this->testObjMySql->isSqlite());
		$this->assertTrue($this->testObjSqlite->isSqlite());
	}
}
