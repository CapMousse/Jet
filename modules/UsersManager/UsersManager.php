<?php

//The UsersManager class need a users table with:
// id 		: type int
// login	: varchar 255
// password	: varchar 255
// auth		: int 6

class UsersManager{
	//private $private_key = "a9d57ffb29775e93a7652a3bdf0c6939b99f71cb";
	public $auth = array(
		'articles' 	=> 0x01,
		'gallery' 	=> 0x02 
	);

	public function connexion($login, $password){
		$password = sha1(stripcslashes($password));
		$login = stripcslashes(htmlspecialchars($login));

		$userModel = Model::factory('Users');
		$user = $userModel->where('password', $password)->where('login', $login)->find_one();
		unset($userModel);
		
		if($user){
			$_SESSION['auth'] = $user->auth;
			return true;
		}
	}

	public function disconnexion(){
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
			'auth' => $this->auth[$auth]);
		
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

	public function addAuth($id, $auth){
		$usersModel = Model::factory('Users');
		$user = $usersModel->find_one($id);

		$currentAuth = (int)$user->auth;
		$currentAuth |= $this->auth[$auth];

		$user->set('auth', $currentAuth);

		return $user->save();
	}

	public function removeAuth($id, $auth){
		$usersModel = Model::factory('Users');
		$user = $usersModel->find_one($id);

		$currentAuth = (int)$user->auth;
		$currentAuth &= ~$this->auth[$auth];

		$user->set('auth', $currentAuth);

		return $user->save();
	}

	public function checkAuth($id, $auth = null){
		if(is_null($auth)){
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