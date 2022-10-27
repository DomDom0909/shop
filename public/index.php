<?php

	header("Content-Type: application/json");

	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Slim\Factory\AppFactory;
	use ReallySimpleJWT\Token;

	require __DIR__ . "/../vendor/autoload.php";
	require "model/student.php";
	require_once "config/config.php";

	$app = AppFactory::create();
	
	$app->run();
?>