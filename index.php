<?php

/**
 * Controller 
 */

  //includes
  require_once "inc/headers.php";
  require_once "inc/helpers.php";
  require_once "api.php";

  // foreach ($_SERVER as $key => $value) {
  //   echo "<br> $key :   " . $value;
  // };

  // get request method 
  $reqMethod = $_SERVER["REQUEST_METHOD"];

  define("http_host", $_SERVER["HTTP_HOST"]);
  define("php_self", $_SERVER["PHP_SELF"]);
  define("protocol", $_SERVER["REQUEST_SCHEME"]);

  //validate the URL
  if(!isset($_GET['url'])){
    notFound();
  }


  //save URL example: "index.php/images"
  $URL = $_GET['url'];

  //create Object Routes
  $routesApi = new Api($URL,$reqMethod);

  //Check URL Format
  if($URL != "index.php" && preg_match("/(public\S)+(img+[0-9])|(public\Z)|(img)/", $URL)==0){

    //Check request method 
    switch ($reqMethod) { 
      case 'GET':
        $routesApi->get();
      break;


      case 'POST':
        var_dump("Metodo POST");
        $routesApi->post();
      break;

      case 'PUT':
        var_dump("Metodo PUT");
        echo "Create";
            var_dump($_FILES["src"]);
            echo "<br>";
            var_dump($_GET["img"]);
      break;

      case 'DELETE':
        var_dump("Metodo DELETE");
      break;

      default:
        http_response_code(405);
      break;

    } # end switch

  } # end if($URL != "index.php")
  elseif($URL == "public" || preg_match("/(public\S)+(img+[0-9])|(public\Z)|(img)/", $URL)==1)
    // echo "<img src='./public/img/1.jpg'>";
    header("Location: public/img/1.jpg");
  else
  echo "SOLO AL INDEX -> HTML_DOC";

  ?>