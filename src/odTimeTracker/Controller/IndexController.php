<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\Controller;

/**
 * Index controller.
 */
class IndexController extends CommonController
{
	/**
	 * Main action.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		// 1) Get and prepare view
		$view = new \odTimeTracker\View\View('index/index.phtml');

		$view->baseUrl = 'http://odtimetracker.local/';
		$view->title = 'odTimeTracker';
		$view->description = 'Simple tool for tracking activity.';
		$view->subtitle = '';
		$view->copyright = '&copy; 2015 Ondřej Doněk';

		// 2) Get the recent activities
		$activityMapper = new \odTimeTracker\Model\ActivityMapper($this->db);
		$view->activities = $activityMapper->selectRecentActivities(10);

		// 3) Render template
		$view->render();
	}
}
