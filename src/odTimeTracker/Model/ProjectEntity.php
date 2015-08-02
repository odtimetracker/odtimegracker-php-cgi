<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

/**
 * Project entity.
 */
class ProjectEntity implements \odTimeTracker\Model\EntityInterface
{
	/**
	 * Project's identifier.
	 *
	 * @var integer $projectId
	 */
	protected $projectId;

	/**
	 * Project's name.
	 *
	 * @var string $name
	 */
	protected $name;

	/**
	 * Project's description.
	 *
	 * @var string $description
	 */
	protected $description;

	/**
	 * Date time of project's creation (RFC3339).
	 *
	 * @var \DateTime $created
	 */
	protected $created;

	/**
	 * Constructor.
	 *
	 * @param array $data (Optional). Data to initialize entity with.
	 * @return void
	 */
	public function __construct($data = array())
	{
		$this->exchangeArray($data);
	}

	/**
	 * Set entity with given data.
	 *
	 * @param array $data
	 * @return void
	 */
	public function exchangeArray(array $data)
	{
		$this->setProjectId(isset($data['ProjectId']) ? $data['ProjectId'] : null);
		$this->setName(isset($data['Name']) ? $data['Name'] : null);
		$this->setDescription(isset($data['Description']) ? $data['Description'] : null);
		$this->setCreated(isset($data['Created']) ? $data['Created'] : null);
	}

	/**
	 * Retrieve entity as array.
	 *
	 * @return array
	 */
	public function getArrayCopy()
	{
		return array(
			'ProjectId' => $this->projectId,
			'Name' => $this->name,
			'Description' => $this->description,
			'Created' => $this->created
		);
	}

	/**
	 * Retrieve project's identifier.
	 *
	 * @return integer|null
	 */
	public function getProjectId()
	{
		return $this->projectId;
	}

	/**
	 * Set project's identifier.
	 *
	 * @param integer $val
	 * @return \odTimeTracker\Model\ProjectEntity
	 */
	public function setProjectId($val)
	{
		$this->projectId = $val ? (int) $val : null;
	}

	/**
	 * Retrieve project's name.
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set project's name.
	 *
	 * @param string $val
	 * @return \odTimeTracker\Model\ProjectEntity
	 */
	public function setName($val)
	{
		$this->name = $val ? (string) $val : null;
	}

	/**
	 * Retrieve project's description.
	 *
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set project's description.
	 *
	 * @param string $val
	 * @return \odTimeTracker\Model\ProjectEntity
	 */
	public function setDescription($val)
	{
		$this->description = $val ? (string) $val : null;
	}

	/**
	 * Retrieve date time when was the project created.
	 *
	 * @return \DateTime|null
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * Set date time when was the project created.
	 *
	 * @param DateTime|string $val Date time of creation.
	 * @return \odTimeTracker\Model\ProjectEntity
	 */
	public function setCreated($val)
	{
		if (($val instanceof \DateTime)) {
			$this->created = $val;
		}
		else if (is_string($val)) {
			$this->created = new \DateTime($val);
		}
		else {
			$this->created = null;
		}

		return $this;
	}
}
