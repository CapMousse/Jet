<?php

/**
*	ShwaarkFramework
*	A lightweigth and fast framework for developper who don't need hundred of files
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.1
*/

/**
*	Controller abstract class
*	the controller model
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.2
*/
abstract class Controller{
    public 
        $layout = true,
        $template = "index",
        $title = "";

    private 
        $view,
        $models = array(),
        $debug = null;

    protected
        $cache;

    //if you want to made your own __construct, add parent::__construct() to your code
    public function __construct(){
        debug::log('Layout set to : '.$this->template);

        if(Shwaark::$config['cache'])
            $this->cache = new Cache();

        //enable view model for template control
        $this->view = new View();
    }

    /**
     * loadView
     *
     * load the asked view. Important for display data... or not
     *
     * @access	protected
     * @param	string	$file		name of the view file
     * @param 	array 	$options 	data used by the view
     * @return	void 
     */	
    protected function loadView($file, $options = null){
        //Control if options is defined, if yes, construct all var used in templates
        if(is_array($options))
            foreach($options as $name => $value){ $$name = $value; }

        if(is_file(APPS.Shwaark::$app.'views/'.$file.'.php')){
            $file = APPS.Shwaark::$app.'views/'.$file.'.php';
        }else if(is_file(VIEWS.$file.'.php')){            
            $file = VIEWS.$file.'.php';
        }else{
            debug::log("The asked view <b>$file</b> doesn't exists in <b>".get_class($this).".php</b>");
            return;
        }
        
        include($file);
        debug::log('Loaded view : '.$file);
    }

    /**
     * loadModel
     *
     * load the asked model. 
     * Y U R SO AHRD WIHT MEH?
     *
     * @access	protected
     * @param	string	$file		name of the model file
     * @param 	bool 	$factoring 	do your want to return a factory model? - default true
     * @return	false/Model Name/Factory model 
     */	
    protected function loadModel($file, $factoring = true){

        //Control if model file exists
        if(!isset($this->models[$file])){
            if(!is_file(APPS.CURRENT_APP.'models/'.$file.'.php')){
                trigger_error("The asked model <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />");
                return false;
            }

            include(APPS.CURRENT_APP.'models/'.$file.'.php');
            $file = ucfirst($file);		
            $this->models[$file] = $file;	
        }

        debug::log('Model loaded : '.$file);

        //return the intentiate model
        if($factoring)
            return Model::factory($this->models[$file]);
        else
            return $this->models[$file];
    }

    /**
     * loadController
     *
     * load the asked model. 
     * We need to go deeper.
     *
     * @access	protected
     * @param	string	$file		name of the controller file
     * @return	false/object
     */	
    protected function loadController($file){
        if(!is_file(APPS.CURRENT_APP.'controllers/'.$file.'.php')){
            trigger_error("The asked controller <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />");
            return false;
        }

        include(APPS.CURRENT_APP.'controllers/'.$file.'.php');

        debug::log('Controller loaded : '.$file);

        $controller = ucfirst($file);
        return new $controller();
    }

    /**
     * loadModule
     *
     * load the asked module with all attached files. 
     *
     * @access	protected
     * @param	array/string	$names		names of all wanted modules
     * @return	void
     */	
    protected function loadModule($name){
        //check if we have a array of name or convert it to array
        if(!is_string($name)) return;
        
        //check if module and module conf exists
        if(is_dir(MODULES.$name)){

            //include all nececary files
            foreach(glob(MODULES.$name.'/*.php') as $file)
                include($file);
            
            $name = ucfirst($name);
            $this->{$name} = new $name();

            debug::log('Module loaded : '.$name);
        }
    }

    /**
     * setLayout
     *
     * @access	public
     * @param	bool 	$bool
     * @return	void
     */	
    public function setLayout($bool){
        $this->layout = $bool;
    }

    /**
     * hasLayout
     *
     * @access	public
     * @return	bool
     */
    public function hasLayout(){
        return $this->layout;
    }

    /**
     * render
     *
     * @access	public
     * @return	void
     */
    public function render(){
        if($this->hasLayout())
            $this->loadView($this->template);
    }
}
?>