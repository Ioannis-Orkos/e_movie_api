<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



// include database and object files
include_once '../config/database.php';
include_once '../objects/store.php';


// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare store object
$store = new store($db);

// set store property values
$store_what_to_update = isset($_POST["what_to_update"])?$_POST["what_to_update"]:"";
$store_google_id = isset($_POST["google_id"])?$_POST["google_id"]:"";
$store_first_name = isset($_POST["first_name"])?$_POST["first_name"]:"";
$store_last_name = isset($_POST["last_name"])?$_POST["last_name"]:"";
$store_display_name= isset($_POST["display_name"])?$_POST["display_name"]:"";
$store_email=  isset($_POST["email"])?$_POST["email"]:"";
$store_geolat = isset($_POST["geolat"])?$_POST["geolat"]:"";
$store_geolng = isset($_POST["geolng"])?$_POST["geolng"]:"";
$store_language = isset($_POST["language"])?$_POST["language"]:"";
$store_prof_img =   isset($_POST["prof_img"])?$_POST["prof_img"]:"";
$store_description =  isset($_POST["description"])?$_POST["description"]:"";
$store_address = isset($_POST["address"])?$_POST["address"]:"";
$store_notif_token = isset($_POST["notif_token"])?$_POST["notif_token"]:"";
$store_business_name = isset($_POST["business_name"])?$_POST["business_name"]:"";
$store_postcode = isset($_POST["postcode"])?$_POST["postcode"]:"";
$store_banner_img = isset($_POST["banner_img"])?$_POST["banner_img"]:"";


//to resolve warning issues
ini_set('error_reporting', E_STRICT);



// make sure basic data is not empty
if( !empty ($store_google_id)&&!empty ($store_what_to_update))
{
    // set store property values
    $store->google_id = $store_google_id;
    $store->first_name =  $store_first_name;
    $store->last_name =  $store_last_name;
    $store->display_name =  $store_display_name;
    $store->email =  $store_email;
    $store->geolat =  $store_geolat;
    $store->geolng =  $store_geolng;
    $store->language =  $store_language;
    $store->address =  $store_address;
    $store->postcode =  $store_postcode;
    $store->description =  $store_description;
    $store->business_name =  $store_business_name;
    $store->banner_img =  $store_banner_img;
    $store->notif_token =  $store_notif_token;

// update the store
if( $store->store_update_bywhat($store_what_to_update)){

    // set response code - 200 ok
    http_response_code(200);

    // tell the store
    echo json_encode(array("message" => "Product was updated."));
}else{

    // set response code - 503 service unavailable
    http_response_code(503);

    // tell the store
    echo json_encode(array("message" => "Unable to update product."));
}}
// tell the store data is incomplete
else{

    // set response code - 400 bad request
    http_response_code(400);

    // tell the store
    echo json_encode(array("message" => "Unable to create product Data is incomplete."));
}
?>
