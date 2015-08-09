<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker;

/**
 * Main application's class.
 */
class Application
{
	/**
	 * @var array $config
	 */
	protected $config;

	/**
	 * @var \odTimeTracker\Db\MyPdo $db
	 */
	protected $db;

	/**
	 * Controller to use.
	 *
	 * @var \odTimeTracker\Controller\ControllerInterface $controller
	 */
	protected $controller;

	/**
	 * Name of controller's action to use.
	 *
	 * @var string $action
	 */
	protected $action;

	/**
	 * Constructor.
	 *
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config)
	{
		$this->config = $config;

		$requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
		if (strpos($requestUri, '/json') === 0) {
			$this->controller = new \odTimeTracker\Controller\JsonController($this->config);

			$action = lcfirst(str_replace('/json/', '', $requestUri));

			// Strip off GET parameters
			if (strpos($action, '?')) {
				$parts = explode('?', $action);
				$action = $parts[0];
			}

			// Check if requested method exists in controller
			$reflector = new \ReflectionClass($this->controller);
			if ($reflector->hasMethod($action . 'Action') !== true) {
				header('Content-Type: application/json; charset=UTF-8');
				echo json_encode(array(
					'errorMessage' => 'Requested method `' . $action . '` is not found!'
				));
				exit();
			}

			$this->action = $action;
		}
		elseif ($requestUri === '/') {
			$this->controller = new \odTimeTracker\Controller\IndexController($this->config);
			$this->action = 'index';
		}

		header('Status-Code: 404 Not Found');
		//header('Location: ' . $this->config['url'] . '404');
	}

	/**
	 * Execute application.
	 *
	 * @return void
	 */
	public function execute()
	{
		if (!($this->controller instanceof \odTimeTracker\Controller\ControllerInterface)) {
			throw new \Exception('Controller is not set.');
		}

		if (empty($this->action)) {
			$this->action = 'index';
		}

		$actionName = strtolower($this->action) . 'Action';

		try {
			$this->controller->{$actionName}();
		} catch (\Exception $e) {
			$reflector = new \ReflectionClass($this->controller);

			if ($reflector->hasMethod('errorAction')) {
				$this->controller->errorAction();
			}

			throw new \Exception('Unhandled controller exception "' . $e->getMessage() . '"!');
		}
	}
}