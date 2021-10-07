<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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

$movie_id = isset($_POST["mid"])?$_POST["mid"]:"";
$user_g_id = isset($_POST["ugid"])?$_POST["ugid"]:"";
$store_id = isset($_POST["sid"])?$_POST["sid"]:"";
$user_msg= isset($_POST["user_msg"])?$_POST["user_msg"]:"";



if(!empty($user_g_id)&&!empty ($store_id) ){

    // query products
    $stmt = $store->store_requestMovieRent($movie_id,$user_g_id,$store_id,$user_msg);
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if($num>0){
        // set response code - 200 OK
        http_response_code(200);
        echo json_encode(array("message" => "requested."));
        $store->store_requestMovieRentNotification($user_g_id,$store_id,$movie_id);


    }else{
        // set response code - 503
        http_response_code(503);
        echo json_encode(array("message" => "Not found or already requested.") );
    }
}else{
    // set response code - 404 Not found
    http_response_code(404);
    echo json_encode(array("message" => "No products found.") );
}


?>
