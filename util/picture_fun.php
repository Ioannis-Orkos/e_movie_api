<?php

class PicureFun{

 public $home_url="http://localhost/e_movie_api";
 public $form_name;
 public $file_path;

	// Check if image file is a actual image or fake image
	function picture_Check(){

			return (!(empty($_FILES[$this->form_name]["tmp_name"])));
	}


	// Check if image file is exists
	function picture_exists($file_name,$upload_to){
			$OK = 0;
			$target_file = $upload_to . $file_name.".".pathinfo($_FILES[$this->form_name]["name"],PATHINFO_EXTENSION);

	return (file_exists($target_file))?	$target_file:false;
	}




	// Check size limite
	function picture_size($size=1000000){
		return ($_FILES[$this->form_name]["size"] < $size);
	}



	// Check for image type
	function Check_picture_type(){

			$target_file =  basename($_FILES[$this->form_name]["name"]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

			return($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" );
	}

	// Check for image type
	function Check_picture_static_type($f_name){

			$target_file =  basename($_FILES[$f_name]["name"]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

			return($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" );
	}




	// upload image file and return
	function picture_upload($upload_to= "",$new_name=""){

		if(!is_dir($upload_to))	mkdir($upload_to);
		//if($this->picture_exists($new_name,$upload_to))  $this->picture_exists($new_name,$upload_to);

		if (empty($new_name)||empty($upload_to))  return false;
  //  if(!($this->picture_Check()&&$this->picture_size()&&$this->Check_picture_type())) return false;

		$target_file = $upload_to .$new_name.date("_sihdmy").".".pathinfo(basename($_FILES[$this->form_name]["name"]),PATHINFO_EXTENSION);

		if (is_dir($upload_to)||empty($upload_to)){

					  // Check if $uploadOK is set to 0 by  error

        		if(move_uploaded_file($_FILES[$this->form_name]["tmp_name"], $target_file))
                  {
                  //  $this->file_path=$this->home_url ."/".str_replace("../","", $target_file);
                    $this->file_path="/".str_replace("../","", $target_file);

                  	return true;
                  }

						}

	    return false;
		}


		// upload image file and return
		function picture_delete($file){
			if(is_file($file))	return  unlink($file);
		  return false;
			}





	function return_name($upload_to,$new_name){

				$target_file = $upload_to .$new_name.".".pathinfo(basename($_FILES[$this->form_name]["name"]),PATHINFO_EXTENSION);

	return $target_file;
	}
}
		?>
