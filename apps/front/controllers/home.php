<?php
	class Home extends Controller{
		public $title = 'Test';

		public function __construct(){
			//must add this line if you create your own __construct method
			parent::__construct();

			//load the Users Manager module
			//you can access to the module with $this->UserManager
			$this->loadModule('UsersManager');			
		}

		public function index($options){

			//You can change the title on the fly
			$this->title = "Modification";
			
			//include a model for db query
			$articles = $this->loadModel('article');

			//get a view and send an article array var
			$this->loadView('partial/home', 
				array('articles' => $articles->where('id', 9)->find_many())
			);
		}

		public function do404($options){
			$this->title = "404 Error";

			$this->loadView('partial/404');
		}

		public function adminPanel($options){
			if(!$this->UsersManager->checkAuth('articles')) $this->do404();
		}
	}
?>