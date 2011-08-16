<?php

//  The UsersManager class need a users table with:
//  id          : type int
//  login       : varchar 255
//  password    : varchar 255
//  auth        : int 6

class TimetofocusUsersManager{

    public function login($login, $password){
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

    public function logout(){
        $_SESSION = null;
        unset($_SESSION);

        return true;
    }

    public function createUser($login, $password, $auth){
        $usersModel = Model::factory('Users');
        $data = array(
            'id' => 'NULL',
            'login' => stripcslashes($login), 
            'password' => sha1(stripcslashes($password)), 
            'api_key' => sha1(time())
        );
        
        $newUser = $usersModel->create($data);

        return $newUser->save();
    }

    public function deleteUser($id){
        $usersModel = Model::factory('Users');
        $user = $usersModel->find_one($id);

        return $user->delete();
    }

    public function updatePassword($id, $password){
        $usersModel = Model::factory('Users');
        $user = $usersModel->find_one($id);
        
        $user->set('password', sha1(stripcslashes($password)));
        
        return $user->save();        
    }
}

?>