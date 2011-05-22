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

/**
*	Debug class
*	Do you need to log something?
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.2
*/
class debug{
	private static $markers = array();
	public static $start;

        /**
	 * log
	 *
	 * add a log data to the log pile
	 *
	 * @access	static method
	 * @param	string	$data		data you want to log
	 * @return	void 
	 */	
	public static function log($data){
		$bk = debug_backtrace();
		
		$caller = substr($bk[1]['file'], strrpos($bk[1]['file'], "/") + 1);
		$line = $bk[1]['line'];
		$time = microtime() - self::$start;

		self::$markers[] = $data.' '.$caller.' at line '.$line.' ('.$time.')';
	}

        /**
	 * displayLog
	 *
	 * show log pile
	 *
	 * @access	static method
	 * @return	void 
	 */
	public static function displayLog(){
		echo '<h1>DEBUG</h1><ul>';
		foreach(self::$markers as $marker){
			echo '<li>'.$marker.'</li>';
		}
		echo '</ul>';
	}
}
?>