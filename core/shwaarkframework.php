<?php	
	/***** init cache ****/
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
	require(BASEPATH.'defaultsHelpers.class.php');

	//load the abstract controler class, used to be extend by user controller
	require(BASEPATH.'controller.class.php');

	//load the view controler class used by templates
	require(BASEPATH.'view.class.php');
	
	//block array, used to render partial
	$blocks = array();

	//get current adresse path
	$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
	$uri_array = '';

	if (trim($path, '/') != '' && $path != "/".SELF)
	{
		$uri_array = explode('/', trim($path, '/'));
	}

	if(is_array($uri_array)){
		if(array_key_exists('/'.$uri_array[0], $routesApp)){
			$app = $routesApp['/'.$uri_array[0]].'/';
			unset($uri_array[0]);
		}
	}

	DEFINE('CURRENT_APP', isset($app) ? $app : $routesApp['default'].'/');

	if(is_file(APPS.'/'.CURRENT_APP.'/routes.php')){
		include(APPS.'/'.CURRENT_APP.'/routes.php');
	}

	if($routes){
		$default_controller = isset($routes['default_controller']['controller']) ? $routes['default_controller']['controller'] : '' ;
		$default_action = isset($routes['default_controller']['action']) ? $routes['default_controller']['action'] : '';
		unset($routes['default_controller']);
		$options = null;

		if(DEBUG)
			$route = 'default_controller';

		if(is_array($uri_array)){
			$uri = implode('/', $uri_array);
	
			if(isset($routes[$uri])){
				$controller = $routes[$uri]['controller'];
				$action = $routes[$uri]['action'];
			}
	
			if(!isset($controller) && !isset($action)){
				foreach ($routes as $key => $val){
					$parsedKey = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', str_replace(':alpha', '([a-zA-Z]+)', $key)));


					if (preg_match('#^'.$parsedKey.'$#', $uri)){
						preg_match('#^'.$parsedKey.'$#', $uri, $array);
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

			if(!isset($controller) && !isset($action)){
				if(isset($routes['404'])){
					$controller = $routes['404']['controller'];
					$action = $routes['404']['action'];
				}
			}
		}
	}

	if(isset($controller) && isset($action)){
		include(APPS.'/'.CURRENT_APP.'/controllers/'.$controller.'.php');

		$theApp = new $controller();
		$theApp->$action($options);
	}else if(isset($default_controller) && isset($default_action)){
		include(APPS.'/'.CURRENT_APP.'/controllers/'.$default_controller.'.php');

		$theApp = new $default_controller();
		$theApp->$default_action($options);
	}else{
		$helper->setLayout(false);
	}
	
	if($theApp->hasLayout()){
		$views = new View();
		require(APPS.CURRENT_APP.'views/'.$theApp->template.'.php');
	}
	
	if(DEBUG){
		$end = microtime() - $start;
		include(BASEPATH.'debug.php');
	}

	ob_end_flush();
?>