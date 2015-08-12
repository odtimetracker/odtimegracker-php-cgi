<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Model;

/**
 * Interface for mappers.
 */
interface MapperInterface
{
	/**
	 * Create schema.
	 *
	 * @return boolean
	 */
	public function createSchema();

	/**
	 * Select all records.
	 *
	 * @param integer $limit (Optional.)
	 * @return array Returns array of {@see \odTimeTracker\Model\EntityInterface}.
	 */
	public function selectAll($limit = 0);

	/**
	 * Insert new record.
	 *
	 * @param \odTimeTracker\Model\EntityInterface|array $data
	 * @return \odTimeTracker\Model\EntityInterface|boolean Returns `FALSE` if anything goes wrong.
	 */
	public function insert($entity);

	/**
	 * Update record.
	 *
	 * @param \odTimeTracker\Model\EntityInterface $entity
	 * @return \odTimeTracker\Model\EntityInterface|boolean Returns `FALSE` if anything goes wrong.
	 */
	public function update($entity);

	/**
	 * Delete record.
	 *
	 * @param \odTimeTracker\Model\EntityInterface|integer $entity
	 * @return \odTimeTracker\Model\EntityInterface|boolean Returns `FALSE` if anything goes wrong.
	 */
	public function delete($entity);
}
