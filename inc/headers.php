<?php 
	header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, AUTHENTICATION, AUTHORIZATION_TOKEN, authorization-token, client-id");
    // header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Allow: GET, POST, PUT, DELETE");
    header("Accept: */*");
    header("Content-Type: application/json");
 ?>