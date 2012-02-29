<?php

//Define important constant
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('SYSPATH', __DIR__.'/../core/');
define('PROJECT', __DIR__.'/../project/');
define('APPS', PROJECT.'apps/');
define('APP', 0);
define('CONTROLLER', 1);
define('ACTION', 2);
define('DR', '/');
define('EXT', '.php');