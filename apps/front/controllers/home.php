<?php
	class Home extends Controller{
		public $title = 'Essai';

		public function index($options){
			$this->title = "Modification";
			
			$tableau = array();
			for($i = 0; $i < 10; $i++){
				$tableau[$i] = $i;
			}

			$articles = $this->includeModel('article');

			print_r($articles->where('id', 9)->find_many());
			
			$this->getView('partial/home', array('tableau' => $tableau));
		}

		public function testAny($options){
			print_r($options);
		}

		public function do404($options){
			$this->title = "Erreur 404 LOLWTFNOOB";

			$this->getView('partial/404');
		}
	}
?>