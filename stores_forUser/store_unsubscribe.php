<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/store.php';

  // instantiate database and product object
  $database = new Database();
  $db = $database->getConnection();

  // initialize object
  $store = new store($db);

  // read products will be here

  $user_g_id = isset($_POST["ugid"])?$_POST["ugid"]:"";
  $store_id = isset($_POST["sid"])?$_POST["sid"]:"";

    if( !empty ($user_g_id)&&!empty ($store_id) )
    {
        // query products
        $stmt = $store->store_unsubscribe($user_g_id,$store_id);
        $num = $stmt->rowCount();
        // check if more than 0 record found
    if($num>0){

        // set response code - 200 OK
        http_response_code(200);
        // show products data in json format
        echo json_encode(  array("message" => "Unsubscribed"   ));
    }else{
        // set response code - 404 Not found
        http_response_code(503);
        // tell the user no products found
        echo json_encode(array("message" => "No products found.") );
    } }

?>
