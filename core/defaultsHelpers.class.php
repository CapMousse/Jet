<?php

class Helpers{
	public function slugify($text){
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
		$text = trim($text, '-');

		if (function_exists('iconv'))
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		$text = strtolower($text);
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
			return 'n-a';

		return $text;
	}

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


	public function array_empty($mixed) {
		if (is_array($mixed)) {
			foreach ($mixed as $value) {
				if (!array_empty($value)) {
					return false;
				}
			}
		}
		elseif (!empty($mixed)) {
			return false;
		}
		return true;
	}


	public function email($expediteur, $destinataire, $sujet, $message)
	{
		//-----------------------------------------------
		//DECLARE LES VARIABLES
		//-----------------------------------------------
			
		$email_expediteur = $expediteur;
		$email_reply = $expediteur;
			
		$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head></head><body><p>'.$message.'</p></body></html>';
			
		$message_texte = strip_tags($message); 
		$message_html = $message;
			
		//-----------------------------------------------
		//GENERE LA FRONTIERE DU MAIL ENTRE TEXTE ET HTML
		//-----------------------------------------------
			
		$frontiere = '-----=' . md5(uniqid(mt_rand()));
			
		//-----------------------------------------------
		//HEADERS DU MAIL
		//-----------------------------------------------
			
		$headers = 'From: '.$email_expediteur."\n";
		$headers .= 'Return-Path: <'.$email_reply.'>'."\n";
		$headers .= 'MIME-Version: 1.0'."\n";
		$headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';
			
		//-----------------------------------------------
		//MESSAGE TEXTE
		//-----------------------------------------------
		$message = 'This is a multi-part message in MIME format.'."\n\n";
			
		$message .= '--'.$frontiere."\n";
		$message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n";
		$message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
		$message .= $message_texte."\n\n";
		
		//-----------------------------------------------
		//MESSAGE HTML
		//-----------------------------------------------
		$message .= '--'.$frontiere."\n";
		$message .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
		$message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
		$message .= $message_html."\n\n";
			
		$message .= '--'.$frontiere.'--'."\n";
		
		
		mail($destinataire, $sujet, $message, $headers);
	}

	public function strstrb($h,$n){
		return array_shift(explode($n,$h,2));
	}
	
	public function getCache(){
		$file = null;

		foreach($_GET as $name => $value)
			$file .= $value.'-';

		$file = rtrim($file, '-') == "" ? "index" : rtrim($file, '-');


		if(is_file('cache/'.$file.'.php')){
			if( filemtime('cache/'.$file.'.php') > time()-5*24*60*60){
				deleteCache();
				return false;
			}else{
				include('cache/'.$file.'.php');
				return true;
			}
		}else
			return false;
	}

	public function createCache(){
		$file = null;

		foreach($_GET as $name => $value)
			$file .= $value.'-';

		$file = rtrim($file, '-') == "" ? "default" : rtrim($file, '-');
		
		$content = ob_get_contents();

		$file = fopen('cache/'.$file.'.php', 'w+');
		fputs($file, $content);
		fclose($file);
	}

	public function deleteCache($file = null){
		if(is_null($file)){
			foreach($_GET as $name => $value)
				$file .= $value.'-';

			$file = rtrim($file, '-');
			
		}

		if(is_file('cache/'.$file.'.php')) return unlink('cache/'.$file.'.php');
	}
}

$helper = new Helpers();

?>