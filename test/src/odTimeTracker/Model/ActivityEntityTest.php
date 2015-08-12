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
			'ProjectId' => 2,
			'Name' => 'Activity #3',
			'Description' => 'Description of the third activity.',
			'Tags' => 'tag',
			'Started' => '2015-07-29T14:05:00+01:00',
			'Stopped' => '2015-07-29T18:35:00+01:00'
		),
		array(
			'ActivityId' => 3,
			'ProjectId' => 1,
			'Name' => 'Activity #4',
			'Description' => null,
			'Tags' => null,
			'Started' => '2015-07-29T20:10:00+01:00',
			'Stopped' => null
		)
	);

	public function testConstructWithNulls()
	{
		$entity = new ActivityEntity();
		$this->assertNull($entity->getId());
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
			$this->assertEquals($data['ActivityId'], $entity->getId());
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
			$this->assertNull($entity->getId());
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
			$this->assertEquals($data['ActivityId'], $entity->getId());
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

	public function testGetArrayCopyExtraValues()
	{
		$activity = new ActivityEntity($this->_testData[0]);
		$data = $activity->getArrayCopy();

		$this->assertEquals($data['StartedFormatted'], '29.7.2015 13:10', '"StartedFormatted" was not set correctly');
		$this->assertEquals($data['StoppedFormatted'], '29.7.2015 13:15', '"StoppedFormatted" was not set correctly');
		$this->assertEquals($data['Duration']->i, 5, '"Duration" was not set correctly');
		$this->assertEquals($data['DurationFormatted'], '5 minutes', '"DurationFormatted" was not set correctly');
		$this->assertEquals($data['IsWithinOneDay'], true, '"IsWithinOneDay" was not set correctly');
	}

	public function testGetDuration()
	{
		$activity1 = new ActivityEntity($this->_testData[0]);
		$duration1 = $activity1->getDuration();
		$this->assertEquals(5, $duration1->i);

		$activity2 = new ActivityEntity($this->_testData[1]);
		$duration2 = $activity2->getDuration();
		$this->assertEquals(30, $duration2->i);
		$this->assertEquals(30, $duration2->s);

		$activity3 = new ActivityEntity($this->_testData[2]);
		$duration3 = $activity3->getDuration();
		$this->assertEquals(4, $duration3->h);
		$this->assertEquals(30, $duration3->i);
		$this->assertEquals(0, $duration3->s);
	}

	public function testGetDurationFormatted()
	{
		$activity1 = new ActivityEntity($this->_testData[0]);
		$this->assertEquals($activity1->getDurationFormatted(), '5 minutes');

		$activity2 = new ActivityEntity($this->_testData[1]);
		$this->assertEquals($activity2->getDurationFormatted(), '30 minutes');

		$activity3 = new ActivityEntity($this->_testData[2]);
		$this->assertEquals($activity3->getDurationFormatted(), '4 hours, 30 minutes');
	}

	public function testGetTagsAsArray()
	{
		$activity1 = new ActivityEntity($this->_testData[0]);
		$this->assertEquals($activity1->getTagsAsArray(), ['tag1', 'tag2']);

		$activity2 = new ActivityEntity($this->_testData[1]);
		$this->assertEquals($activity2->getTagsAsArray(), ['tag2', 'tag3']);

		$activity3 = new ActivityEntity($this->_testData[2]);
		$this->assertEquals($activity3->getTagsAsArray(), ['tag']);

		$activity4 = new ActivityEntity($this->_testData[3]);
		$this->assertEquals($activity4->getTagsAsArray(), []);
	}

	public function testIsWithinOneDay()
	{
		$activity1 = new ActivityEntity($this->_testData[0]);
		$this->assertEquals($activity1->isWithinOneDay(), true);

		$activity2 = new ActivityEntity($this->_testData[1]);
		$this->assertEquals($activity2->isWithinOneDay(), true);

		$activity3 = new ActivityEntity($this->_testData[2]);
		$this->assertEquals($activity3->isWithinOneDay(), true);

		$activity4 = new ActivityEntity($this->_testData[3]);
		$this->assertEquals($activity4->isWithinOneDay(), false);
	}

	public function testGetStartedFormatted()
	{
		$activity1 = new ActivityEntity($this->_testData[0]);
		$this->assertEquals($activity1->getStartedFormatted(), '29.7.2015 13:10');

		$activity2 = new ActivityEntity($this->_testData[1]);
		$this->assertEquals($activity2->getStartedFormatted(), '29.7.2015 13:30');

		$activity3 = new ActivityEntity($this->_testData[2]);
		$this->assertEquals($activity3->getStartedFormatted(), '29.7.2015 14:05');

		$activity4 = new ActivityEntity($this->_testData[3]);
		$this->assertEquals($activity4->getStartedFormatted(), '29.7.2015 20:10');
	}

	public function testGetStoppedFormatted()
	{
		$activity1 = new ActivityEntity($this->_testData[0]);
		$this->assertEquals($activity1->getStoppedFormatted(), '29.7.2015 13:15');

		$activity2 = new ActivityEntity($this->_testData[1]);
		$this->assertEquals($activity2->getStoppedFormatted(), '29.7.2015 14:00');

		$activity3 = new ActivityEntity($this->_testData[2]);
		$this->assertEquals($activity3->getStoppedFormatted(), '29.7.2015 18:35');
	}
}