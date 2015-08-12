<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

/**
 * Abstract mapper.
 */
abstract class AbstractMapper implements MapperInterface
{
	/**
	 * @var \Blog\Db\MyPdo $db
	 */
	protected $db;

	/**
	 * Constructor.
	 *
	 * @param \Blog\Db\MyPdo $db
	 * @return void
	 */
	public function __construct(\odTimeTracker\Db\MyPdo $db)
	{
		$this->db = $db;
	}

	/**
	 * Create schema.
	 *
	 * @return boolean
	 */
	abstract function createSchema();

	/**
	 * Select all records.
	 *
	 * @param integer $limit (Optional.)
	 * @return array Returns array of {@see \odTimeTracker\Model\EntityInterface}.
	 */
	abstract function selectAll($limit = 0);

	/**
	 * Insert new record.
	 *
	 * @param \odTimeTracker\Model\EntityInterface|array $data
	 * @return \odTimeTracker\Model\EntityInterface|boolean Returns `FALSE` if anything goes wrong.
	 */
	abstract function insert($entity);

	/**
	 * Update record.
	 *
	 * @param \odTimeTracker\Model\EntityInterface $entity
	 * @return \odTimeTracker\Model\EntityInterface|boolean Returns `FALSE` if anything goes wrong.
	 */
	abstract function update($entity);

	/**
	 * Delete record.
	 *
	 * @param \odTimeTracker\Model\EntityInterface|integer $entity
	 * @return \odTimeTracker\Model\EntityInterface|boolean Returns `FALSE` if anything goes wrong.
	 */
	function delete($entity)
	{
		$entityId = null;

		if (is_numeric($entity)) {
			$entityId = intval($entity);
		} else if (($entity instanceof EntityInterface)) {
			$entityId = $entity->getId();
		}

		if (is_null($entityId) || is_int($entityId)) {
			return false;
		}

		$stmt = $this->db->getPdo()->prepare(<<<EOD
DELETE FROM `:table` WHERE `:pkcolname` = :entityId LIMIT 1;
EOD
		);
		$stmt->bindParam(':table', self::TABLE_NAME);
		$stmt->bindParam(':pkcolname', self::PK_COL_NAME);
		$stmt->bindParam(':entityId', $entityId, \PDO::PARAM_INT);
	}

}
