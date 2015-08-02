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
			'Created' => '2015-07-29T14:10:00+01:00'
		)
	);

	public function testConstructWithNulls()
	{
		$entity = new ProjectEntity();
		$this->assertNull($entity->getProjectId(), '"ProjectId" should initially be null');
		$this->assertNull($entity->getName(), '"Name" should initially be null');
		$this->assertNull($entity->getDescription(), '"Description" should initially be null');
		$this->assertNull($entity->getCreated(), '"Created" should initially be null');
	}

	public function testConstructWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ProjectEntity($data);
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
	}

	public function testGetArrayCopyWithValues()
	{
		foreach ($this->_testData as $data) {
			$entity = new ProjectEntity($data);
			$copy = $entity->getArrayCopy();
			$this->assertEquals($data['ProjectId'], $copy['ProjectId'], '"ProjectId" was not set correctly');
			$this->assertEquals($data['Name'], $copy['Name'], '"Name" was not set correctly');
			$this->assertEquals($data['Description'], $copy['Description'], '"Description" was not set correctly');
			$this->assertEquals(new \DateTime($data['Created']), $copy['Created'], '"Created" was not set correctly');
		}
	}
}
