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
include_once '../util/picture_fun.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare user object
$user = new user($db);

// set user property values
$form_id="user_profile_form";

$user_prof_img_path = "../picture/user_profileimage/";

$user_what_to_update = isset($_POST["what_to_update"])?$_POST["what_to_update"]:"";
$user_google_id = isset($_POST["google_id"])?$_POST["google_id"]:"";
$user_prof_img =  "";



$picFun = new PicureFun();
$picFun->form_name = $form_id;


//to resolve warning issues
ini_set('error_reporting', E_STRICT);

if($picFun->picture_Check()&&$picFun->Check_picture_type()&&$picFun->picture_size()&& !empty ($user_google_id)&&!empty ($user_what_to_update)){


      if ($picFun->picture_upload($user_prof_img_path,$user_google_id))
        {

           // set user property values
           $user->google_id = $user_google_id;
           $user->prof_img =  $picFun->file_path;
           if( $user->user_update_bywhat($user_what_to_update)){

               // set response code - 200 ok
               http_response_code(200);

               // tell the user
               echo json_encode(array("message" => "Product was updated."));
           }else{

               // set response code - 503 service unavailable
               http_response_code(503);

               // tell the user
               echo json_encode(array("message" => "Unable to update product."));
           }

        }else{

            // set response code - 503 service unavailable
            http_response_code(503);

            // tell the user
            echo json_encode(array("message" => "Unable to update product."));
        }






}
// tell the user data is incomplete
else{

    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to create product Data is incomplete."));
}


?>
