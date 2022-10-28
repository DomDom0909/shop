<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use ReallySimpleJWT\Token;

/**
     * @OA\Post(
     *     path="/authentification",
     *     summary="Used to authenticate and obtain an access token that will be stored in the cookies.",
     *     tags={"General"},
     *     requestBody=@OA\RequestBody(
     *         request="/Authenticate",
     *         required=true,
     *         description="The credentials are passed to the server via the request body.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", example="bannert"),
     *                 @OA\Property(property="password", type="string", example="whynot123")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully authenticated")),
     *     @OA\Response(response="401", description="Invalid credentials")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */


// Connetct to database.
require "model/database.php";

function get_all_categorys() {
    global $database;

    $result = $database->query("SELECT * FROM category");

    if (!$result) {
        return "fetching error.";
    }
    else if ($result === true || $result->num_rows == 0) {
        return array();
    }
    $categorys = array();

    while ($category = $result->fetch_assoc()) {
        $categorys[] = $category;
    }

    return $categorys;
}

function create_new_category($name, $sku, $image) {
    global $database;

    $result = $database->query("INSERT INTO category(name, sku, image) VALUES('$name', $sku, '$image')");

    if (!$result) {
        return false;
    }

    return true;
}

function get_category($category_id) {
    global $database;

    $result = $database->query("SELECT * FROM category WHERE category_id = $category_id");

    if (!$result) {
        return "fetching error.";
    }
    else if ($result === true || $result->num_rows == 0) {
        return null;
    }
    else {
        $category = $result->fetch_assoc();

        return $category;
    }

}

function update_category($category_id, $name, $sku, $image) {
    global $database;


$result = $database->query("UPDATE category SET name = '$name', sku = $sku, image = '$image' WHERE category_id = $category_id");

    if (!$result) {
        return false;
    }

return true;

}

function delete_category($category_id) {
    global $database;

    $result = $database->query("DELETE FROM category WHERE category_id = $category_id");

    if (!$result) {
        return "deleting error";
    }
    else if ($database->affected_rows == 0) {
        return null;
    }
    else {
        return true;
    }
}