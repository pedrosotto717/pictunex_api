<?php 

/**
 * ENV
 */

class ENV
{

	private static $ENV_PATH = [];
	private static $NUM_VAR_PATH = 0;
	private static $env = false;

	public static function init() {
		$handle = fopen("config/.env", "r");
	    $i = 0;

	    if ($handle) {
	        while (($buffer = fgets($handle,1000)) !== false) {
	            $i++;
	            list($key, $value) = explode("=", $buffer);
	    		self::$ENV_PATH[$key] = preg_replace("/\\n|\\r/","",$value);
	        }

	        if (!feof($handle))
	            echo "Error: unexpected fgets() fail\n";

	        fclose($handle);
	    	self::$NUM_VAR_PATH = $i;
	    }
	}//end <- init()

	public function get($k) {
		//autoload .env secure
		if(!self::$env)
	  		ENV::init();

		if(isset(self::$ENV_PATH[$k]))
			return self::$ENV_PATH[$k];
		else return null;
	}
}

 ?>