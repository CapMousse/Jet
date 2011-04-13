<?php

class Helpers{

	//upload file to specific location
	public function upload($index,$destination,$maxsize=FALSE,$extensions=FALSE)
	{
		//Test1: verify if no errors
		if (!isset($_FILES[$index]) OR $_FILES[$index]['error'] > 0) return FALSE;
		
		//Test2: is dest exists?
		if(!is_dir($destination)) mkdir($destination);
		
		//Test2: test file size limit
		if ($maxsize !== FALSE AND $_FILES[$index]['size'] > $maxsize) return FALSE;
		
		//Test3: extension test
		$ext = strtolower(substr(strrchr($_FILES[$index]['name'],'.'),1));
		if ($extensions !== FALSE AND !in_array($ext,$extensions)) return FALSE;
		 
		$nom = time().'_'.slugify($_FILES[$index]['name']);
		$destination = $destination.$nom;
		//Déplacement
		if(!move_uploaded_file($_FILES[$index]['tmp_name'],$destination)) return false;
		
		return $destination;
	}	
}

$helper = new Helpers();

?>