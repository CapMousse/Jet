<?php
	// Controller class

	abstract class Controller{

		//$layout var true if controller need a layout to work
		public $layout = true;

		//define the used view for layout, by default index
		public $template = "index";

		//used by isAction() view method
		private $action = "";

		//used by isController view method
		private $name = "";

		//the view object
		private $view;

		public function __construct($name, $action){

			$this->name = $name;
			$this->action = $action;

			//enable view model for template control
			$this->view = new View();
		}

		protected function getView($file, $options = null){

			//Control if options is defined, if yes, construct all var used in templates
			if(is_array($options))
				foreach($options as $name => $value){ $$name = $value; }
				
			//Control if view file exists
			if(!is_file(APPS.CURRENT_APP.'views/'.$file.'.php')){
				trigger_error("The asked view <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />");
				exit();
			}

			include(APPS.CURRENT_APP.'views/'.$file.'.php');
		}

		protected function includeModel($file){

			//Control if model file exists
			if(!is_file(APPS.CURRENT_APP.'models/'.$file.'.php')){
				trigger_error("The asked model <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />");
				exit();
			}
			
			include(APPS.CURRENT_APP.'models/'.$file.'.php');

			//Init model
			$file = ucfirst($file);
			return Model::factory($file);
		}

		//Set the layout property
		public function setLayout($bool){
			$this->layout = $bool;
		}

		//Return the layout value (used for render)
		public function hasLayout(){
			return $this->layout;
		}

		public function isAction($action){
			return $action == $this->action ? true : false;
		}

		public function isController($controller){			
			return $controller == $this->name ? true : false;
		}

		//Include the asked layout and launch the render
		public function render(){
			require(APPS.CURRENT_APP.'views/'.$this->template.'.php');
		}
	}
?>