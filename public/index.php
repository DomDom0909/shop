<?php
	//Content type set for all endpoints : application/json
	header("Content-Type: application/json");

	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Slim\Factory\AppFactory;
	use ReallySimpleJWT\Token;

	require __DIR__ . "/../vendor/autoload.php";
	require "model/student.php";
	require_once "config/config.php";

	$app = AppFactory::create();

	function error($message, $code) {
		//error = json object.
		$error = array("message" => $message);
		echo json_encode($error);

		//response code.
		http_response_code($code);

		//end scripts.
		die();
	}

	require "controller/routes.php";
	
	$app->run();
?>