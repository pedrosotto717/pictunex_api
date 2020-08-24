<?php 

/**
 * Routes 
 */
class Api {
	
	private $URL;
	private $REQUEST_METHOD;

	function __construct($url, $rq_method) {
		$this->URL = $url;
		$this->REQUEST_METHOD = $rq_method;
	}



	public function get() {

		//Check URL parameters 
        if(preg_match('/[\/]/', $this->URL)){
	        $params = preg_split('/[\/]/', $this->URL);
	        echo "images => id: ";
	    	echo $params[1]; 
        }else{
         	echo "TODAS LAS IMAGENES";
        }

	}# end GET



	public function post() {

		//Check URL parameters 
        if(preg_match('/[\/]/', $this->URL)){
	        $params = preg_split('/[\/]/', $this->URL);
	        echo "images => id: ";
	    	echo $params[1]; 
        }else{
         	echo "NO Pasaron Parametros";
        }

	} # end POST

} # end class Api

 ?>