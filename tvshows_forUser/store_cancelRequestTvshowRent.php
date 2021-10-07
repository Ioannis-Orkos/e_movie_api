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

$data = json_decode(file_get_contents("php://input"));

$tvshow_id = isset($_POST["tid"])?$_POST["tid"]:"";
$user_g_id = isset($_POST["ugid"])?$_POST["ugid"]:"";
$store_id = isset($_POST["sid"])?$_POST["sid"]:"";


if( !empty ($user_g_id)&&!empty ($store_id)&&!empty ($tvshow_id) )
{
// query products
$stmt = $store->store_cancelRequestTvshowRent($tvshow_id,$user_g_id,$store_id);
$num = $stmt->rowCount();

  // check if more than 0 record found
  if($num>0){

      // set response code - 200 OK
      http_response_code(200);
      // show products data in json format
      echo json_encode(  array("message" => "removed"   ));
  }else{

      // set response code - 404 Not found
      http_response_code(503);
      // tell the user no products found
      echo json_encode(array("message" => "No products found.")
      );
}
}else{
    // set response code - 404 Not found
    http_response_code(404);
    // tell the user no products found
    echo json_encode(array("message" => "No products found.") );
}





?>
