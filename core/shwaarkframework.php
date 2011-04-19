<?php	
	ob_start();

	// load framework config
	require(BASEPATH.'config.inc.php');
	$config = $config[$environement];

	// used for perf test in debug mod
	if($config['debug'])
		$start = microtime();

	session_start();

	/***********************************************/
	/**** Include class framework and init them ****/
	/***********************************************/

	// don't necesary load orm class if no sql needed
	if($config['sql']){
		require(BASEPATH.'idiorm.class.php');
		require(BASEPATH.'paris.class.php');
		ORM::configure('mysql:host='.$config['host'].';dbname='.$config['base']);
		ORM::configure('username', $config['log']);
		ORM::configure('password', $config['pass']);
	}
	
	//load defaults helpers
	require(BASEPATH.'helpers.class.php');

	//load the abstract controler class, used to be extend by user controller
	require(BASEPATH.'controller.class.php');

	//load the view controler class used by templates
	require(BASEPATH.'view.class.php');
	
	//block array, used to render partial
	$blocks = array();

	//get current adresse path
	$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
	$uri_array = '';

	//check if current path is not root url or core url, else return array of current route
	if (trim($path, '/') != '' && $path != "/".SELF)
	{
		$uri_array = explode('/', trim($path, '/'));
	}

	//check if uri_array is not empty and check if it contain a route app
	if(is_array($uri_array)){
		if(array_key_exists('/'.$uri_array[0], $config['routes'])){
			$app = $config['routes']['/'.$uri_array[0]].'/';

			if(count($uri_array) > 1)
				unset($uri_array[0]);
			else
				$uri_array = '';
		}
	}

	//define CURRENT_APP with asked app or default app
	DEFINE('CURRENT_APP', isset($app) ? $app : $config['routes']['default'].'/');

	//check if route file exists in app dir
	if(is_file(APPS.CURRENT_APP.'routes.php')){
		include(APPS.CURRENT_APP.'routes.php');

		//define default controller & action and unset them from array for route control
		$default_controller = isset($routes['default']['controller']) ? $routes['default']['controller'] : '' ;
		$default_action = isset($routes['default']['action']) ? $routes['default']['action'] : '';
		unset($routes['default']);
		$options = null;

		if($config['debug'])
			$route = 'default';

		if(is_array($uri_array)){

			//impode current uri for control
			$uri = implode('/', $uri_array);
	
			//first, check if current raw uri look like one route
			if(isset($routes[$uri])){
				$controller = $routes[$uri]['controller'];
				$action = $routes[$uri]['action'];
			}

			//second, check each routes
			if(!isset($controller) && !isset($action)){
				foreach ($routes as $key => $val){

					//for each route, replace the :any, :alpha and :num by regex for control
					$parsedKey = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', str_replace(':alpha', '([a-zA-Z]+)', $key)));

					//try if current uri look like the parsed route
					if (preg_match('#^'.$parsedKey.'$#', $uri, $array)){
						unset($array[0]);
						sort($array);

						$controller = $routes[$key]['controller'];
						$action = $routes[$key]['action'];
						$options = $array;

						if($config['debug'])
							$route = $key;
					}
				}
			}

			//third, if no routes look like our uri, try the 404 route;
			if(!isset($controller) && !isset($action)){
				if(isset($routes['404'])){
					$controller = $routes['404']['controller'];
					$action = $routes['404']['action'];
				}
			}
		}

		$controller = isset($controller) ? $controller : $default_controller;
		$action = isset($action) ? $action : $default_action;
	}

	
	if(isset($controller) && isset($action)){
		include(APPS.CURRENT_APP.'controllers/'.$controller.'.php');

		$theApp = new $controller($controller, $action);
		$theApp->$action($options);
	
		//check if we have a layout and render it if yes
		if($theApp->hasLayout()){
			$theApp->render();
		}
	}
	
	if($config['debug']){
		$end = microtime() - $start;
		include(BASEPATH.'debug.php');
	}

	ob_end_flush();
?>