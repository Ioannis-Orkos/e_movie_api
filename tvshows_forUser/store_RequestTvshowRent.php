<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");

// include database and object files
include_once '../config/database.php';
include_once '../objects/store.php';
include_once '../notification/notification.php';


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
$user_msg= isset($_POST["user_msg"])?$_POST["user_msg"]:"";




if( !empty ($user_g_id)&&!empty ($store_id)&&!empty ($store_id) ){

    // query products
    $stmt = $store->store_requestTvshowRent($tvshow_id,$user_g_id,$store_id,$user_msg);
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if($num>0){
        // set response code - 200 OK
        http_response_code(200);
        echo json_encode(array("message" => "requested."));
        $store->store_requestTvshowRentNotification($tvshow_id,$user_g_id,$store_id);

    }else{
        // set response code - 404 Not found
        http_response_code(503);
        echo json_encode(array("message" => "Not found or already requested.") );
    }
}else{
  // set response code - 204 No Content
  http_response_code(204);
  // tell the user products does not exist
    echo json_encode(
        array("message" => "No products found.")
    );
}


?>
