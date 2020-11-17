<?php 

require_once "connection.class.php";
require_once "auth.class.php";

/**
 * Class User 
 el token debe de llegar en las cabeceras como "Bearer TOKEN"
 */

class User{
	private $connection;
	private $USER_ID;
	public $DATA_TOKEN;
	
	function User(){
		//connection->getBD()
		$this->connection = new Connection("pictunex");
		$this->USER_ID = null;
		$this->DATA_TOKEN = null;
	}

	public function NewUser($_user, $CLIENT_ID) {

		$user = (array) json_decode(base64_decode($_user));
		
		$firstName = $user["firstName"];
		$lastName = $user["lastName"];
		$userName = $user["userName"];
		$passWord = $user["passWord"];

		if($CLIENT_ID !== ENV::get("CLIENT_ID")){
			return false;
		}

		if( isset($firstName) && isset($lastName)
			&& isset($userName) && isset($passWord) ){

        	$firstName = escSpecialChar($firstName);
        	$lastName = escSpecialChar($lastName);
        	$userName = escSpecialChar($userName);
        	$user_id  = hash("sha256", $userName . time());
			
			try {

				if(self::existUser($userName)){
					return null;
				}

				$PWH = password_hash($passWord,PASSWORD_DEFAULT, ["coste" => 12]);

				$_query = "INSERT INTO `users`(
							`first_name`,
							`last_name`,
							`username`,
							`password`,
							`user_id`
						)
						VALUES(
							:first_name,
							:last_name,
							:username,
							:password,
							:user_id
						)";


				$_result = $this->connection->getDB()->prepare($_query);

				if( $_result->execute([":first_name" => $firstName,
									":last_name" => $lastName,
									":username" => $userName,
									":password" => $PWH,
									":user_id" => $user_id]) ){

					if($_result->rowCount()==1)
						return true;
					else return false;
				}// end <- if(execute)

			} catch (Exception $e) {
				errorServer();
				return false;
			}
		}else{
			return false;
		}
	} // end <- NewUser


	// $base64_user = base64_encode( username:password )
	public function Authentication($base64_user) {
		/*
		USER =  entonces el usuario => base64("USUARIO")
		PASSWORD = la contraseña => base64(sha256("PASSWORD"))

		AUTENTICATION: "USER:PASSWORD";

		"user_id" => hash_hmac("sha256",base64_encode("pedroSotto"),"PICTUNEX")
		*/

		$decodeB64 = base64_decode($base64_user);
		list($username, $password) = explode(":", $decodeB64);


		// var_dump("<", $password);
		$_ACCESS = self::queryUser($username, $password);
		if( $_ACCESS!=false && !empty($_ACCESS)){

			$TOKEN = Auth::SignIn("",$_ACCESS);
			return $TOKEN;
		}else{
			return false;
		}
	}

	public function queryUser($username,$password) {

		try {
			$table = ENV::get("USERTABLE");
			$_query = "SELECT * FROM $table WHERE username = :USERNAME"; 
			
	        $result = $this->connection->getDB()->prepare($_query); 

	        if($result->execute([":USERNAME" => $username])) {
	        	$user = $result->fetchAll(PDO::FETCH_ASSOC);
	        	
						$PW = $user[0]["password"]; // flag recupero PS

	        	if( password_verify($password, $PW) === true ){

	        		if(isset($user[0]["user_id"]))
	        			return $user[0]["user_id"];
		        	else
						return false;
								
	        	}else {
	        		return false;
	        	}
	        	
	        }else{
	        	return false;
	        }
		} catch (Exception $e) {
			errorServer();
			die();
		}
            // return $result->rowCount(); // equivalente a num_rows
	}

	//flag 
	//Al invocar $user = validateUSER_ID("9d89j2934ed92j3ed92d")
	//if( is_array($user) ){} esto en la clase API
	public function validateUSER_ID($user_id) {

		$table = ENV::get("USERTABLE");
		$_query = "SELECT `first_name`, `last_name`, `username`, `user_id`, `ico` FROM $table WHERE user_id = :USERID"; 
		try {
			
			$result = $this->connection->getDB()->prepare($_query);  

			if($result->execute([":USERID" => $user_id])) {
				//return user DB
				$USER = $result->fetch(PDO::FETCH_ASSOC);
				if($USER["ico"]!=null)
					self::constructUrl($USER["ico"]);	
				return $USER;
			}else{
				return false;
			}
		} catch (Exception $e) {
			errorServer();
			return false;
		}
	}

	public function Authorization($Token) {
		list($type, $token) = explode(" ", $Token);

		if($type !== "Bearer"){
			return false;
		}

		try {

			$token_dec = Auth::Check($token);
			
			if($token_dec!=null && $token_dec!=false && gettype($token_dec)==="object"){

				if(isset($token_dec->user_id))
					$this->USER_ID = $token_dec->user_id;
				else
					return false;

				//Authorized return data, storage user_id in the class
				if(isset($token_dec->data)){
					$this->DATA_TOKEN = $token_dec->data;
					return true;
				}else return "";

			}else if($token_dec==1)
				return 1;
			
		} catch (Exception $e) {
			errorServer();
		}
		return false;
	}

	public function existUser($user_name)
	{
		$table = ENV::get("USERTABLE");
		$_query = "SELECT * FROM $table WHERE `username` = :USERNAME";

        $result = $this->connection->getDB()->prepare($_query);  
        
        if( $result->execute([":USERNAME" => $user_name]) ) {
        	
        	if( count($result->fetchAll(PDO::FETCH_ASSOC)) >= 1 ){
        		return true;
        	}else
        		return false;
        }else{
        	return false;
        }
	}

	public function updateUser($TOKEN, $objUser, $img_avatar_user) {

        $user = (array) json_decode(base64_decode($objUser));
        
        $firstName = $user["firstName"];
				$lastName = $user["lastName"];

        //verify if the user wants change password
        if($user["passWord"] !== "0" 
        	&& $user["passWord"] != null && isset($user["passWord"])){

	        $passWord = $user["passWord"];
	    	}else $passWord = null;

        if($user["newPassword"] !== "0"
		&& $user["newPassword"] != null && isset($user["newPassword"])){

			$newPassword = $user["newPassword"];
		}else $newPassword = null;

        // get the userName
				$userName = $user["userName"] ?? null;
				
        try {
		if( isset($firstName) && isset($lastName)
          && $userName != null  && isset($userName) ){

            $firstName = escSpecialChar($firstName);
            $lastName = escSpecialChar($lastName);

            if(self::Authorization($TOKEN) === true){

              $query = "";

              // if change password is true Update F|L|P|I
              if($passWord!=null && $newPassword!=null){

              	$is_true_auth = self::queryUser($userName, $passWord);
                if($is_true_auth!=null && $is_true_auth!=false){

									
                  if($img_avatar_user === NULL || $img_avatar_user == 0){
                      
                    $query = "UPDATE `users` SET 
                            `first_name`=:firstName,
                            `last_name`=:lastName,
                            `password`=:newPass
                            WHERE `user_id`=:userdId";


                    $PWH = password_hash($newPassword,PASSWORD_DEFAULT, ["coste" => 12]);   		

                    $_result = $this->connection->getDB()->prepare($query);

                    if( $_result->execute([
                            ":firstName" => $firstName,
                            ":lastName" => $lastName,
                            ":newPass" => $PWH,
														":userdId" => $this->USER_ID]) ){

                      if($_result->rowCount()==1)
                        return true;
                      else return false;
                    }// end <- if(execute)

                  }else{

                    $query = "UPDATE `users` SET 
                            `first_name`=:firstName,
                            `last_name`=:lastName,
                            `password`=:newPass,
                            `ico`=:ico
                            WHERE `user_id`=:userdId";

                    $PWH = password_hash($newPassword,PASSWORD_DEFAULT, ["coste" => 12]);   		

                    $fileDel = self::destroyImages();

                    if($fileDel == 0 || $fileDel == true){

                      $urlForSave = self::saveImage($img_avatar_user);
                      if($urlForSave != false){

                        $_result = $this->connection->getDB()->prepare($query);

                        if( $_result->execute([
                              ":firstName" => $firstName,
                              ":lastName" => $lastName,
                              ":newPass" => $PWH,
                              ":ico" => $urlForSave,
                              ":userdId" => $this->USER_ID]) ){

                          if($_result->rowCount()>=1)
                              return true;
                          else return false;
                        }// end <- if(execute)
                      }
                    }	# end fileDel
                  } # end <- not imageFile
                }else Unauthorized();#end queryUser()
              } # end verify password


              // if only update F|L|I
              else if(self::existUser($userName) === true){

                if($img_avatar_user === NULL || $img_avatar_user == 0){


                  $query = "UPDATE `users` SET 
                  `first_name`=:firstName,
                  `last_name`=:lastName
                  WHERE `user_id`=:userdId";        		

                  $_result = $this->connection->getDB()->prepare($query);

                  if( $_result->execute([
                          ":firstName" => $firstName,
                          ":lastName" => $lastName,
                          ":userdId" => $this->USER_ID]) ){

                    if($_result->rowCount()>=0)
                      return true;
                    else return false;
                  }// end <- if(execute)

                }else{

                  $query = "UPDATE `users` SET 
                  `first_name`=:firstName,
                  `last_name`=:lastName,
                  `ico`=:ico 
                  WHERE `user_id`=:userdId";

									$fileDel = self::destroyImages();

                  // if the image was delete
                  if($fileDel == 0 || $fileDel == true){

										$urlForSave = self::saveImage($img_avatar_user);

                    if($urlForSave != false){
                      
                      $_result = $this->connection->getDB()->prepare($query);

                      if( $_result->execute([
                            ":firstName" => $firstName,
                            ":lastName" => $lastName,
                            ":ico" => $urlForSave,
                            ":userdId" => $this->USER_ID]) ){

                        if($_result->rowCount()>=0)
                            return true;
                        else return false;
                      }// end <- if(execute)
                    }else errorServer();
                  }else errorServer();
                }

              }else Unauthorized(); # end exisUser()
            }else Unauthorized();  # end VerifyToken()

          } # end <- end isset(params)
        } catch (Exception $e) {
          errorServer();
        }

    } # end updateImage()

	public function getUSER_ID() {
		return $this->USER_ID;
	} 


	public function saveImage($imgObj = "") {
        if(!empty($imgObj)) {
            
            $name = $this->USER_ID . ".jpg";

            $srcFinal = "../public/users/" . $name;
            $url = preg_replace("/api\/index.*/", "", php_self) 
            . "public/users/" . $name;

            //Check File Type == Image
            if($imgObj["type"]=="image/jpg" 
            || $imgObj["type"]=="image/jpeg" 
            || $imgObj["type"]=="image/png" 
            || preg_match("/jpg\Z/", $imgObj["name"])==1
            || preg_match("/png\Z/", $imgObj["name"])==1){

                if(move_uploaded_file($imgObj["tmp_name"], $srcFinal))
                    return $url;
                return false;
            }else return false;
        
        }
    } #end saveImage()

	public function destroyImages(){

		$name = $this->USER_ID . ".jpg";
		$srcFinal = "../public/users/" . $name;

	  if(file_exists($srcFinal)) {
	      if(unlink($srcFinal)==1){
	          return true;
	      }else return false;

	  }else return 0;
	} # end destroyImages()

	public function constructUrl(&$urlImages)  {
			$urlImages = protocol . "://" . http_host . $urlImages;
			return $urlImages;
	}
    
}

 ?>

