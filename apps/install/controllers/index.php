<?php
class Index extends Controller{
    public $title = 'Congratulation';
    
    public function congrats(){
        $this->loadView('partial/home');
    }
}
?>