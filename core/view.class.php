<?php
	//View class
	class View{
		private $blocks = array();

		//Lauch the render of a block
		//@param block string
		//@return void
		public function createBlock($block){

			if(!isset($this->blocks[$block]))
				$this->blocks[$block] = null;

			ob_start();
		}

		//End the render of a block and stock it
		//@param block string
		//@return void
		public function endBlock($block){

			$value = ob_get_clean();
			
			$this->blocks[$block] .= $value;
		}

		//Get the rendered block in the template
		//@param block string
		//@return string
		public function getBlock($block){
			return isset($this->blocks[$block]) ? $this->blocks[$block] : null;
		}

		//Control is the asked block exists
		//@param block string
		//@return bool
		public function issetBlock($block){
			return isset($this->blocks[$block]) ? true : false;
		}

		public function destroyBlock($block){
			unset($this->blocks[$block]);
		}

		//Get a simple var (like titleVew) in the template
		//@param block string
		//@return ?
		public function getVar($var){
			global $theApp;

			return $theApp->$var;
		}
}
?>