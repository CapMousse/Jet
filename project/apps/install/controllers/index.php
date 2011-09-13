<?php
class Index extends Controller{
    public $title = 'Congratulation';
    
    public function congrats(){
        $this->loadView('partial/home');
        $model = $this->loadModel('essaiModel');
        $test = $model->join('left', 'a', array('b = c' , 'd = e' ))->findOne(2);
        
        //$test->description = "Test d'édition";
        //$test->save();
        
        /*$test2 = $model->create(array(
            'nom' => 'autre test',
            'description' => 'test'
        ));
        
        $test2->save();*/
    }
    
    public function contactForm(){
        $this->loadView('partial/form');
        
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
}
?>