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
 * Tests for {@see \odTimeTracker\Model\ActivityMapper} using SQLite as a database.
 */
class ActivityMapperTest extends PHPUnit_Framework_TestCase
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

		$mapper = new ActivityMapper($pdo);
		$projectMapper = new ProjectMapper($pdo);
		$projectRes = $projectMapper->createSchema();
		$this->assertTrue($projectRes);
		$project1 = $projectMapper->insert(new ProjectEntity(array(
			'Name' => 'Project #1', 
			'Description' => 'The first test project.'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $project1);

		// Test `createSchema`
		$res = $mapper->createSchema();
		$this->assertTrue($res);

		if (!$mapper->createSchema()) {
			$this->markTestSkipped('Database schema was not created!');
		}

		// Test `insert`
		$activity1 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #1',
			'Description' => 'The first test activity.',
			'Tags' => 'tag1,tag2,tag3',
			'Started' => '2011-10-05 10:00:00.0000+1:00',
			'Stopped' => '2011-10-05 13:00:00.0000+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity1);

		$activity2 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #2',
			'Description' => 'The second test activity.',
			'Tags' => 'tag2,tag3',
			'Started' => '2011-10-05 14:10:00.0000+1:00',
			'Stopped' => '2011-10-05 21:15:30.0000+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity2);

		$activity3 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #3',
			'Description' => 'The third test activity.',
			'Tags' => 'tag3,tag5',
			'Started' => '2011-10-06 07:15:00.0000+1:00',
			'Stopped' => '2011-10-06 19:45:00.0000+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity3);

		$activity4 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #4',
			'Description' => 'The fourth test activity.',
			'Tags' => null,
			'Started' => '2011-10-07 09:15:00.0000+1:00',
			'Stopped' => '2011-10-07 09:19:00.0000+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity4);

		$activity5 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #5',
			'Description' => null,
			'Tags' => '',
			'Started' => '2011-10-07 10:01:10.0000+1:00',
			'Stopped' => '2011-10-07 11:32:55.0000+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity5);

		$activity6 = $mapper->insert(array(
			'ProjectId' => 1,
			'Name' => 'Activity #6', 
			'Description' => 'The sixth test activity.',
			'Tags' => 'tag3,tag4',
			'Started' => '2011-10-07 12:00:00.0000+1:00',
			'Stopped' => '2011-10-08 00:30:00.0000+1:00'
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity6);

		$activity7 = $mapper->insert(array(
			'ProjectId' => 1,
			'Name' => 'Activity #7', 
			'Description' => 'The seventh test activity.',
			'Tags' => 'tag1',
			'Started' => '2011-10-08 12:00:00.0000+1:00',
			'Stopped' => '2011-10-08 13:05:00.0000+1:00'
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity7);

		$activity8 = $mapper->insert(array(
			'ProjectId' => 1,
			'Name' => 'Activity #8', 
			'Started' => '2011-10-09 18:00:00.0000+1:00',
			'Stopped' => '2011-10-09 19:45:00.0000+1:00'
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity8);

		// Test `selectAll`
		//$results = $mapper->selectAll();
		//$this->assertEquals(8, count($results));

		// Test "random" activity
		/*$testActivity = $results[4];
		$this->assertGreaterThanOrEqual(5, $testActivity->getId());
		$this->assertGreaterThanOrEqual(5, $testActivity->getActivityId());
		$this->assertEquals('Activity #5', $testActivity->getName());
		$this->assertEquals('The fifth test activity.', $testActivity->getDescription());
		$this->assertEquals(new \DateTime('2011-10-10 10:00:00.0000+1:00'), $testActivity->getStarted());
		$this->assertEquals('10.10.2011 10:00', $testActivity->getStartedFormatted());*/

		// TODO Test `update`
		// TODO Test `delete`
		$this->markTestIncomplete();
	}

	/**
	 * Test mapper on MySQL database.
	 */
	public function testMysql()
	{
		$this->markTestIncomplete();
	}
}
