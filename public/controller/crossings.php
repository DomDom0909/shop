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
			error("Please enter a \"name\" field.", 500);
		}
		if (!isset($request_data["sku"])|| !is_numeric($request_data["sku"])) {
			error("Pleas enter a number for your \"sku\" field.", 100);
		}
		if (!isset($request_data["image"])) {
			error("Pleas uploaad one \"image\".", 1000);
		}

		//clean values if necessary.
		$name = strip_tags(addslashes($request_data["name"]));
		$sku = intval($request_data["sku"]);
		$image = $request_data["image"];

		//check if the name, sku and image field were fild if not thay have to fill it.
		if(empty($name)) {
			error("You have to fill the \"name\" field.", 500);
		}
		if (empty($image) || !in_array($image, array("image"))) {
			error("You must upload a \"image\".", 400);
		}

		//name lenght limit
		if (strlen($name) > 250) {
			error("The name you enterd is to long. Pleas enter a less long name.", 500);
		}

		//limit of sku
		if ($sku < 0 || $sku > 50) {
			error("The sku u enterd is not possible pleas look that its not under 50.", 100);
		}

		//sku must be an integer
		if (is_float($sku)) {
			error("Not real sku pleas enter a real sku.", 100);
		}

        if (create_new_category($name, $age, $color) === true) {
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

        $category_id = intval($args["category_id"]);

        //Get entity.
        $category = get_category($registration_id);

        if (!$category) {
            //entity not found
            error("No ID for the category was found" . $category_id . ".", 404);
        }
        else if (is_string($category)) {
            //Error while fetching.
            error($category, 500);
        }
        else {
            //great success.
            echo json_encode($category);
        }
        return $response;
    });	

    $app->put("/Registration/{category_id}", function (Request $request, Response $response, $args) {
        //authentication check
        require "controller/authentification.php";

        $category_id = intval($args["category_id"]);

        //get entity.
        $category = get_category($category_id);

        if (!$category) {
            //entity not found
            error("No ID for the category was found" . $category_id . ".", 404);
        }
        else if (is_string($category)) {
            //error while fetching.
            error($category, 500);
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
                error("You have to fill the \"name\" field.", 500);
            }
            //name lengh limit
            if (strlen($name) > 250) {
                error("The name you enterd is to long. Pleas enter a less long name.", 500);
            }

            $registration["name"] = $name;
        }
        if (isset($request_data["sku"])) {
            //age must be numeric.
            if(!is_numeric($request_data["sku"])) {
                error("Pleas enter a integer number for the \"sku\" .", 100);
            }

            //clen sku
            $age = intval($request_data["sku"]);

            //sku limit
            if ($age < 0 || $age > 50) {
			error("The sku you enterd is not possible pleas enter a real sku.", 100);
            }

            //sku must be an integer
            if (is_float($sku)) {
                error("Not real sku pleas enter a real sku.", 100);
            }

            $category["sku"] = $sku;
        }
        if (isset($request_data["image"])) {
            //clen image
            $color = $request_data["image"];

            //image must be filld out.
            if (empty($image) || !in_array($image, array("red", "blue", "violett", "pink"))) {
                error("The field \"image\" must be choosen by one of \"red\", \"blue\", \"violett\" or \"pink\".", 400);
            }

            $category["image"] = $image;
        }

        //save information
        if (update_category($category_id, $category["name"], $category["sku"], $category["image"])) {
            echo "true";
        }
        else {
            error("Error while saving the category data.", 500);
        }

        return $response;
        });

        $app->delete("/Registration/{category_id}", function (Request $request, Response $response, $args) {
            //client authentication check
            require "controller/authentication.php";

            $category_id = intval($args["category_id"]);

            //delete entity.
            $result = delete_category($category_id);

            if (!$result) {
                //no entity was found
                error("No category was found for this ID " . $category_id . ".", 404);
            }
            else if (is_string($result)) {
                //error while deleting
                error($category, 500);
            }
            else {
                //great succrss.
                echo json_encode($result);
            }

            return $response;
        });

        $app->get("/Registration", function (Request $request, Response $response, $args) {
            //check authentication.
            require "controller/authentification.php";

            //get entities.
            $registrations = get_all_categorys();

            if (is_string($category)) {
                //fetching error.
                error($category, 500);
            }
            else {
                //great success.
                echo json_encode($category);
            }
            
            return $response;
        });

