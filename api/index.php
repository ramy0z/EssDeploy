<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET,POST,PATCH,DELETE,PUT,OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization");
	require_once('functions.php');
	$api = new Api;
	$api->processApi();
	//echo '{"result":"done"}';
?>