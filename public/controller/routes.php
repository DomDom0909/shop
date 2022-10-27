<?php
	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Slim\Factory\AppFactory;
	use ReallySimpleJWT\Token;

	$app->post("/Authenticate", function (Request $request, Response $response, $args) {
		global $api_password;
		global $api_username;

		//request body input string
		$request_body_string = file_get_contents("php://input");

		//JSON string
		$request_data = json_decode($request_body_string, true);

		$username = $request_data["username"];
		$password = $request_data["password"];

		//error if username or password is wrong
		if ($password != $api_password || $username != $api_username) {
			error("password or username is wrong.", 401);
		}

		//tokens that were createt should be stored in the cookies.
		$token = token::create($password, $username, time() + 3600, "localhost");

		setcookie("token", $token);

		//If it is correct then its true response.
		echo "true";

		return $response;
	});

	$app->post("/Registration", function (Request $request, Response $response, $args) {
		//useres authentication check.
		require "controller/authentification.php";

		//request body input string.
		$request_body_string = file_get_contents("php://input");

		//JSON string.
		$request_data = json_decode($request_body_string, true);

		//checking that all values are enterd.
		if (!isset($request_data0["name"])) {
			error("Please enter a \"name\" field.", 400);
		}
		if (!isset($request_data["age"])|| !is_numeric($request_data["age"])) {
			error("Pleas enter a number for your \"age\" field.", 400);
		}
		if (!isset($request_data["color"])) {
			error("Pleas coose one \"color\" of the following examples \"red\", \"blue\", \"violett\", \"pink\".", 400);
		}

		//clean values if necessary.
		$name = strip_tags(addslashes($request_data["name"]));
		$age = intval($request_data["age"]);
		$color = $request_data["color"];

		//check if the name, age and color field were fild if not thay have to fill it.
		if(empty($name)) {
			error("You have to fill the \"name\" field.", 400);
		}
		if (empty($color) || !in_array($color, array("red", "blue", "violett", "pink"))) {
			error("You must choose one \"color\" of these \"red\", \"blue\", \"violett\" or \"pink\".", 400);
		}

		//name lenght limit
		if (strlen($name) > 250) {
			error("The name you enterd is to long. Pleas enter a less long name.", 400);
		}

		//limit of age
		if ($age < 0 || $age > 100) {
			error("The age u enterd is not possible pleas enter a real age.", 400);
		}

		//age must be an integer
		if (is_float($age)) {
			error("Not real age pleas enter a real age.", 400);
		}

        if (create_new_registration($name, $age, $color) === true) {
            http_response_code(201);
            echo "true";
        }
        else {
            error("We have an error while saving the data.", 500);
        }
        return $response;
	});

    $app->get("/Registration/{registration_id}", function (Request $request, Response $response, $args) {
        
        //authentication check
        require "controller/authentification.php";

        $registration_id = intval($args["registration_id"]);

        //Get entity.
        $registration = get_registration($registration_id);

        if (!$registration) {
            //entity not found
            error("No ID for the registration was found" . $registration_id . ".", 404);
        }
        else if (is_string($registration)) {
            //Error while fetching.
            error($registration, 500);
        }
        else {
            //great success.
            echo json_encode($registration);
        }
        return $response;
    });	

    $app->put("/Registration/{registration_id}", function (Request $request, Response $response, $args) {
        //authentication check
        require "controller/authentification.php";

        $registration_id = intval($args["registration_id"]);

        //get entity.
        $registration = get_registration($registration_id);

        if (!$registration) {
            //entity not found
            error("No ID for the registration was found" . $registration_id . ".", 404);
        }
        else if (is_string($registration)) {
            //error while fetching.
            error($registration, 500);
        }

        //request body input string
        $request_body_string = file_get_contents("php://input");

        //json string
        $request_data = json_decode($request_body_string, true);

        //updated information into fetched entity.
        if (isset($request_data["name"])) {
            //clen name
            $name = strip_tags(addslashes($request_data["name"]));
            
            //name not empty
            if(empty($name)) {
                error("You have to fill the \"name\" field.", 400);
            }
            //name lengh limit
            if (strlen($name) > 250) {
                error("The name you enterd is to long. Pleas enter a less long name.", 400);
            }

            $registration["name"] = $name;
        }
        if (isset($request_data["age"])) {
            //age must be numeric.
            if(!is_numeric($request_data["age"])) {
                error("Pleas enter a integer number for the \"age\" .", 400);
            }

            //clen age
            $age = intval($request_data["age"]);

            //age limit
            if ($age < 0 || $age > 100) {
			error("The age you enterd is not possible pleas enter a real age.", 400);
            }

            //age must be an integer
            if (is_float($age)) {
                error("Not real age pleas enter a real age.", 400);
            }

            $registration["age"] = $age;
        }
        if (isset($request_data["color"])) {
            //clen color
            $color = $request_data["color"];

            //color must be filld out.
            if (empty($color) || !in_array($color, array("red", "blue", "violett", "pink"))) {
                error("The field \"color\" must be choosen by one of \"red\", \"blue\", \"violett\" or \"pink\".", 400);
            }

            $registration["color"] = $color;
        }

        //save information
        if (update_registration($registration_id, $registration["name"], $registration["age"], $registration["color"])) {
            echo "true";
        }
        else {
            error("Error while saving the registration data.", 500);
        }

        return $response;
        });

        $app->delete("/Registration/{registration_id}", function (Request $request, Response $response, $args) {
            //client authentication check
            require "controller/authentication.php";

            $registration_id = intval($args["registration_id"]);

            //delete entity.
            $result = delete_registration($registration_id);

            if (!$result) {
                //no entity was found
                error("No registration was found for this ID " . $registration_id . ".", 404);
            }
            else if (is_string($result)) {
                //error while deleting
                error($registration, 500);
            }
            else {
                //great succrss.
                echo json_encode($result);
            }

            return $response;
        });

        $app->get("/Registration", function (Request $request, Response $response, $args) {
            //check authentication.
            require "controller/authentication.php";

            //get entities.
            $registrations = get_all_registrations();

            if (is_string($registration)) {
                //fetching error.
                error($registration, 500);
            }
            else {
                //great success.
                echo json_encode($registration);
            }
            
            return $response;
        });

