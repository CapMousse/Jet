<?php
class View{
	public $titleView = '';

	public function createBlock($block){
		global $blocks;

		if(!isset($blocks[$block]))
			$blocks[$block] = null;
		ob_start();
	}

	public function endBlock($block){
		global $blocks;

		$value = ob_get_clean();
		
		$blocks[$block] .= $value;
	}

	public function getBlock($block){
		global $blocks;
		
		return isset($blocks[$block]) ? $blocks[$block] : null;
	}

	public function getVar($var){
		global $theApp;

		return $theApp->$var;
	}
}
?>