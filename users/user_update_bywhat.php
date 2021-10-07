<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare user object
$user = new user($db);

// set user property values
$user_what_to_update = isset($_POST["what_to_update"])?$_POST["what_to_update"]:"";
$user_google_id = isset($_POST["google_id"])?$_POST["google_id"]:"";
$user_first_name = isset($_POST["first_name"])?$_POST["first_name"]:"";
$user_last_name = isset($_POST["last_name"])?$_POST["last_name"]:"";
$user_display_name= isset($_POST["display_name"])?$_POST["display_name"]:"";
$user_email=  isset($_POST["email"])?$_POST["email"]:"";
$user_geolat = isset($_POST["geolat"])?$_POST["geolat"]:"";
$user_geolng = isset($_POST["geolng"])?$_POST["geolng"]:"";
$user_language = isset($_POST["language"])?$_POST["language"]:"";
$user_prof_img =   isset($_POST["prof_img"])?$_POST["prof_img"]:"";
$user_description =  isset($_POST["description"])?$_POST["description"]:"";
$user_address = isset($_POST["address"])?$_POST["address"]:"";
$user_postcode = isset($_POST["postcode"])?$_POST["postcode"]:"";
$user_notif_token = isset($_POST["notif_token"])?$_POST["notif_token"]:"";

  //to resolve warning issues
  ini_set('error_reporting', E_STRICT);


  // make sure basic data is not empty
  if( !empty ($user_google_id)&&!empty ($user_what_to_update))
  {
      // set user property values
      $user->google_id = $user_google_id;
      $user->first_name =  $user_first_name;
      $user->last_name =  $user_last_name;
      $user->display_name =  $user_display_name;
      $user->email =  $user_email;
      $user->geolat =  $user_geolat;
      $user->geolng =  $user_geolng;
      $user->language =  $user_language;
      $user->address =  $user_address;
      $user->postcode =  $user_postcode;
      $user->description =  $user_description;
      $user->prof_img =  $user_prof_img;
      $user->notif_token =  $user_notif_token;

    // update the user
    if( $user->user_update_bywhat($user_what_to_update)){

        // set response code - 200 ok
        http_response_code(200);
        echo json_encode(array("message" => "Product was updated."));
    }else{

        // set response code - 503 service unavailable
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update product."));
     }
  }
  // tell the user data is incomplete
  else{
      // set response code - 400 bad request
      http_response_code(400);
      echo json_encode(array("message" => "Unable to create product Data is incomplete."));
  }
?>
