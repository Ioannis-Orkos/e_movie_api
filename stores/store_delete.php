<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object file
include_once '../config/database.php';
include_once '../objects/store.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare product object
$store = new store($db);

// get product id

//var_dump($data);

// set product id to be deleted
$store->id =isset($_GET["id"]) ? $_GET["id"] : "";

$stmt = $store->store_delete();
$num = $stmt->rowCount();
// delete the product


if($num>0){

    // set response code - 200 ok
    http_response_code(200);

    // tell the store
    echo json_encode(array("message" => "store account was deleted."));
}elseif ($num==0) {

      // set response code - 404 Not found
      http_response_code(404);

      // tell the store account does not exist
      echo json_encode(array("message" => "store account does not exist."));

}

// if unable to delete the store account
else{

    // set response code - 503 service unavailable
    http_response_code(503);

    // tell the store
    echo json_encode(array("message" => "Unable to delete store account."));
}
?>
