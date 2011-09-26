<?php
class Index extends Controller{
    public $title = 'Congratulation';
    
    public function congrats(){
        //$test = $this->loadModel('test');
        
        $this->view->setLayout('index');
        $this->view->load('partial/home');
        //$model = $this->loadModel('essaiModel');
        //$test = $model->findMany();
        
        //$test->description = "Test d'édition";
        //$test->save();
        
        /*$test2 = $model->create(array(
            'nom' => 'autre test',
            'description' => 'test'
        ));
        
        $test2->save();*/
    }
    
    public function contactForm(){
        $this->view->loadView('partial/form');
        
        if(Validation::method() == 'POST'){
            $val = new Validation('JSON');
            
            $val->add('name')
                ->type('text')
                ->minLength(10)
                ->required();
            
            $val->add('mail')
                ->type('mail');
            
            $val->add('pass')
                ->type('password')
                ->required();
            
            var_dump($val->validate());
        }
    }

    public function showId($id){
    	echo "Current id is {$id}";
        $this->loadView('partial/home');
    }
    
    public function do404($url){
        echo $url." doesn't exists";
    }
}
?>