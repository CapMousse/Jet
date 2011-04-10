<?php
	abstract class Controller{
		public $layout = true;
		public $template = "index";
		public $view;

		protected $model;

		public function __construct(){
			$this->view = new View();
		}

		protected function getView($file, $options = null){
			if(is_array($options))
				foreach($options as $name => $value){ $$name = $value; }
				
			if(!is_file(APPS.CURRENT_APP.'views/'.$file.'.php')){
				trigger_error("The asked view <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />");
				exit();
			}

			include(APPS.CURRENT_APP.'views/'.$file.'.php');
		}

		protected function includeModel($file){
			if(!is_file(APPS.CURRENT_APP.'models/'.$file.'.php')){
				trigger_error("The asked model <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />");
				exit();
			}
			
			include(APPS.CURRENT_APP.'models/'.$file.'.php');

			$file = ucfirst($file);
			return Model::factory($file);
		}

		public function setLayout($bool){
			$this->layout = $bool;
		}

		public function hasLayout(){
			return $this->layout;
		}
	}
?>