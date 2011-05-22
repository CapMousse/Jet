<?php
/**
*	ShwaarkFramework
*	A lightwave and fast framework for developper who don't need hundred of files
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.1
*/

/**
*	Cache class
*	Do you have memory?
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 0.1
*/

/**
 *      The UsersManager class need a users table with 
 *  id 		: type int
 * login	: varchar 255
 * password	: varchar 255
 * auth		: int 6
 */

class UsersManager{
    //private $private_key = "a9d57ffb29775e93a7652a3bdf0c6939b99f71cb";
    public $auth = array(
        'articles'  => 0x01,
        'gallery'   => 0x02 
    );

    /**
     * connexion
     *
     * connect a user
     *
     * @access      public
     * @param       string	$login	
     * @param       string      $password
     * @return      mixed 	false or data
     */	
    public function connexion($login, $password){
        $password = sha1(stripcslashes($password));
        $login = stripcslashes(htmlspecialchars($login));

        //factory the Users models. Module don't automaticly factiry included models
        $userModel = Model::factory('Users');
        $user = $userModel->where('password', $password)->where('login', $login)->find_one();
        unset($userModel);

        if($user){
            $_SESSION['auth'] = $user->auth;
            return true;
        }
    }

    /**
     * disconnexion
     *
     * disconnect a user
     *
     * @access      public
     * @return      mixed 	false or data
     */	
    public function disconnexion(){
        $_SESSION = null;
        unset($_SESSION);

        return true;
    }

    /**
     * createUser
     *
     * create a user
     *
     * @access      public
     * @param       string      $login
     * @param       string      $password
     * @param       binary      $auth
     * @return      mixed 	false or data
     */	
    public function createUser($login, $password, $auth){
        $usersModel = Model::factory('Users');
        $data = array(
            'id' => 'NULL',
            'login' => stripcslashes($login), 
            'password' => sha1(stripcslashes($password)), 
            'auth' => $this->auth[$auth]);

        $newUser = $usersModel->create($data);

        return $newUser->save();
    }

    /**
     * deleteUser
     *
     * @access      public
     * @param       int         $id
     * @return      mixed 	false or data
     */	
    public function deleteUser($id){
        $usersModel = Model::factory('Users');
        $user = $usersModel->find_one($id);

        return $user->delete();
    }

    /**
     * updatePassword
     *
     * @access      public
     * @param       int         $id
     * @param       string      $password
     * @return      mixed 	false or data
     */	
    public function updatePassword($id, $password){
        $usersModel = Model::factory('Users');
        $user = $usersModel->find_one($id);

        $user->set('password', sha1(stripcslashes($password)));

        return $user->save();		
    }

    /**
     * addAuth
     *
     * @access      public
     * @param       int         $id
     * @param       binary      $auth
     * @return      mixed 	false or data
     */
    public function addAuth($id, $auth){
        $usersModel = Model::factory('Users');
        $user = $usersModel->find_one($id);

        $currentAuth = (int)$user->auth;
        $currentAuth |= $this->auth[$auth];

        $user->set('auth', $currentAuth);

        return $user->save();
    }

    /**
     * removeAuth
     *
     * @access      public
     * @param       int         $id
     * @param       binary      $auth
     * @return      mixed 	false or data
     */
    public function removeAuth($id, $auth){
        $usersModel = Model::factory('Users');
        $user = $usersModel->find_one($id);

        $currentAuth = (int)$user->auth;
        $currentAuth &= ~$this->auth[$auth];

        $user->set('auth', $currentAuth);

        return $user->save();
    }

    /**
     * checkAuth
     *
     * @access      public
     * @param       int         $id
     * @param       binary      $auth
     * @return      mixed 	false or data
     */
    public function checkAuth($id, $auth = null){
        if(is_null($auth)){
            if(!isset($_SESSION['auth'])) return false;
            if((int)$_SESSION['auth'] & $this->auth[$id]) return true;
        }else{
            $usersModel = Model::factory('Users');
            $user = $usersModel->find_one($id);

            if((int)$user->auth & $this->auth[$auth]) return true;
        }

        return false;
    }
}

?>