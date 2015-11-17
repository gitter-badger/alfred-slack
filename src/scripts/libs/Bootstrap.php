<?php

namespace AlfredSlack\Libs;

class Bootstrap {

	public function run (Query $config) {

		// Set the timezone for the user using the system one
        Utils::defineTimeZone();

        // Retrieve the route from the given parameters
		$route = Router::getRoute($config);
		if ($route !== false) {
			$this->invoke($route);
		}
	}

	private function invoke (Route $route) {
		$className = 'AlfredSlack\Controllers\\'.ucfirst($route->getController()).'Controller';
		$actionName = $route->getAction().'Action';
		$controller = new $className();

		if (!($controller instanceof \AlfredSlack\Controllers\Controller)) {
			throw new \Exception("$className must inherits from AlfredSlack\Controllers\Controller");
		}

		error_log($className.'::'.$actionName.'()'.PHP_EOL);
		
		$interruptAction = ($controller->preDispatch($actionName, $route->getParams()) === false);
		if (!$interruptAction) {
			$actionResult = $controller->dispatch($actionName, $route->getParams());
			$controller->postDispatch($actionName, $route->getParams(), $actionResult);
		}
	}

}
