<?php

namespace Devvime\KiichiPhpEngine;

class FileService {

    public function upload($img, $folder = UPLOAD_DIR)
	{
		if (isset($img['name']) && $img["error"] == 0) {
		    $file_temp = $img['tmp_name'];
		    $name = $img['name'];
		    $extension = strrchr($name, '.');
		    $extension = strtolower($extension);
		    if (strstr('.jpg;.jpeg;.gif;.png', $extension)) {
		        $new_name = uniqid() . '-'.str_replace(" ","-",$name);
		        $destiny = $folder . $new_name;
		        move_uploaded_file( $file_temp, $destiny);
		    }else {
		        echo json_encode(["error"=>"Você poderá enviar apenas arquivos \'*.jpg;*.jpeg;*.gif;*.png\'"]);
		        exit;
		    }
		}else {
	        echo json_encode(["error"=>"Você não enviou nenhum arquivo!"]);
	        exit;
		} 
		return $new_name;		
	}

	public function uploadBase64($folder = UPLOAD_DIR)
	{
		define('UPLOAD_DIR', $folder);
		$img = $_POST['image'];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '-', $img);
		$data = base64_decode($img);
		$file = UPLOAD_DIR . uniqid() . '.png';
		$success = file_put_contents($file, $data);
		echo $success ? $file : 'Unable to save the file.';
		return $file;
	}

}