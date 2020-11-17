<?php 

require_once "./inc/helpers.php";
require_once "./inc/class/images.class.php";
require_once "./inc/class/user.class.php";

/**
 * Class Api
 * * Routes 
 */

// var_dump($_POST["user"]);
// var_dump( apache_request_headers() );
// var_dump($_SERVER);
// var_dump($_SERVER["HTTP_CLIENT_ID"]);

class Api{
	
	private $URL;
	private $REQUEST_METHOD;
	private $userAdmin;
	private $imgs;

	function __construct($url, $rq_method) {

		$this->URL = $url;
		$this->REQUEST_METHOD = $rq_method;
		$this->imgs = new Images($this->REQUEST_METHOD);
		$this->userAdmin = new User();
	}


	public function get() {
		##Leyends 
			#$params[0] -> images
			#$params[1] -> id OR category
			#$params[2] -> Category_to_search

		$params = preg_split('/[\/]/', $this->URL); //separate URL parameters
		
		if( $params[0]=="images" ){ //Check request to /images
			 
			//Check URL parameters 
			if( preg_match('/[\/]/', $this->URL) ){ //if exist Parameters

				if(preg_match('/[0-9]+\Z/', $params[1])){ //Check Request by id

					$finalResult = $this->imgs->getByID($params[1]);

					if($finalResult == false){
						notFound();
					}
					else echo json_encode( $finalResult );

				}elseif ($params[1] == 'category'){ //Check Request by Categories
					
					$finalResult = null;

					if( isset($params[2]) ){ //Check if exist some Categories

						if( isset($_GET["user_id"]) ){

							$user = $this->userAdmin->validateUSER_ID($_GET["user_id"]);

	              			if($user !== false){
								$finalResult = $this->imgs->getByCategory( $params[2], $user["username"] );
	              			}else 
	              				Unauthorized();

						}else{
							$finalResult = $this->imgs->getByCategory( $params[2] );
						}

						if($finalResult == false){
							notFound();
						}else echo json_encode( $finalResult );

					}else{

						$finalResult = $this->imgs->getCategories();

						if($finalResult == false){
							notFound();
						}else echo json_encode( $finalResult );

					}

				}elseif ($params[1] == 'search' && !empty($_GET['q']) ) { //Check Request For Search
           if(isset($_GET["user_id"])){
              //flag
              $user = $this->userAdmin->validateUSER_ID($_GET["user_id"]);
              if($user !== false){ // if Exist the USER

                 $finalResult = $this->imgs->search($_GET['q'],$user["username"]);

                 if($finalResult!==false && $finalResult!==null){
                    echo json_encode( $finalResult );
                 }else
                    notFound();
              }else
                 badReq();

              return false;
              exit();
           }else{ //withOut User_id

              $finalResult = $this->imgs->search($_GET['q']);
              
              if($finalResult == false){
                 notFound();
              }else 
                 echo json_encode( $finalResult );
           }
				}else{
					notFound();
				}


			}else{//All Images

				if(isset($_GET["user_id"])){
					//flag
					$user = $this->userAdmin->validateUSER_ID($_GET["user_id"]);

          if($user !== false){ // if Exist the USER

						$finalResult = $this->imgs->getAll($user["username"]);

						if($finalResult!==false && $finalResult!==null){
                     echo json_encode( $finalResult );
						}else
							badReq();
					}else
						badReq();

					return false;
					exit();
				}

				$finalResult = $this->imgs->getAll();

				if($finalResult == false){
					notFound();
				}else echo json_encode( $finalResult );
			}

		}#end  if( $params[0]=="images" )
		else{
			notFound();
		}
	} # end method get()



	public function post() {
		##Leyends 
			#$params[0] -> images
			#$params[1] -> delete
			#$params[2] -> id


		//Check URL parameters 
		$params = preg_split('/[\/]/', $this->URL); //separate URL parameters
		//auth/register
		if(isset($params[0])){
			if( $params[0]=="images" ){
			
				if(isset($_POST["action"])){
					if($_POST["action"]=="create" && !isset($params[1])){

						if( isset($_SERVER["HTTP_AUTHORIZATION_TOKEN"]) ){

							$TOKEN = $_SERVER["HTTP_AUTHORIZATION_TOKEN"];

							$result = $this->userAdmin->Authorization($TOKEN);
							  // echo $this->userAdmin->DATA_TOKEN;
							  // echo $this->userAdmin->getUSER_ID();

							if($result===true){
								$user = $this->userAdmin->validateUSER_ID($this->userAdmin->getUSER_ID());

								if( !empty($user["username"])
									&& isset($_FILES["src"] ) 
									&& isset($_POST["name"])
									&& isset($_POST["keywords"])
									&& isset($_POST["categories"]) ){
										
									$finalResult = $this->imgs->insertImage(
											$_POST["name"],
											$_POST["keywords"],
											$_POST["categories"],
											$_FILES["src"],
                      						$user["username"]);

											if($finalResult == false){
												errorServer(); //error
											}elseif ($finalResult == null) {
												errorServer();
											}else created();
									}else badReq();
							}
						} // end <- HTTP_AUTHORIZATION_TOKEN
						else{
							Unauthorized();
						}
					} # end if($_POST["action"]=="create" && !isset($params[1]))

				} # end if set $_POST["action"]
				elseif (isset($params[1]) && isset($params[2])) {
					if ($params[1]=="update"){
						if( preg_match('/[0-9]+\Z/', $params[2]) ){//Check Request by id

							if( isset($_SERVER["HTTP_AUTHORIZATION_TOKEN"]) ){
					
								$TOKEN = $_SERVER["HTTP_AUTHORIZATION_TOKEN"];
							
								$result = $this->userAdmin->Authorization($TOKEN);
								// echo $this->userAdmin->DATA_TOKEN;
								// echo $this->userAdmin->getUSER_ID();
							

								if($result===true){
									$user = $this->userAdmin->validateUSER_ID($this->userAdmin->getUSER_ID());
							
									if( !empty($user["username"]) 
									&& isset($_POST["name"])
									&& isset($_POST["keywords"])
									&& isset($_POST["categories"]) ){

										if($_FILES == 0 || $_FILES == null)
											$files = NULL;
										else
											$files = $_FILES["src"];


										$finalResult = $this->imgs->updateImage(
										$params[2],
										$_POST["name"],
										$_POST["keywords"],
										$_POST["categories"],
										$files,
										$user["username"]);
							
										if($finalResult == false){
										errorServer(); //error
										}elseif ($finalResult == null) {
										errorServer();
										}else created();
									}else badReq();
								}
							} // end <- HTTP_AUTHORIZATION_TOKEN
							else{
								Unauthorized();
							}
							
					
						}
					} # end if $params[1]=="update"
					
				}
			} # end  if( $params[0]=="images" )
			elseif( $params[0]=="auth" ){

				if(isset($params[1])){

          if( $params[1]=="register" ){

            if( isset($_POST["user"]) && isset($_SERVER["HTTP_CLIENT_ID"])){

              $user = $_POST["user"];
              $client_id_px = $_SERVER["HTTP_CLIENT_ID"];

              $result = $this->userAdmin->NewUser($user,$client_id_px);
              
              if($result===true){
               echo json_encode(["statusCode" => 201]);
               created(); //flag IMPORTANT
              }elseif ($result===null) {
                Unauthorized();
              }else{
                badReq();
              }
            }

          }elseif($params[1]=="token"){
            if ( isset($_SERVER["HTTP_AUTHENTICATION"]) ) {

              $USER = $_SERVER["HTTP_AUTHENTICATION"];
              $result = $this->userAdmin->Authentication($USER);

              if($result!==false){
                echo json_encode(["access_token" => $result]);
                http_response_code(200);
                return true;
              }

              Unauthorized();
            }

          }elseif( $params[1]=="verify" ){

            if ( isset($_SERVER["HTTP_AUTHORIZATION_TOKEN"]) ) {

              $TOKEN = $_SERVER["HTTP_AUTHORIZATION_TOKEN"];

              $result = $this->userAdmin->Authorization($TOKEN);
                  // echo $this->userAdmin->DATA_TOKEN;
                  // echo $this->userAdmin->getUSER_ID();

              if($result===true){

              	if(isset($_POST["user"])){

              		if($_POST["user"] == true){
              			$user = $this->userAdmin->validateUSER_ID($this->userAdmin->getUSER_ID());

	              		$user["code"] = 200;

	                	echo json_encode($user);
              		}else
              			Unauthorized();
              			exit();              		
              	}else{
                	echo json_encode(["code" => 200]);
              	}

                http_response_code(200);
                return true;
              }
              elseif ($result==1) {
                echo json_encode(["code" => "-1"]);
                http_response_code(401);
                return false;
              }
              Unauthorized();
            }

          }else if( $params[1]=="update" ){

						if ( isset($_SERVER["HTTP_AUTHORIZATION_TOKEN"]) ) {
							$TOKEN = $_SERVER["HTTP_AUTHORIZATION_TOKEN"];
							

							if( isset($_POST["user"]) ){

								if( isset($_FILES["ico"]) )
									$file = $_FILES["ico"];
								elseif ( isset($_POST["ico"]) ) {
									$file = $_POST["ico"];
								}

								$result = $this->userAdmin->updateUser($TOKEN, $_POST["user"], $file);


								if($result===true){
									ok();
								}else badReq();
							}

						}else badReq();


          } # end $params[1]=="update"
				} # end if(isset($params[1]))
			}else badReq();
			badReq();
		}
		badReq();
	} # end method post()


	public function delete() {
		##Leyends 
			#$params[0] -> images
			#$params[1] -> id

		//Check URL parameters 
		$params = preg_split('/[\/]/', $this->URL); //separate URL parameters
		
		if( $params[0]=="images" ){
			if($params[1]='delete'){
				if( isset($params[2]) ){
					if( preg_match('/[0-9]+\Z/', $params[2]) ){

						if( isset($_SERVER["HTTP_AUTHORIZATION_TOKEN"]) ){
							$TOKEN = $_SERVER["HTTP_AUTHORIZATION_TOKEN"];

							$result = $this->userAdmin->Authorization($TOKEN);

							if($result===true){

								$user = $this->userAdmin
										->validateUSER_ID($this->userAdmin->getUSER_ID());

								$finalResult = $this->imgs->deleteImage($params[2], $user["username"]);

								if($finalResult == false){
									notFound();
								}else ok();								
							}
						}else Unauthorized();
					}
				}
				badReq();
			} 
			badReq();
		}
		methodNotAllowed();
	} # end method delete()

} # end class Api
 ?>






