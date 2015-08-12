<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

use PHPUnit_Framework_TestCase;

class ProjectEntityTest extends PHPUnit_Framework_TestCase
{
	private $_testData = array(
		array(
			'ProjectId' => 1,
			'Name' => 'Project #1',
			'Description' => 'Description of the first project.',
			'Created' => '2015-07-29T13:10:00+01:00'
		),
		array(
			'ProjectId' => 2,
			'Name' => 'Project #2',
			'Description' => 'Description of the second project.',
			'Created' => '2015-07-29T13:30:00+01:00'
		),
		array(
			'ProjectId' => 3,
			'Name' => 'Project #3',
			'Description' => 'Description of the third project.',
			'Created' => '2015-08-01T07:10:00+01:00'
		)
	);

	public function testConstructWithNulls()
	{
		$entity = new ProjectEntity();
		$this->assertNull($entity->getId());
		$this->assertNull($entity->getProjectId(), '"ProjectId" should initially be null');
		$this->assertNull($entity->getName(), '"Name" should initially be null');
		$this->assertNull($entity->getDescription(), '"Description" should initially be null');
		$this->assertNull($entity->getCreated(), '"Created" should initially be null');
	}

	public function testConstructWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ProjectEntity($data);
			$this->assertEquals($data['ProjectId'], $entity->getId());
			$this->assertEquals($data['ProjectId'], $entity->getProjectId(), '"ProjectId" was not set correctly');
			$this->assertEquals($data['Name'], $entity->getName(), '"Name" was not set correctly');
			$this->assertEquals($data['Description'], $entity->getDescription(), '"Description" was not set correctly');
			$this->assertEquals(new \DateTime($data['Created']), $entity->getCreated(), '"Created" was not set correctly');
		}
	}

	public function testExchangeArrayWithNulls()
	{
		foreach ($this->_testData as $data) {
			$entity = new ProjectEntity();
			$entity->exchangeArray($data);
			$entity->exchangeArray(array());
			$this->assertNull($entity->getId());
			$this->assertNull($entity->getProjectId(), '"ProjectId" should be null');
			$this->assertNull($entity->getName(), '"Name" should be null');
			$this->assertNull($entity->getDescription(), '"Description" should be null');
			$this->assertNull($entity->getCreated(), '"Created" should be null');
		}
	}

	public function testExchangeArrayWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ProjectEntity();
			$entity->exchangeArray($data);
			$this->assertEquals($data['ProjectId'], $entity->getProjectId(), '"ProjectId" was not set correctly');
			$this->assertEquals($data['Name'], $entity->getName(), '"Name" was not set correctly');
			$this->assertEquals($data['Description'], $entity->getDescription(), '"Description" was not set correctly');
			$this->assertEquals(new \DateTime($data['Created']), $entity->getCreated(), '"Created" was not set correctly');
		}
	}

	public function testGetArrayCopyWithNulls()
	{
		$entity = new ProjectEntity();
		$copy = $entity->getArrayCopy();
		$this->assertNull($copy['ProjectId'], '"ProjectId" should initially be null');
		$this->assertNull($copy['Name'], '"Name" should initially be null');
		$this->assertNull($copy['Description'], '"Description" should initially be null');
		$this->assertNull($copy['Created'], '"Created" should initially be null');
		$this->assertNull($copy['CreatedFormatted'], '"CreatedFormatted" should initially be null');
	}

	public function testGetArrayCopyWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ProjectEntity($data);
			$copy = $entity->getArrayCopy();
			$this->assertEquals($data['ProjectId'], $copy['ProjectId'], '"ProjectId" was not set correctly');
			$this->assertEquals($data['Name'], $copy['Name'], '"Name" was not set correctly');
			$this->assertEquals($data['Description'], $copy['Description'], '"Description" was not set correctly');
			$created = new \DateTime($data['Created']);
			$this->assertEquals($created, $copy['Created'], '"Created" was not set correctly');
			$this->assertEquals($created->format('j.n.Y G:i'), $copy['CreatedFormatted'], '"CreatedFormatted" was not set correctly');
		}
	}

	public function testGetCreatedFormatted()
	{
		$project1 = new ProjectEntity($this->_testData[0]);
		$this->assertEquals($project1->getCreatedFormatted(), '29.7.2015 13:10');

		$project2 = new ProjectEntity($this->_testData[1]);
		$this->assertEquals($project2->getCreatedFormatted(), '29.7.2015 13:30');

		$project3 = new ProjectEntity($this->_testData[2]);
		$this->assertEquals($project3->getCreatedFormatted(), '1.8.2015 7:10');
	}
}
