<?php 

function notFound() {
	echo json_encode(['code' => 404, 'msg' => 'Not Found']);
	http_response_code(404);
	die();
}


function Unauthorized() {
	echo json_encode(['code' => 401, 'msg' => 'Unauthorized']);
	http_response_code(401);
	die();
}

function methodNotAllowed() {
	echo json_encode(['code' => 405, 'msg' => 'Method Not Allowed']);
	http_response_code(405);
	die();
}


function badReq() {
	echo json_encode(['code' => 400, 'msg' => 'Bad Request']);
	http_response_code(400);
	die();
}


function errorServer() {
	echo json_encode(['code' => 500, 'msg' => 'Internal Server Error']);
	http_response_code(500);
	die();
}


function created() {
	echo json_encode(['code' => 201, 'msg' => 'Created']);
	http_response_code(201);
	die();
}

function ok() {
	echo json_encode(['code' => 200, 'msg' => 'OK']);
	http_response_code(200);
	die();
}


function escSpecialChar(&$key) {
    $key = strtolower($key);
    $key = htmlentities($key);
    $key = preg_replace('/[.*+\-?^${}()|\\\[\]\/]/', ' ', $key); //escape charartes specials
}


 ?>