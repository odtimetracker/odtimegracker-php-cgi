<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Controller;

/**
 * JSON controller.
 *
 * @todo Translate all messages!
 */
class JsonController extends CommonController
{
	/**
	 * Returns JSON with details about curently running activity.
	 *
	 * @return void
	 */
	public function selectRunningActivityAction()
	{
		$activityMapper = new \odTimeTracker\Model\ActivityMapper($this->db);
		$runningActivity = $activityMapper->selectRunningActivity();

		header('Content-Type: application/json; charset=UTF-8');

		if (!($runningActivity instanceof \odTimeTracker\Model\ActivityEntity)) {
			echo json_encode(array(
				'runningActivity' => null,
				'message' => 'There is no running activity.'
			));
			return;
		}

		echo json_encode(array(
			'runningActivity' => $runningActivity->getArrayCopy(),
			'projects' => array(
				$runningActivity->getProjectId() => $runningActivity->getProject()->getArrayCopy()
			)
		));
	}

	/**
	 * Returns JSON with activities for the given day.
	 *
	 * @return void
	 */
	public function selectActivitiesAction()
	{
		$dateFrom = $this->getParam('dateFrom', date('Y-m-d'));
		$dateTo = $this->getParam('dateTo', date('Y-m-d', time() + 60*60*24));

		$activityMapper = new \odTimeTracker\Model\ActivityMapper($this->db);
		$activities = $activityMapper->selectActivitiesForInterval($dateFrom, $dateTo);
		$data = array(
			'activities' => array(),
			'projects' => array()
		);

		foreach ($activities as $activity) {
			$data['activities'][] = $activity->getArrayCopy();

			if (array_key_exists($activity->getProjectId(), $data['projects']) !== true) {
				$data['projects'][$activity->getProjectId()] = $activity->getProject()->getArrayCopy();
			}
		}

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($data);
	}

	/**
	 * Stops currently running activity.
	 *
	 * @return void
	 */
	public function stopRunningActivityAction()
	{
		$activityMapper = new \odTimeTracker\Model\ActivityMapper($this->db);
		$ret = $activityMapper->stopRunningActivity();

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode(array('stopped' => $ret));
	}
}
