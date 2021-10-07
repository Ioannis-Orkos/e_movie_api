<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/store.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

function out(){
  http_response_code(400);
  echo json_encode(array("message" => "Bad Request."));
}

// prepare product object
$store = new store($db);

// set ID property of record to read
$store->id = isset($_GET['id']) ? $_GET['id'] : die(out());

// read the details of product to be edited
$stmt = $store->store_read_single_byID();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

  // get retrieved row
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $products_arr["records"]=array();
  // set values to object properties

    extract($row);
    //var_dump($row);
    //create array
    $store_arr = array(
      "id"  =>          $store_id,
      "google_id" =>    $store_google_id,
      "first_name" =>   $store_first_name,
      "last_name" =>    $store_last_name,
      "display_name"=>  $store_display_name,
      "email"=>         $store_email,
      "language"=>      $store_language,
      "geolat"  =>      $store_geolat,
      "geolng"  =>      $store_geolng,
      "address"  =>     $store_address,
      "postcode" =>     $store_postcode,
      "description"=>   $store_description,
      "reg_date"=>      $store_reg_date,
      "prof_img"=>      $store_prof_img,
      "business_name"=> $store_business_name,
      "banner_img"=>    $store_banner_img,
      "notif_token"=>   $store_notif_token
    );
    // set response code - 200 OK
    http_response_code(200);
    echo json_encode($store_arr);
}

else{
    //No Content
    http_response_code(204);
    echo json_encode(array("message" => "ID does not exist."));
}
?>
