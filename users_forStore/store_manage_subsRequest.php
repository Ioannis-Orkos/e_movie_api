<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../notification/notification.php';


// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// initialize object
$user = new user($db);

// read products will be here

$user_id = isset($_POST["uid"])?$_POST["uid"]:"";
$store_g_id = isset($_POST["sgid"])?$_POST["sgid"]:"";
$req_status = isset($_POST["req_s"])?$_POST["req_s"]:"";

if( !empty ($user_id)&&!empty ($store_g_id) )
{

// query products
$stmt = $user->store_manage_request($user_id,$store_g_id,$req_status);

// check if more than 0 record found
if($stmt){

    // set response code - 200 OK
    http_response_code(200);

    // show products data in json format
    echo json_encode(  array("message" => "request changed  to {$req_status}"   ));
     $user->store_manage_subscritionNotification($user_id,$store_g_id,$req_status);
}else{

    // set response code - 404 Not found
    http_response_code(503);

    // tell the user no products found
    echo json_encode(
        array("message" => "No products found.")
    );
}
}





?>
