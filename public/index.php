<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

/**
 * @var array $config
 */
$config = require('config/config.local.php');

try {
	$app = new \odTimeTracker\Application($config);
	$app->execute();
} catch (Exception $e) {
	if ($config['debug'] === true) {
		var_dump($e);
		exit();
	}

	// Failsafe error handler; just display static error page
	header('Content-Type: text/html; charset=UTF-8');
	echo file_get_contents('500.html');
}
