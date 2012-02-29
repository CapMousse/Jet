<?php
class Index extends Controller{
    public $title = 'Congratulation';
    
    public function congrats(){
        
        $this->view->setLayout('index');
        $this->view->load('partial/home');
    }
    
    public function contactForm(){
        $this->view->setLayout('index');
        $this->view->load('partial/form');
        
        if(Validation::method() == 'POST'){
            $val = new Validation();
            
            $val->add('name')
                ->type('text')
                ->minLength(10)
                ->required();
            
            $val->add('mail')
                ->type('mail')
                ->required();
            
            var_dump($val->validate());
        }
    }

    public function showId($id){
    	echo "Current id is {$id}";

        $this->view->setLayout('index');
        $this->view->load('partial/home');
    }
    
    public function do404($url){

        $this->view->setLayout('index');

        $this->view->beginBlock('content');
        echo $url." doesn't exists";
        $this->view->endBlock();
    }

    public function showExample($id = null){
        $userModel = $this->model->load('user');

        $example = $userModel->findOne($id);

        var_dump($example);
    }
}
?>