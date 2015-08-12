<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

use PHPUnit_Framework_TestCase;

/**
 * Tests for {@see \odTimeTracker\Model\ProjectMapper}.
 */
class ProjectMapperTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test mapper on SQLite database.
	 */
	public function testSqlite()
	{
		try {
			$pdo = new \odTimeTracker\Db\MyPdo('sqlite::memory:');

			if (!($pdo instanceof \odTimeTracker\Db\MyPdo)) {
				throw new \Exception('MyPdo was not initialized!');
			}
		} catch (\Exception $e) {
			$this->markTestSkipped('Database connection was not established!');
		}

		$mapper = new ProjectMapper($pdo);

		// Test `createSchema`
		$res = $mapper->createSchema();
		$this->assertTrue($res);

		if ($res === false) {
			$this->markTestSkipped('Database schema was not created!');
		}

		// Test `insert`
		$project1 = $mapper->insert(new ProjectEntity(array(
			'Name' => 'Project #1', 
			'Description' => 'The first test project.'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project1);

		$project2 = $mapper->insert(new ProjectEntity(array(
			'Name' => 'Project #2', 
			'Description' => 'The second test project.'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project2);

		$project3 = $mapper->insert(new ProjectEntity(array(
			'Name' => 'Project #3', 
			'Description' => 'The third test project.', 
			'Created' => null
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project3);

		$project4 = $mapper->insert(new ProjectEntity(array(
			'Name' => 'Project #4', 
			'Description' => 'The fourth test project.', 
			'Created' => '2011-10-05 10:00:00.0000+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project4);

		$project5 = $mapper->insert(new ProjectEntity(array(
			'Name' => 'Project #5', 
			'Description' => 'The fifth test project.', 
			'Created' => new \DateTime('2011-10-10 10:00:00.0000+1:00')
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project5);

		$project6 = $mapper->insert(array(
			'Name' => 'Project #6', 
			'Description' => 'The sixth test project.'
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project6);

		$project7 = $mapper->insert(array(
			'Name' => 'Project #7', 
			'Description' => 'The seventh test project.',
			'Created' => null
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project7);

		$project8 = $mapper->insert(array(
			'Name' => 'Project #8', 
			'Description' => 'The eigth test project.',
			'Created' => '2011-10-09 10:00:00.0000+1:00'
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project8);

		$project9 = $mapper->insert(array(
			'Name' => 'Project #9', 
			'Description' => 'The ninth test project.',
			'Created' => new \DateTime('2011-10-04 10:00:00.0000+1:00')
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project9);

		// Test `selectAll`
		$results = $mapper->selectAll();
		$this->assertEquals(9, count($results));

		// Test "random" project
		$testProject = $results[4];
		$this->assertGreaterThanOrEqual(5, $testProject->getId());
		$this->assertGreaterThanOrEqual(5, $testProject->getProjectId());
		$this->assertEquals('Project #5', $testProject->getName());
		$this->assertEquals('The fifth test project.', $testProject->getDescription());
		$this->assertEquals(new \DateTime('2011-10-10 10:00:00.0000+1:00'), $testProject->getCreated());
		$this->assertEquals('10.10.2011 10:00', $testProject->getCreatedFormatted());

		// TODO Test `update`
		// TODO Test `delete`
		$this->markTestIncomplete();
	}

	/**
	 * Test mapper on MySQL database.
	 */
	public function testMysql()
	{
		// TODO Test `createSchema`
		// TODO Test `insert`
		// TODO Test `update`
		// TODO Test `delete`
		$this->markTestIncomplete();
	}
}
