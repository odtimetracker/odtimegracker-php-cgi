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
		$dateFrom = $this->getParam('dateFrom', date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day')));
		$dateTo = $this->getParam('dateTo', date('Y-m-d'));

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
	 * Returns JSON with projects.
	 *
	 * @return void
	 */
	public function selectProjectsAction()
	{
		$projectMapper = new \odTimeTracker\Model\ProjectMapper($this->db);
		$projects = $projectMapper->selectAll();
		$data = array('projects' => array());

		foreach ($projects as $project) {
			$data['projects'][] = $project->getArrayCopy();
		}

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($data);
	}

	/**
	 * Starts new activity.
	 *
	 * @return void
	 */
	public function startActivityAction()
	{
		header('Content-Type: application/json; charset=UTF-8');

		// Check given parameters
		$name = $this->getPostParam('Name');
		$projectId = $this->getPostParam('ProjectId');
		$description = $this->getPostParam('Description');
		$tags = $this->getPostParam('Tags');

		if (empty($name) || empty($projectId) || !is_numeric($projectId)) {
			echo json_encode(array(
				'errorMessage' => 'Bad parameters given!'
			));
			return;
		}

		// TODO Check if `$projectId` is correct!

		$activityMapper = new \odTimeTracker\Model\ActivityMapper($this->db);
		$activity = $activityMapper->startActivity($name, (int) $projectId, $description, $tags);

		if (!($activity instanceof \odTimeTracker\Model\ActivityEntity)) {
			echo json_encode(array(
				'errorMessage' => 'Activity was not successfully started!'
			));
			return;
		}

		echo json_encode(array(
			'runningActivity' => $activity,
			'message' => 'Activity was successfully started.'
		));
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
