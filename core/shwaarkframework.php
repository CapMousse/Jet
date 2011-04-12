<?php	
	ob_start();

	// load framework config
	require(BASEPATH.'config.inc.php');

	// used for perf test in debug mod
	if(DEBUG)
		$start = microtime();

	session_start();
	
	// prevent from stollen session
	if(isset($_SESSION['HTTP_USER_AGENT'])){
		if($_SESSION['HTTP_USER_AGENT'] != sha1($_SERVER['HTTP_USER_AGENT'].'sand')){
			exit;
		}
	}else{
		$_SESSION['HTTP_USER_AGENT'] = sha1($_SERVER['HTTP_USER_AGENT'].'sand');
	}
	
	/***********************************************/
	/**** Include class framework and init them ****/
	/***********************************************/

	// don't necesary load orm class if no sql needed
	if(SQL){
		require(BASEPATH.'idiorm.class.php');
		require(BASEPATH.'paris.class.php');
		ORM::configure('mysql:host='.HOST.';dbname='.BASE);
		ORM::configure('username', LOG);
		ORM::configure('password', PASS);
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
		if(array_key_exists('/'.$uri_array[0], $routesApp)){
			$app = $routesApp['/'.$uri_array[0]].'/';
			unset($uri_array[0]);
		}
	}

	//define CURRENT_APP with asked app or default app
	DEFINE('CURRENT_APP', isset($app) ? $app : $routesApp['default'].'/');

	//check if route file exists in app dir
	if(is_file(APPS.'/'.CURRENT_APP.'/routes.php')){
		include(APPS.'/'.CURRENT_APP.'/routes.php');

		//define default controller & action and unset them from array for route control
		$default_controller = isset($routes['default_controller']['controller']) ? $routes['default_controller']['controller'] : '' ;
		$default_action = isset($routes['default_controller']['action']) ? $routes['default_controller']['action'] : '';
		unset($routes['default_controller']);
		$options = null;

		if(DEBUG)
			$route = 'default_controller';

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

						if(DEBUG)
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
	}


	//if we have a controller and an action, let's rock!
	if(isset($controller) && isset($action)){
		include(APPS.'/'.CURRENT_APP.'/controllers/'.$controller.'.php');

		$theApp = new $controller($controller, $action);
		$theApp->$action($options);

	//else launch the default_controller and default_action
	}else if(isset($default_controller) && isset($default_action)){
		include(APPS.'/'.CURRENT_APP.'/controllers/'.$default_controller.'.php');

		$theApp = new $default_controller($default_controller, $default_action);
		$theApp->$default_action($options);
	}
	
	//check if we have a layout and render it if yes
	if($theApp->hasLayout()){
		$theApp->render();
	}
	
	if(DEBUG){
		$end = microtime() - $start;
		include(BASEPATH.'debug.php');
	}

	ob_end_flush();
?>