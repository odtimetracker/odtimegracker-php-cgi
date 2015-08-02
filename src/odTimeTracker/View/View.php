<?php
/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
namespace odTimeTracker\View;

/**
 * View class.
 */
class View implements ViewInterface
{
	/**
	 * @var string $template
	 */
	protected $template;

	/**
	 * @var array $properties
	 */
	protected $properties;

	/**
	 * Controller.
	 *
	 * @param string $template
	 * @return void
	 */
	public function __construct($template = null)
	{
		if (empty($template) || is_null($template)) {
			throw new \InvalidArgumentException('Template name is not set!');
		}

		$template = 'view' . DIRECTORY_SEPARATOR . $template;

		if (!file_exists($template)) {
			throw new \Exception('Template file was not found!');
		}

		$this->template = $template;
		$this->properties = array();
	}

	/**
	 * Retrieve view variable.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}

		throw new \InvalidArgumentException('Property "' . $name . '" was not found!');
	}

	/**
	 * Set view variable.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->properties[$name] = $value;
	}

	/**
	 * Render view.
	 *
	 * @return void
	 */
	public function render()
	{
		header('Content-Type: text/html; charset=UTF-8');
		extract($this->properties);
		include $this->template;
	}
}
