<?php
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