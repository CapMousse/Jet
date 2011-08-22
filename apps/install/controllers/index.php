<?php
class Index extends Controller{
    public $title = 'Congratulation';
    
    public function congrats(){
        $this->loadView('partial/home');
    }

    public function showId($id){
    	echo "Current id is {$id}";
        $this->loadView('partial/home');
    }
}
?>