<?php

/**
*   Jet
*   A lightweight and fast framework for developer who don't need hundred of files
*
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/

$args = $argv;

//Check if an argument is called
if(count($args) < 2){
    print "For help, use the help argument\n";
    exit(0);
}

//Init all constant for the script
define('TYPE', 0);
define('LENGTH', 1);
define('DEFAULTVALUE', 2);
define('ENCODE', 3);
define('ATTR', 4);
define('ISNULL', 5);
define('INDEX', 6);


//Get the first argument name and his option
$arguments = explode(':', $args[1]);
$argumentType = $arguments[0];
$argumentOptions = $arguments[1];


//include all core files needed to launch the orm
include(PROJECT.'config.php');
include(SYSPATH.'orm/connector.php');
include(SYSPATH.'orm/wrapper.php');
include(SYSPATH.'jet.php');
include(SYSPATH.'model.php');

$config = new Config();

//Get an instance of the Jet core
$jet = Jet::getInstance();

/** @var $environment String */
$jet->setEnvironment($config->environment);
/** @var $config Array */
$jet->setConfig($config);


//Check called argument
switch($argumentType){
    default:
        print "Command $argumentType doesn't exists\nHere is a list of authorized arguments\n\n";
    case 'help':
        print "the Jet cli tools provide some simple command to create/destroy apps and create/remove tables from your database : \n";
        print "\t- help : list all available command \n";
        print "\t- app:create name:name : Create the named app with controllers/models/views dir and a config file\n";
        print "\t- app:remove name:name : remove an APP and all his dir and remove tables\n";
        print "\t- db:create : create all tables \n";
        print "\t\t- db:create app:name : create all tables from the selected app\n";
        print "\t\t- db:create model:name : create all tables from the model app\n";
        print "\t- db:migrate : migrate to last version all tables \n";
        print "\t\t- db:migrate app:name : migrate to last version the selected app\n";
        print "\t\t- db:migrate model:name : migrate to last version the model app\n";
        print "\t- db:load : load fake datas in named tables from the fixtures.php file \n";
        print "\t\tFixtures data don't have to fill the id column\n";
        print "\t- db:empty : remove all tables \n";
        print "\t\t- db:empty app:name : remove all tables from the selected app\n";
        print "\t\t- db:empty model:name : remove all tables from the selected app\n";
        print "\t- env:name : set the current environment name\n\n";
        print "Please, note that the db argument only work on mysql databases\n\n";
    break;

    case 'env':
        $configFile = file(PROJECT.'config.php');

        foreach($configFile as $number => $line){
            if(preg_match('#environment = \".+\"#i', $line)){
                $configFile[$number] = preg_replace('#environment = \".+\"#i', 'environment = "'.$argumentOptions.'"', $line);
            }
        }

        file_put_contents(PROJECT.'config.php', implode("", $configFile));
        print "Change environment to $argumentOptions\n";
    break;

    case 'app':

        //Check if a app name exists
        if(!isset($args[2])){
            print 'Missing app argument';
            exit(0);
        }

        $appName = explode(':', $args[2]);

        //Check if the app name is not empty
        if(count($appName) != 2){
            print 'Missing app name';
            exit(0);
        }

        //Launch the AppManager class to manage asked option
        new AppManager($argumentOptions, array_pop($appName));
    break;

    case 'db':
        $appName = null;
        $type = null;
        $name = null;

        //Check if a app name is called
        if(isset($args[2])){
            $options = explode(':', $args[2]);

            //Check if the app name is not empty
            if(count($options) != 2){
                print 'Missing app name';
                exit(0);
            }

            //Get the app name
            $type = $options[0];
            $name = $options[1];
        }

        //Launch the TableManager class to manage asked option
        new TableManager($argumentOptions, $type, $name);
    break;
}
