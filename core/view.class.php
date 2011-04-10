<?php
	//View class
	class View{

		//Lauch the render of a block
		public function createBlock($block){
			global $blocks;

			if(!isset($blocks[$block]))
				$blocks[$block] = null;
			ob_start();
		}

		//End the render of a block and stock it
		public function endBlock($block){
			global $blocks;

			$value = ob_get_clean();
			
			$blocks[$block] .= $value;
		}

		//Get the rendered block in the template
		public function getBlock($block){
			global $blocks;
			
			return isset($blocks[$block]) ? $blocks[$block] : null;
		}

		//Get a simple var (like titleVew) in the template
		public function getVar($var){
			global $theApp;

			return $theApp->$var;
		}
}
?>