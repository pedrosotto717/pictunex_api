<?php 

require_once "php-jwt-master/src/JWT.php";
require_once "env.class.php";

use Firebase\JWT\JWT;

/**
 * Class Auth Implement JWT Class
 */

// var_dump( hash_hmac("sha256", base64_encode("@pictunex"), "PICTUNEX") );

class Auth
{
    // private static $secret_key = ;
    private static $encrypt = ['HS256'];
    private static $aud = null;

    public static function SignIn($data = "",$user_id)
    {
        $time = time();

        $payload = array(
        	'iat' => $time, //current time
            'exp' => $time + (ENV::get("TIME_EXP")*60), // time expiration
            "iss" => "pictunex",  // owner or creator
            'aud' => self::Aud(), // aud
            'user_id' => $user_id, // user_id validate hash_hmac("sha256",base64_encode("pedroSotto"),"PICTUNEX")
            'data' => $data // data
        );

        return JWT::encode($payload, ENV::get("PRIVATE_KEY"));
    }

    public static function Check($token) {

        $code = null;
        $decode = null;

        if(empty($token)){
            return false;
        }

        try {

            $decode = JWT::decode(
                $token,
                ENV::get("PRIVATE_KEY"),
                self::$encrypt
            );

            if($decode->aud != self::Aud()) {
                $decode = null;
                return false;
            } 

        } catch (\Exception $e) {
            $code = $e->getCode();
        } finally {

            if($code==1)
                return 1;
            else 
                return $decode!=null ? $decode : false;
        }
    }

    private static function Aud() {

    	$Aud = "";

		if(isset($_SERVER["SERVER_NAME"])){
		    $Aud = $_SERVER["SERVER_NAME"];
		}elseif(isset($_SERVER["HTTP_HOST"])){
		    $Aud = $_SERVER["HTTP_HOST"];
		}

		if(isset($_SERVER["REMOTE_ADDR"]))
		    $Aud .= $_SERVER["REMOTE_ADDR"];

		if(isset($_SERVER["HTTP_USER_AGENT"]))
		    $Aud .= $_SERVER["HTTP_USER_AGENT"];

        return hash("sha256", $Aud);
    }
}

 ?>