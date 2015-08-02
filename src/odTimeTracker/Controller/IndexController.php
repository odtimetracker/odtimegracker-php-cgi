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

		$view->title = 'odTimeTracker';
		$view->subtitle = '';

		// 2) Get the recent activities
		$activityMapper = new \odTimeTracker\Model\ActivityMapper($this->db);
		$view->activities = $activityMapper->selectRecent();

		// 3) Render template
		$view->render();
	}
}
