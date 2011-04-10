<?php
	class Home extends Controller{
		public $title = 'Test';

		public function index($options){

			//You can change the title on the fly
			$this->title = "Modification";
			
			//include a model for db query
			$articles = $this->includeModel('article');

			//get a view and send an article array var
			$this->getView('partial/home', 
				array('articles' => $articles->where('id', 9)->find_many())
			);
		}

		public function do404($options){
			$this->title = "404 Error";

			$this->getView('partial/404');
		}
	}
?>