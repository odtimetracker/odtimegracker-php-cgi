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
 * Tests for {@see \odTimeTracker\Model\ActivityMapper}.
 */
class ActivityMapperTest extends PHPUnit_Framework_TestCase
{
	public function testMapper()
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
		$project2 = $projectMapper->insert(new ProjectEntity(array(
			'Name' => 'Project #2', 
			'Description' => 'The second test project.'
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
			'Started' => '2011-10-05 10:00:00+1:00',
			'Stopped' => '2011-10-05 13:00:00+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity1);

		$activity2 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #2',
			'Description' => 'The second test activity.',
			'Tags' => 'tag2,tag3',
			'Started' => '2011-10-05 14:10:00+1:00',
			'Stopped' => '2011-10-05 21:15:30+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity2);

		$activity3 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #3',
			'Description' => 'The third test activity.',
			'Tags' => 'tag3,tag5',
			'Started' => '2011-10-06 07:15:00+1:00',
			'Stopped' => '2011-10-06 19:45:00+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity3);

		$activity4 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 1,
			'Name' => 'Activity #4',
			'Description' => 'The fourth test activity.',
			'Tags' => null,
			'Started' => '2011-10-07 09:15:00+1:00',
			'Stopped' => '2011-10-07 09:19:00+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity4);

		$activity5 = $mapper->insert(new ActivityEntity(array(
			'ProjectId' => 2,
			'Name' => 'Activity #5',
			'Description' => null,
			'Tags' => '',
			'Started' => '2011-10-07 10:01:10+1:00',
			'Stopped' => '2011-10-07 11:32:55+1:00'
		)));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity5);

		$activity6 = $mapper->insert(array(
			'ProjectId' => 2,
			'Name' => 'Activity #6', 
			'Started' => '2011-10-15 17:10:09+1:00'
		));
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity6);

		// Test `selectAll`
		$activities1 = $mapper->selectAll();
		$this->assertEquals(6, count($activities1));

		$count1 = 0;
		$count2 = 0;

		foreach ($activities1 as $activity) {
			$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity);
			$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $activity->getProject());

			if ($activity->getProjectId() === 1) {
				$count1++;
			}
			else if ($activity->getProjectId() === 2) {
				$count2++;
			}
		}

		$this->assertEquals(4, $count1);
		$this->assertEquals(2, $count2);

		// Test "random" activity
		$testActivity1 = $activities1[1];
		$this->assertGreaterThanOrEqual(5, $testActivity1->getId());
		$this->assertGreaterThanOrEqual(5, $testActivity1->getActivityId());
		$this->assertEquals('Activity #5', $testActivity1->getName());
		$this->assertNull($testActivity1->getDescription());
		$this->assertEquals('', $testActivity1->getTags());
		$this->assertEquals(new \DateTime('2011-10-07 10:01:10.00+1:00'), $testActivity1->getStarted());
		$this->assertEquals('7.10.2011 10:01', $testActivity1->getStartedFormatted());
		$this->assertEquals(new \DateTime('2011-10-07 11:32:55.00+1:00'), $testActivity1->getStopped());
		$this->assertEquals('7.10.2011 11:32', $testActivity1->getStoppedFormatted());
		$this->assertEquals('P0Y0M0DT1H31M45S', $testActivity1->getDuration()->format('P%yY%mM%dDT%hH%iM%sS'));
		$this->assertEquals('One hour, 31 minutes', $testActivity1->getDurationFormatted());
		$this->assertTrue($testActivity1->isWithinOneDay());
		$this->assertEquals(2, $testActivity1->getProjectId());
		$this->assertEquals(2, $testActivity1->getProject()->getProjectId());
		$this->assertEquals('Project #2', $testActivity1->getProject()->getName());
		$this->assertEquals('The second test project.', $testActivity1->getProject()->getDescription());

		// TODO Test `update`
		// TODO Test `delete`

		// Test `selectRecentActivities`
		$activities2 = $mapper->selectRecentActivities(2);
		$this->assertEquals(2, count($activities2));

		foreach ($activities2 as $activity) {
			$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity);
			$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $activity->getProject());
			$this->assertEquals(2, $activity->getProject()->getProjectId());
		}

		// Test `selectRunningActivity`
		$testActivity2 = $mapper->selectRunningActivity();
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $testActivity2);
		$this->assertEquals(6, $testActivity2->getId());
		$this->assertEquals(6, $testActivity2->getActivityId());
		$this->assertEquals('Activity #6', $testActivity2->getName());
		$this->assertNull($testActivity2->getDescription());
		$this->assertNull($testActivity2->getTags());
		$this->assertEquals(new \DateTime('2011-10-15 17:10:09.00+1:00'), $testActivity2->getStarted());
		$this->assertEquals('15.10.2011 17:10', $testActivity2->getStartedFormatted());
		$this->assertEquals(2, $testActivity2->getProjectId());
		$this->assertEquals(2, $testActivity2->getProject()->getProjectId());
		$this->assertEquals('Project #2', $testActivity2->getProject()->getName());
		$this->assertEquals('The second test project.', $testActivity2->getProject()->getDescription());
		
		// Test `selectActivitiesForInterval`
		$activities3 = $mapper->selectActivitiesForInterval('2011-10-05', '2011-10-06');
		$this->assertEquals(2, count($activities3));

		foreach ($activities3 as $activity) {
			$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $activity);
			$this->assertInstanceOf('\odTimeTracker\Model\ProjectEntity', $activity->getProject());
			$this->assertEquals(1, $activity->getProject()->getProjectId());
		}

		// Test `stopRunningActivity`
		$res1 = $mapper->stopRunningActivity();
		$this->assertTrue($res1);
		$res2 = $mapper->stopRunningActivity();
		$this->assertFalse($res2);

		// TODO Test `startActivity`
		$testActivity3 = $mapper->startActivity('Activity #7', 1);
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $testActivity3);
		$this->assertEquals('Activity #7', $testActivity3->getName());
		$this->assertNull($testActivity3->getDescription());
		$this->assertNull($testActivity3->getTags());
		
		$testActivity4 = $mapper->selectRunningActivity();
		$this->assertInstanceOf('\odTimeTracker\Model\ActivityEntity', $testActivity4);
		$this->assertEquals($testActivity3->getActivityId(), $testActivity4->getActivityId());
		$this->assertEquals($testActivity3->getProjectId(), $testActivity4->getProjectId());
		$this->assertEquals($testActivity3->getName(), $testActivity4->getName());
		$this->assertEquals($testActivity3->getDescription(), $testActivity4->getDescription());
		$this->assertEquals($testActivity3->getTags(), $testActivity4->getTags());
		$this->assertEquals($testActivity3->getStarted(), $testActivity4->getStarted());
		$this->assertEquals($testActivity3->getStartedFormatted(), $testActivity4->getStartedFormatted());
		$this->assertEquals($testActivity3->getStopped(), $testActivity4->getStopped());
		$this->assertEquals($testActivity3->getStoppedFormatted(), $testActivity4->getStoppedFormatted());
	}
}
