<?php
final class debug{
	protected static $instance;
	private static $markers = array();
	public static $start;

	protected function __construct(){}
	protected function __clone(){}

	public static function log($data){
		$bk = debug_backtrace();
		
		$caller = substr($bk[1]['file'], strrpos($bk[1]['file'], "/") + 1);
		$line = $bk[1]['line'];
		$time = microtime() - self::$start;

		self::$markers[] = $data.' '.$caller.' at line '.$line.' ('.$time.')';
	}

	public static function displayLog(){
		echo '<h1>DEBUG</h1><ul>';
		foreach(self::$markers as $marker){
			echo '<li>'.$marker.'</li>';
		}
		echo '</ul>';
	}
}
?>