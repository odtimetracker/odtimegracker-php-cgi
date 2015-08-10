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
class ActivityMapper extends CommonMapper
{
	/**
	 * Select recent activities.
	 *
	 * @param integer $limit
	 * @return array
	 */
	public function selectRecentActivities($limit)
	{
		$stmt = $this->db->getPdo()->prepare(<<<EOD
SELECT 
	`t1`.*,
	`t2`.`ProjectId` AS `Project.ProjectId`,
	`t2`.`Name` AS `Project.Name`,
	`t2`.`Description` AS `Project.Description`,
	`t2`.`Created` AS `Project.Created`   
FROM `Activities` AS `t1` 
LEFT JOIN `Projects` AS `t2` ON `t1`.`ProjectId` = `t2`.`ProjectId` 
ORDER BY `t1`.`Started` DESC 
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
		$stmt = $this->db->getPdo()->prepare(<<<EOD
SELECT 
	`t1`.*,
	`t2`.`ProjectId` AS `Project.ProjectId`,
	`t2`.`Name` AS `Project.Name`,
	`t2`.`Description` AS `Project.Description`,
	`t2`.`Created` AS `Project.Created`   
FROM `Activities` AS `t1` 
LEFT JOIN `Projects` AS `t2` ON `t1`.`ProjectId` = `t2`.`ProjectId` 
WHERE `t1`.`Stopped` IS NULL OR `t1`.`Stopped` = '' 
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
		$stmt = $this->db->getPdo()->prepare(<<<EOD
SELECT 
	`t1`.*,
	`t2`.`ProjectId` AS `Project.ProjectId`,
	`t2`.`Name` AS `Project.Name`,
	`t2`.`Description` AS `Project.Description`,
	`t2`.`Created` AS `Project.Created`   
FROM `Activities` AS `t1` 
LEFT JOIN `Projects` AS `t2` ON `t1`.`ProjectId` = `t2`.`ProjectId` 
WHERE `t1`.`Started` > :dateFrom AND `t1`.`Started` < :dateTo 
ORDER BY `t1`.`Started` DESC ;
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
	 * Starts new activity. Returns `FALSE` when inserting failed (usually
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

		$stmt = $this->db->getPdo()->prepare(<<<EOD
INSERT INTO `Activities` (`ProjectId`, `Name`, `Description`, `Tags`, `Started`) 
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

$stmt = $this->db->getPdo()->prepare(<<<EOD
UPDATE `Activities` 
SET `Stopped` = :stopped 
WHERE `ActivityId` = :activityId ;
EOD
		);

		$nowObj = new \DateTime('now');
		$nowStr = $nowObj->format(\DateTime::RFC3339);
		$activityId = $runningActivity->getActivityId();

		$stmt->bindParam(':stopped', $nowStr, \PDO::PARAM_STR);
		$stmt->bindParam(':activityId', $activityId, \PDO::PARAM_INT);
		$res = $stmt->execute();

		return $res;
	}
}
