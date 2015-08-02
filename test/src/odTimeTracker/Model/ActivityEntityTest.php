<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

use PHPUnit_Framework_TestCase;

class ActivityEntityTest extends PHPUnit_Framework_TestCase
{
	private $_testData = array(
		array(
			'ActivityId' => 1,
			'ProjectId' => 1,
			'Name' => 'Activity #1',
			'Description' => 'Description of the first activity.',
			'Tags' => 'tag1,tag2',
			'Started' => '2015-07-29T13:10:00+01:00',
			'Stopped' => '2015-07-29T13:15:00+01:00'
		),
		array(
			'ActivityId' => 2,
			'ProjectId' => 2,
			'Name' => 'Activity #2',
			'Description' => 'Description of the second activity.',
			'Tags' => 'tag2,tag3',
			'Started' => '2015-07-29T13:30:00+01:00',
			'Stopped' => '2015-07-29T14:00:30+01:00'
		),
		array(
			'ActivityId' => 3,
			'ProjectId' => 1,
			'Name' => 'Activity #3',
			'Description' => null,
			'Tags' => null,
			'Started' => '2015-07-29T14:10:00+01:00',
			'Stopped' => null
		)
	);

	public function testConstructWithNulls()
	{
		$entity = new ActivityEntity();
		$this->assertNull($entity->getActivityId(), '"ActivityId" should initially be null');
		$this->assertNull($entity->getProjectId(), '"ProjectId" should initially be null');
		$this->assertNull($entity->getName(), '"Name" should initially be null');
		$this->assertNull($entity->getDescription(), '"Description" should initially be null');
		$this->assertNull($entity->getTags(), '"Tags" should initially be null');
		$this->assertNull($entity->getStarted(), '"Started" should initially be null');
		$this->assertNull($entity->getStopped(), '"Stopped" should initially be null');
	}

	public function testConstructWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ActivityEntity($data);
			$this->assertEquals($data['ActivityId'], $entity->getActivityId(), '"ActivityId" was not set correctly');
			$this->assertEquals($data['ProjectId'], $entity->getProjectId(), '"ProjectId" was not set correctly');
			$this->assertEquals($data['Name'], $entity->getName(), '"Name" was not set correctly');
			$this->assertEquals($data['Description'], $entity->getDescription(), '"Description" was not set correctly');
			$this->assertEquals($data['Tags'], $entity->getTags(), '"Tags" was not set correctly');
			$this->assertEquals(new \DateTime($data['Started']), $entity->getStarted(), '"Started" was not set correctly');

			if (is_null($data['Stopped'])) {
				$this->assertNull($entity->getStopped());
			} else {
				$this->assertEquals(new \DateTime($data['Stopped']), $entity->getStopped(), '"Stopped" was not set correctly');
			}
		}
	}

	public function testExchangeArrayWithNulls()
	{
		foreach ($this->_testData as $data) {
			$entity = new ActivityEntity();
			$entity->exchangeArray($data);
			$entity->exchangeArray(array());
			$this->assertNull($entity->getActivityId(), '"ActivityId" should be null');
			$this->assertNull($entity->getProjectId(), '"ProjectId" should be null');
			$this->assertNull($entity->getName(), '"Name" should be null');
			$this->assertNull($entity->getDescription(), '"Description" should be null');
			$this->assertNull($entity->getTags(), '"Tags" should be null');
			$this->assertNull($entity->getStarted(), '"Started" should be null');
			$this->assertNull($entity->getStopped(), '"Stopped" should be null');
		}
	}

	public function testExchangeArrayWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ActivityEntity();
			$entity->exchangeArray($data);
			$this->assertEquals($data['ActivityId'], $entity->getActivityId(), '"ActivityId" was not set correctly');
			$this->assertEquals($data['ProjectId'], $entity->getProjectId(), '"ProjectId" was not set correctly');
			$this->assertEquals($data['Name'], $entity->getName(), '"Name" was not set correctly');
			$this->assertEquals($data['Description'], $entity->getDescription(), '"Description" was not set correctly');
			$this->assertEquals($data['Tags'], $entity->getTags(), '"Tags" was not set correctly');
			$this->assertEquals(new \DateTime($data['Started']), $entity->getStarted(), '"Started" was not set correctly');

			if (is_null($data['Stopped'])) {
				$this->assertNull($entity->getStopped());
			} else {
				$this->assertEquals(new \DateTime($data['Stopped']), $entity->getStopped(), '"Stopped" was not set correctly');
			}
		}
	}

	public function testGetArrayCopyWithNulls()
	{
		$entity = new ActivityEntity();
		$copy = $entity->getArrayCopy();
		$this->assertNull($copy['ActivityId'], '"ActivityId" should initially be null');
		$this->assertNull($copy['ProjectId'], '"ProjectId" should initially be null');
		$this->assertNull($copy['Name'], '"Name" should initially be null');
		$this->assertNull($copy['Description'], '"Description" should initially be null');
		$this->assertNull($copy['Tags'], '"Tags" should initially be null');
		$this->assertNull($copy['Started'], '"Started" should initially be null');
		$this->assertNull($copy['Stopped'], '"Stopped" should initially be null');
	}

	public function testGetArrayCopyWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ActivityEntity($data);
			$copy = $entity->getArrayCopy();
			$this->assertEquals($data['ActivityId'], $copy['ActivityId'], '"ActivityId" was not set correctly');
			$this->assertEquals($data['ProjectId'], $copy['ProjectId'], '"ProjectId" was not set correctly');
			$this->assertEquals($data['Name'], $copy['Name'], '"Name" was not set correctly');
			$this->assertEquals($data['Description'], $copy['Description'], '"Description" was not set correctly');
			$this->assertEquals($data['Tags'], $copy['Tags'], '"Tags" was not set correctly');
			$this->assertEquals(new \DateTime($data['Started']), $copy['Started'], '"Started" was not set correctly');

			if (is_null($data['Stopped'])) {
				$this->assertNull($entity->getStopped());
			} else {
				$this->assertEquals(new \DateTime($data['Stopped']), $copy['Stopped'], '"Stopped" was not set correctly');
			}
		}
	}

	public function testGetDuration()
	{
		// Test the first activity
		$activity1 = new ActivityEntity($this->_testData[0]);
		$duration1 = $activity1->getDuration();
		$this->assertEquals(5, $duration1->i);
		// Test the second activity
		$activity2 = new ActivityEntity($this->_testData[1]);
		$duration2 = $activity2->getDuration();
		$this->assertEquals(30, $duration2->i);
		$this->assertEquals(30, $duration2->s);
	}
}
