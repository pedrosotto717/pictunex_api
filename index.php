<?php

/**
 * Controller 
 */
  
  //includes
  require_once "inc/headers.php";
  require_once "inc/helpers.php";
  require_once "api.php";

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

  $routesApi = new Api($URL,$reqMethod);

  //Check URL Format
  if($URL != "index.php" && preg_match("/(public\S)+(img+[0-9])|(public\Z)|(img)/", $URL)==0){

    //Check request method 
    switch ($reqMethod) { 
      case 'GET':
        $routesApi->get();
      break;


      case 'POST':
        $routesApi->post();
      break;


      case 'DELETE':
        $routesApi->delete();
      break;

      case 'PUT':
        // var_dump($_GET);
        // var_dump($_POST);
        // var_dump($_REQUEST);
        // var_dump( apache_request_headers()["Authorization"] );
        // var_dump($http_response_header);
      break;

      case 'OPTIONS':
        http_response_code(100);
        break;

      default:
        http_response_code(405);
      break;

    } # end switch

  } # end if($URL != "index.php")
  else{
    header("Content-Type: text/html;charset=UTF-8");
    echo "

    <h1>PICTUNEX | API</h1>
    <br>

    <h3>REQUEST:GET</h3>
    <ul>
     <li>api/images <b>Get All Images</b></li>
     <li>api/images?user_id='' <b>Get All User's Images</b></li>
     <li>api/images/'id_img' <b>Get Image by Id</b></li>
     <li>api/images/category <b>Get a array list of the categories available</b></li>
     <li>api/images/category/'category' <b>Get Image by category</b></li>
     <li>api/images/search?q='keyWord' <b>Search</b></li>
     <li>api/images/search?q='keyWord'&&user_id='id' <b>Search by user</b></li>
    </ul>

    <br>

    <h3>REQUEST:POST</h3>
    <ul>
      <li>api/images -> <b>Upload Images</b> 
        <br>
        <p> 
        Header:  HTTP_AUTHORIZATION_TOKEN='Token'
        <br>
        POST: 'action=create'
        <br>
        POST:   FORMDATA(name_img, keywords, categories, img)
        </p>
      </li>

      <li>api/auth/register <b>Create a New User</b>
        <br>
        POST: base64_encode({firts_name,last_name,user_name,password})
      </li>

      <li>api/auth/token <b>AUTHENTICATION</b> 
        <br>
        Header: HTTP_AUTHENTICATION=base64_encode(username:password)
      </li>

      <li>api/auth/verify <b>AUTHORIZATION</b>
        <br>
        Header: HTTP_AUTHORIZATION_TOKEN='Token'
      </li>

      <li>api/images/update/25 <b>Update Image</b>
        <br>
        Header:  HTTP_AUTHORIZATION_TOKEN='Token'
        <br>
        POST:   FORMDATA(name_img, keywords, categories, img)
      </li>

      <li>api/images/delete/'id' <b>Delete Image</b>
        Header:  HTTP_AUTHORIZATION_TOKEN='Token'
      </li>

      <li>

      </li>
    </ul>
    ";

  }

  ?>