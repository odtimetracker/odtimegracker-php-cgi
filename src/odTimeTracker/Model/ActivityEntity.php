<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

/**
 * Activity entity.
 */
class ActivityEntity implements \odTimeTracker\Model\EntityInterface
{
	/**
	 * Activity's identifier.
	 *
	 * @var integer $activityId
	 */
	protected $activityId;

	/**
	 * Identifier of activity's project.
	 *
	 * @var integer $projectId
	 */
	protected $projectId;

	/**
	 * Activity's name.
	 *
	 * @var string $name
	 */
	protected $name;

	/**
	 * Activity's description.
	 *
	 * @var string $description
	 */
	protected $description;

	/**
	 * Comma-separated tags attached to the activity.
	 *
	 * @var string $tags
	 */
	protected $tags;

	/**
	 * Date time when was activity started (RFC3339).
	 *
	 * @var \DateTime $started
	 */
	protected $started;

	/**
	 * Date time when was activity started (RFC3339).
	 *
	 * @var \DateTime $stopped
	 */
	protected $stopped;

	/**
	 * Referenced project entity.
	 *
	 * @var \odTimeTracker\Model\ProjectEntity
	 */
	protected $project;

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
		$this->setActivityId(isset($data['ActivityId']) ? $data['ActivityId'] : null);
		$this->setProjectId(isset($data['ProjectId']) ? $data['ProjectId'] : null);
		$this->setName(isset($data['Name']) ? $data['Name'] : null);
		$this->setDescription(isset($data['Description']) ? $data['Description'] : null);
		$this->setTags(isset($data['Tags']) ? $data['Tags'] : null);
		$this->setStarted(isset($data['Started']) ? $data['Started'] : null);
		$this->setStopped(isset($data['Stopped']) ? $data['Stopped'] : null);

		if (array_key_exists('Project.ProjectId', $data)) {
			$this->project = new ProjectEntity(array(
				'ProjectId' => $data['Project.ProjectId'],
				'Name' => $data['Project.Name'],
				'Description' => $data['Project.Description'],
				'Created' => $data['Project.Created']
			));
		}
	}

	/**
	 * Retrieve entity as array.
	 *
	 * @return array
	 */
	public function getArrayCopy()
	{
		return array(
			'ActivityId' => $this->activityId,
			'ProjectId' => $this->projectId,
			'Name' => $this->name,
			'Description' => $this->description,
			'Tags' => $this->tags,
			'Started' => $this->started,
			'Stopped' => $this->stopped
		);
	}

	/**
	 * Retrieve activity's identifier.
	 *
	 * @return integer|null
	 */
	public function getActivityId()
	{
		return $this->activityId;
	}

	/**
	 * Set activity's identifier.
	 *
	 * @param integer $val
	 * @return \odTimeTracker\Model\ActivityEntity
	 */
	public function setActivityId($val)
	{
		$this->activityId = $val ? (int) $val : null;
	}

	/**
	 * Retrieve identifier of activity's project.
	 *
	 * @return integer|null
	 */
	public function getProjectId()
	{
		return $this->projectId;
	}

	/**
	 * Set identifier of activity's project.
	 *
	 * @param integer $val
	 * @return \odTimeTracker\Model\ActivityEntity
	 */
	public function setProjectId($val)
	{
		$this->projectId = $val ? (int) $val : null;
	}

	/**
	 * Retrieve activity's name.
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set activity's name.
	 *
	 * @param string $val
	 * @return \odTimeTracker\Model\ActivityEntity
	 */
	public function setName($val)
	{
		$this->name = $val ? (string) $val : null;
	}

	/**
	 * Retrieve activity's description.
	 *
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set activity's description.
	 *
	 * @param string $val
	 * @return \odTimeTracker\Model\ActivityEntity
	 */
	public function setDescription($val)
	{
		$this->description = $val ? (string) $val : null;
	}

	/**
	 * Retrieve tags attached to activity.
	 *
	 * @return string|null
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Retrieve tags as an array.
	 *
	 * @return array
	 */
	public function getTagsAsArray()
	{
		return explode(',', $this->getTags());
	}

	/**
	 * Set tags attached to activity.
	 *
	 * @param string $val
	 * @return \odTimeTracker\Model\ActivityEntity
	 */
	public function setTags($val)
	{
		$this->tags = $val ? (string) $val : null;
	}

	/**
	 * Retrieve date time when was the activity started.
	 *
	 * @return \DateTime|null
	 */
	public function getStarted()
	{
		return $this->started;
	}

	/**
	 * Set date time when was the activity started.
	 *
	 * @param DateTime|string $val Date time of creation.
	 * @return \odTimeTracker\Model\ActivityEntity
	 */
	public function setStarted($val)
	{
		if (($val instanceof \DateTime)) {
			$this->started = $val;
		}
		else if (is_string($val)) {
			$this->started = new \DateTime($val);
		}
		else {
			$this->started = null;
		}

		return $this;
	}

	/**
	 * Retrieve date time when was the activity stopped.
	 *
	 * @return \DateTime|null
	 */
	public function getStopped()
	{
		return $this->stopped;
	}

	/**
	 * Set date time when was the activity stopped.
	 *
	 * @param DateTime|string $val Date time of creation.
	 * @return \odTimeTracker\Model\ActivityEntity
	 */
	public function setStopped($val)
	{
		if (($val instanceof \DateTime)) {
			$this->stopped = $val;
		}
		else if (is_string($val) && !empty($val)) {
			$this->stopped = new \DateTime($val);
		}
		else {
			$this->stopped = null;
		}

		return $this;
	}

	/**
	 * Retrieve entity of the project to which activity belongs.
	 *
	 * @return \odTimeTracker\Model\ProjectEntity|null
	 */
	public function getProject()
	{
		return $this->project;
	}

	/**
	 * Retrieve duration between started and stopped. If stopped is `NULL`
	 * calculates duration up to now.
	 *
	 * @return \DateInterval
	 */
	public function getDuration()
	{
		$stopped = (is_null($this->stopped)) ? new \DateTime('now') : $this->stopped;

		return $this->started->diff($stopped);
	}

	/**
	 * Retrieve duration as formatted string.
	 *
	 * Note: Currently we are displaying just hours and minutes and we does not expect 
	 * activities that takes more than day.
	 *
	 * @return string
	 */
	public function getDurationFormatted()
	{
		$duration = $this->getDuration();
		$ret = '';

		if ($duration->h == 0 && $duration->m == 0 && $duration->s > 0) {
			return 'Less than minute';
		}

		if ($duration->h == 1) {
			$ret .= 'One hour';
		}
		else if ($duration->h > 1) {
			$ret .= $duration->h . ' hours';
		}

		if ($duration->m == 1) {
			if (!empty($ret)) {
				$ret .= ', one minute';
			}
			else {
				$ret .= 'One minute';
			}
		}
		else if ($duration->m > 1) {
			if (!empty($ret)) {
				$ret .= ', ' . $duration->m . ' minutes';
			}
			else {
				$ret .= $duration->m . ' minutes';
			}
		}

		return $ret;
	}

	/**
	 * Returns `TRUE` if activity is started and stopped within one day.
	 *
	 * @return boolean
	 */
	public function isWithinOneDay()
	{
		$started = $this->getStarted();
		$stopped = (is_null($this->stopped)) ? new \DateTime('now') : $this->stopped;

		return ($started->format('Y-m-d') === $stopped->format('Y-m-d'));
	}
}