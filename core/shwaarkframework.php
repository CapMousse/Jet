<?php	
/**
*	ShwaarkFramework
*	A lightwave and fast framework for developper who don't need hundred of files
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.1
*/

ob_start('ob_gzhandler');

// load framework user config file
require(BASEPATH.'config.inc.php');

// get the asked config environment
$config = $config[$environment];

// star the debug mode
$debug = array(
	'startRender' => microtime(),
	'endRender' => null,
	'layout' => null,
	'loadedViews' => array(),
	'loadedControllers' => array(),
	'loadedModels' => array(),
	'loadedModules' => array(),
	'route' => null
);

//create the Static constant, for statics files
define('STATICS', $config['statics']);

session_start();

/***********************************************/
/**** Include class framework and init them ****/
/***********************************************/

// load the abstract controler class, used to be extend by user controller
require(BASEPATH.'controller.class.php');

// load the view controler class used by templates
require(BASEPATH.'view.class.php');

// don't necesary load orm class if no sql needed
if($config['sql']){
	require(BASEPATH.'idiorm.class.php');
	require(BASEPATH.'paris.class.php');
	ORM::configure('mysql:host='.$config['host'].';dbname='.$config['base']);
	ORM::configure('username', $config['log']);
	ORM::configure('password', $config['pass']);
}

if($config['cache']){
	require(BASEPATH.'cache.class.php');
}

/**********************/
/**** Parse routes ****/
/**********************/

// get current adresse path
$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');

$uri_array = '';

// check if current path is not root url or core url, else return array of current route
if (trim($path, '/') != '' && $path != "/".SELF)
{
	$uri_array = explode('/', trim($path, '/'));
}

// check if uri_array is not empty and check if it contain a core config route
if(isset($uri_array[0])){
	if(array_key_exists('/'.$uri_array[0], $config['routes'])){
		$app = $config['routes']['/'.$uri_array[0]].'/';

		// check if whe have app routes and remove current route
		if(count($uri_array) > 1){
			unset($uri_array[0]);
			sort($uri_array);
		}else
			$uri_array = null;
	}
}

// define CURRENT_APP with asked app or default app
DEFINE('CURRENT_APP', isset($app) ? $app : $config['routes']['default'].'/');

// check and include route file app
if(is_file(APPS.CURRENT_APP.'routes.php')){
	include(APPS.CURRENT_APP.'routes.php');

	//define default controller & action and unset them from array for route control
	$default_controller = isset($routes['default']['controller']) ? $routes['default']['controller'] : '' ;
	$default_action = isset($routes['default']['action']) ? $routes['default']['action'] : '';
	unset($routes['default']);
	$options = null;

	$debug['route'] = 'default';

	// check if we have routes to parse
	if(isset($uri_array[0])){

		// impode current uri for control
		$uri = implode('/', $uri_array);

		// first, check if current raw uri look exactly to one route
		if(isset($routes[$uri])){
			$controller = $routes[$uri]['controller'];
			$action = $routes[$uri]['action'];
		}

		// second, check each routes
		if(!isset($controller) && !isset($action)){
			foreach ($routes as $key => $val){

				// for each route, replace the :any, :alpha and :num by regex for control
				$parsedKey = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', str_replace(':alpha', '([a-zA-Z]+)', $key)));

				// try if current uri look like the parsed route
				if (preg_match('#^'.$parsedKey.'$#', $uri, $array)){

					// remove the first element of array, '/'
					unset($array[0]);

					// resort the array to fill the empty space
					sort($array);

					//now, let's rock!
					$controller = $routes[$key]['controller'];
					$action = $routes[$key]['action'];
					$options = $array;

					if($config['debug'])
						$debug['route'] = $key;
				}
			}
		}

		// third, if no routes look like our uri, try the 404 route
		if(!isset($controller) && !isset($action)){
			if(isset($routes['404'])){
				$controller = $routes['404']['controller'];
				$action = $routes['404']['action'];
			}
		}
	}

	// if a controller already exists, keep it, else, load the default controller. Same for action
	$controller = isset($controller) ? $controller : $default_controller;
	$action = isset($action) ? $action : $default_action;
}


if(isset($controller) && isset($action)){
	// include the asked controller
	include(APPS.CURRENT_APP.'controllers/'.$controller.'.php');

	$debug['loadedControllers'][] = $controller;

	// create the asked controller
	$theApp = new $controller($controller, $action);

	// lauch the asked action, with our options
	$theApp->$action($options);

	// check if we our app need to be rendered
	if($theApp->hasLayout()){
		$theApp->render();
	}
}

$debug['endRender'] = microtime();


if($config['debug']){
	include(BASEPATH.'debug.php');
}

ob_end_flush();
