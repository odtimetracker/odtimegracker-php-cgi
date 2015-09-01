<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

/**
 * Mapper for activities.
 */
class ActivityMapper extends AbstractMapper
{
	/**
	 * @const string Database table name.
	 */
	const TABLE_NAME = 'Activities';

	/**
	 * @const string Primary key column name.
	 */
	const PK_COL_NAME = 'ActivityId';

	/**
	 * Create schema.
	 *
	 * @return boolean
	 */
	function createSchema()
	{
		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
CREATE TABLE IF NOT EXISTS $table (
	ActivityId INTEGER PRIMARY KEY,
	ProjectId INTEGER NOT NULL,
	Name TEXT,
	Description TEXT,
	Tags TEXT,
	Started TEXT NOT NULL,
	Stopped TEXT NOT NULL DEFAULT '',
	FOREIGN KEY(ProjectId) REFERENCES Projects(ProjectId) 
)
EOD
		);

		return $stmt->execute();
	}

	/**
	 * Insert new record.
	 *
	 * @param \odTimeTracker\Model\ActivityEntity|array $data
	 * @return \odTimeTracker\Model\ActivityEntity|boolean Returns FALSE if anything goes wrong.
	 */
	function insert($entity)
	{
		if (!is_array($entity) && !($entity instanceof ActivityEntity)) {
			return false;
		}

		$projectId = is_array($entity) ? $entity['ProjectId'] : $entity->getProjectId();
		$name = is_array($entity) ? $entity['Name'] : $entity->getName();
		$desc = is_array($entity) ? (array_key_exists('Description', $entity) ? $entity['Description'] : '') : $entity->getDescription();
		$tags = is_array($entity) ? (array_key_exists('Tags', $entity) ? $entity['Tags'] : '') : $entity->getTags();
		$started = is_array($entity) ? (array_key_exists('Started', $entity) ? $entity['Started'] : null) : $entity->getStarted();
		$startedObj = is_null($started) ? new \DateTime() : (($started instanceof \DateTime) ? $started : new \DateTime($started));
		$startedStr = $startedObj->format(\DateTime::RFC3339);
		$stopped = is_array($entity) ? (array_key_exists('Stopped', $entity) ? $entity['Stopped'] : null) : $entity->getStopped();
		$stoppedObj = is_null($stopped) ? null : (($stopped instanceof \DateTime) ? $stopped : new \DateTime($stopped));
		$stoppedStr = ($stopped instanceof \DateTime) ? $stoppedObj->format(\DateTime::RFC3339) : '';

		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
INSERT INTO $table (ProjectId, Name, Description, Tags, Started, Stopped) 
VALUES ( :projectId, :name , :desc , :tags , :started , :stopped );
EOD
		);
		$stmt->bindParam(':projectId', $projectId, \PDO::PARAM_INT);
		$stmt->bindParam(':name', $name, \PDO::PARAM_STR);
		$stmt->bindParam(':desc', $desc, \PDO::PARAM_STR);
		$stmt->bindParam(':tags', $tags, \PDO::PARAM_STR);
		$stmt->bindParam(':started', $startedStr, \PDO::PARAM_STR);
		$stmt->bindParam(':stopped', $stoppedStr);
		$res = $stmt->execute();
		//var_dump($stmt->debugDumpParams());exit();

		if ($res === false) {
			return false;
		}

		return new ActivityEntity(array(
			'ProjectId' => $this->db->getPdo()->lastInsertId(),
			'Name' => $name,
			'Description' => empty($description) ? null : $description,
			'Tags' => empty($tags) ? null : $tags,
			'Started' => $started,
			'Stopped' => $stopped
		));
	}

	/**
	 * Update record.
	 *
	 * @param \odTimeTracker\Model\ActivityEntity $entity
	 * @return \odTimeTracker\Model\ActivityEntity|boolean Returns FALSE if anything goes wrong.
	 * @todo Implement `ActivityMapper.update`!
	 */
	function update($entity)
	{
		// ...
	}

	/**
	 * Select all records.
	 *
	 * @param integer $limit (Optional.)
	 * @return array Returns array of {@see \odTimeTracker\Model\ActivityEntity}.
	 */
	function selectAll($limit = 0)
	{
		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
SELECT 
	t1.*,
	t2.ProjectId AS "Project.ProjectId",
	t2.Name AS "Project.Name",
	t2.Description AS "Project.Description",
	t2.Created AS "Project.Created"   
FROM $table AS t1 
LEFT JOIN Projects AS t2 ON t1.ProjectId = t2.ProjectId 
ORDER BY t1.Started DESC ;
EOD
		);
		$res = $stmt->execute();

		if ($res === false) {
			return array();
		}

		$rows = $stmt->fetchAll();

		$ret = array();
		foreach ($rows as $row) {
			array_push($ret, new ActivityEntity($row));
		}

		return $ret;
	}

	/**
	 * Select recent activities.
	 *
	 * @param integer $limit
	 * @return array
	 */
	public function selectRecentActivities($limit)
	{
		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
SELECT 
	t1.*,
	t2.ProjectId AS "Project.ProjectId",
	t2.Name AS "Project.Name",
	t2.Description AS "Project.Description",
	t2.Created AS "Project.Created"
FROM $table AS t1 
LEFT JOIN Projects AS t2 ON t1.ProjectId = t2.ProjectId 
ORDER BY t1.Started DESC 
LIMIT :limit ;
EOD
		);
		$stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
		$res = $stmt->execute();

		if ($res === false) {
			return array();
		}

		$rows = $stmt->fetchAll();

		$ret = array();
		foreach ($rows as $row) {
			array_push($ret, new ActivityEntity($row));
		}

		return $ret;
	}

	/**
	 * Select currently running activity.
	 *
	 * @return \odTimeTracker\Model\ActivityEntity|null
	 */
	public function selectRunningActivity()
	{
		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
SELECT 
	t1.*,
	t2.ProjectId AS "Project.ProjectId",
	t2.Name AS "Project.Name",
	t2.Description AS "Project.Description",
	t2.Created AS "Project.Created"
FROM $table AS t1 
LEFT JOIN Projects AS t2 ON t1.ProjectId = t2.ProjectId 
WHERE t1.Stopped IS NULL OR t1.Stopped = '' 
LIMIT 1 ;
EOD
		);
		$res = $stmt->execute();

		if ($res === false) {
			return null;
		}

		$row = $stmt->fetch();

		if (!is_array($row)) {
			return null;
		}

		return new ActivityEntity($row);
	}

	/**
	 * Select activities which was started in given interval.
	 *
	 * @param string $dateFrom
	 * @param string $dateTo
	 * @return array
	 */
	public function selectActivitiesForInterval($dateFrom, $dateTo)
	{
		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
SELECT 
	t1.*,
	t2.ProjectId AS "Project.ProjectId",
	t2.Name AS "Project.Name",
	t2.Description AS "Project.Description",
	t2.Created AS "Project.Created"
FROM $table AS t1 
LEFT JOIN Projects AS t2 ON t1.ProjectId = t2.ProjectId 
WHERE t1.Started > :dateFrom AND t1.Started < :dateTo  
ORDER BY t1.Started DESC ;
EOD
		);
		$stmt->bindParam(':dateFrom', $dateFrom, \PDO::PARAM_STR);
		$stmt->bindParam(':dateTo', $dateTo, \PDO::PARAM_STR);
		$res = $stmt->execute();

		if ($res === false) {
			return array();
		}

		$rows = $stmt->fetchAll();

		$ret = array();
		foreach ($rows as $row) {
			array_push($ret, new ActivityEntity($row));
		}

		return $ret;
	}

	/**
	 * Starts new activity. Returns FALSE when inserting failed (usually
	 * when another activity is running). Otherwise returns instance of
	 * {@see \odTimeTracker\Model\ActivityEntity}.
	 *
	 * @param string $name
	 * @param integer $projectId
	 * @param string $description (Optional.)
	 * @param string $tags (Optional.)
	 * @return \odTimeTracker\Model\ActivityEntity|boolean
	 */
	public function startActivity($name, $projectId, $description = '', $tags = '')
	{
		// Only one activity can be executed at once!
		if (($this->selectRunningActivity() instanceof ActivityEntity)) {
			return false;
		}

		$started = new \DateTime();
		$startedStr = $started->format(\DateTime::RFC3339);

		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
INSERT INTO $table (ProjectId, Name, Description, Tags, Started) 
VALUES ( :projectId , :name , :description , :tags, :started );
EOD
		);
		$stmt->bindParam(':projectId', $projectId, \PDO::PARAM_INT);
		$stmt->bindParam(':name', $name, \PDO::PARAM_STR);
		$stmt->bindParam(':description', $description, \PDO::PARAM_STR);
		$stmt->bindParam(':tags', $tags, \PDO::PARAM_STR);
		$stmt->bindParam(':started', $startedStr, \PDO::PARAM_STR);
		$res = $stmt->execute();

		if ($res === false) {
			return false;
		}

		return new ActivityEntity(array(
			'ActivityId' => $this->db->getPdo() ->lastInsertId(),
			'ProjectId' => $projectId,
			'Name' => $name,
			'Description' => empty($description) ? null : $description,
			'Tags' => empty($tags) ? null : $tags,
			'Started' => $started
		));
	}

	/**
	 * Stops currently running activity.
	 *
	 * @return boolean
	 */
	public function stopRunningActivity()
	{
		$runningActivity = $this->selectRunningActivity();

		// There is no running activity...
		if (!($runningActivity instanceof ActivityEntity)) {
			return false;
		}

		$nowObj = new \DateTime('now');
		$nowStr = $nowObj->format(\DateTime::RFC3339);
		$activityId = $runningActivity->getActivityId();

		$table = self::TABLE_NAME;
		$stmt = $this->db->getPdo()->prepare(<<<EOD
UPDATE $table 
SET Stopped = :stopped 
WHERE ActivityId = :activityId ;
EOD
		);
		$stmt->bindParam(':stopped', $nowStr, \PDO::PARAM_STR);
		$stmt->bindParam(':activityId', $activityId, \PDO::PARAM_INT);
		$res = $stmt->execute();

		return $res;
	}
}
