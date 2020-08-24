<?php

/**
 * Controller 
 */

  //includes
  require_once("inc/headers.php");
  require_once("api.php");

  // get request method 
  $reqMethod = $_SERVER["REQUEST_METHOD"];


  //validate the URL
  if(!isset($_GET['url'])){
    http_response_code(400);
    echo json_encode(['code' => 400, 'msg' => 'Bad request']);
    die();
  }


  //save URL example: "index.php/images"
  $URL = $_GET['url'];

  //create Object Routes
  $routesApi = new Api($URL,$reqMethod);

  //Check URL Format
  if($URL != "index.php"){

    //Check request method 
    switch ($reqMethod) { 
      case 'GET':

        var_dump("Metodo GET");
        $routesApi->get();
          
      break;


      case 'POST':
        var_dump("Metodo POST");
        $routesApi->post();
      break;


      case 'PUT':
        $body = file_get_contents("php://input");
        var_dump("Metodo PUT");
      break;


      case 'DELETE':
        var_dump("Metodo DELETE");
      break;

      default:
        http_response_code(405);
      break;

    } # end switch

  } # end if($URL != "index.php")


  ?>