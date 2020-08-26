<?php 

require_once "./inc/class/connection.class.php";

/**
 * Routes 
 */
class Api {
	
	private $URL;
	private $REQUEST_METHOD;
	private $userAdmin;
	private $img;
	private $DB;

	function __construct($url, $rq_method) {
		$this->URL = $url;
		$this->REQUEST_METHOD = $rq_method;

		$this->DB = new Connection("pictunex");
	}


	public function get() {
		
		if(preg_match('/images/', $this->URL)){ //Check request to /images
			
			//Check URL parameters 
			// echo "SOLICITUD CORRECTA";

			if( preg_match('/[\/]/', $this->URL) ){

				$params = preg_split('/[\/]/', $this->URL);

				if(preg_match('/[0-9]/', $this->URL)){

					echo "<br>PETICION POR ID: ";
					echo $params[1];

				}elseif ($params[1] == 'category'){

					echo "<br>PETICION POR Categoria";
					//hago el query a la base de datos de la tabla categorias

				}elseif ($params[1] == 'search' && !empty($_GET['q']) ) {
					
					echo "<br>PETICION POR Busqueda";
					echo $_GET['q'];

				}else{
					echo json_encode(['code' => 404, 'msg' => 'Resource Not Found']);
				    http_response_code(404);
				}


			}else{
				//All Images
				echo json_encode( $this->DB->queryAll('images') );
			}

		}#end  if(preg_match('/[images]/', $this->URL))
		else{

			echo "INCORRECTA";
			var_dump($this->URL);
		}
	}# end method get()



	public function post() {

		//Check URL parameters 
        if(preg_match('/[\/]/', $this->URL)){
	        $params = preg_split('/[\/]/', $this->URL);
	        echo "images => id: ";
	    	echo $params[1]; 
        }else{
         	echo "NO Pasaron Parametros";
        }

	} # end method post()

} # end class Api





	// if(preg_match('/[0-' . $this->img->num_records . ']/', $this->URL)){


/* 			foreach($arr as $key => $value){
				echo $key . '  =>  ' . $value;
				$arr[$key] = utf8_encode($value);
			} */
 ?>






