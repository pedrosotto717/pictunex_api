<?php 

require_once "./inc/class/images.class.php";
require_once "./inc/helpers.php";

/**
 * Class Api
 * * Routes 
 */
class Api {
	
	private $URL;
	private $REQUEST_METHOD;
	private $userAdmin;
	private $imgs;

	function __construct($url, $rq_method) {
		$this->URL = $url;
		$this->REQUEST_METHOD = $rq_method;
		$this->imgs = new Images($this->REQUEST_METHOD);
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

					if(isset($params[2])){ //Check if exist some Categories

						$finalResult = $this->imgs->getByCategory( $params[2] );

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
					
					$finalResult = $this->imgs->search($_GET['q']);
					
					if($finalResult == false){
						notFound();
					}else echo json_encode( $finalResult );

				}else{
					notFound();
				}


			}else{//All Images

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
			#$params[1] -> id

		//Check URL parameters 
		$params = preg_split('/[\/]/', $this->URL); //separate URL parameters
		
		if(isset($_POST["action"])){

			if( $params[0]=="images" ){

				if(!isset($params[1])){ //if not defined Insert new Image

					#codigo para insertar una imagen
					if($_POST["action"]=="create"){
						#INSERT INTO 
						var_dump($_FILES["src"]);
						echo "==================================================================";
						if(	isset($_FILES["src"] ) 
							&& isset($_POST["name"])
							&& isset($_POST["keywords"])
							&& isset($_POST["categories"]) ){
								
								$finalResult = $this->imgs->insertImage(
									$_POST["name"],
								    $_POST["keywords"],
								    $_POST["categories"],
								    $_FILES["src"]);

							echo "TODO BIEN EN API";
								
								if($finalResult == false){
									notFound(); //error
								}elseif ($finalResult == null) {
									errorServer();
								}else
								created();
						}else badReq();

					} # end if($_POST["action"]=="create")
					else Unauthorized();

				} # end if(!isset($params[1]))
				elseif( preg_match('/[\/]/', $this->URL) ){ //if exist Parameters

					# UPDATE by ID
					if( preg_match('/[0-9]+\Z/', $params[1]) ){ //Check Request by id
					
					}
				}

			} # end  if( $params[0]=="images" )
			elseif( $this->URL==="login" ){
					echo "LOGIN";

			}else badReq();
		} # end if(isset($_POST["action"]))
		else Unauthorized();

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
						if (true){ //validate User
							$finalResult = $this->imgs->deleteImage($params[2]);

							if($finalResult == false){
								notFound();
							}else ok();
						}else{
							Unauthorized();
						}
					}
				}
			} 
			badReq();
		}
		methodNotAllowed();
	} # end method delete()

} # end class Api


 ?>






