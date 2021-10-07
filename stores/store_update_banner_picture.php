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
include_once '../util/picture_fun.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare store object
$store = new store($db);

// set store property values
$form_id="store_banner_form";

$store_banner_img_path = "../picture/store_bannerimage/";

$store_what_to_update = isset($_POST["what_to_update"])?$_POST["what_to_update"]:"";
$store_google_id = isset($_POST["google_id"])?$_POST["google_id"]:"";
$store_banner_img =  "";

//var_dump($_FILES);


$picFun = new PicureFun();
$picFun->form_name = $form_id;
$store = new store($db);


//to resolve warning issues
ini_set('error_reporting', E_STRICT);

if($picFun->picture_Check()&&$picFun->Check_picture_type()&&$picFun->picture_size()&& !empty ($store_google_id)&&!empty ($store_what_to_update)){


      if ($picFun->picture_upload($store_banner_img_path,$store_google_id))
        {

           // set store property values
           $store->google_id = $store_google_id;
           $store->banner_img =  $picFun->file_path;
           if( $store->store_update_bywhat($store_what_to_update)){

               // set response code - 200 ok
               http_response_code(200);

               // tell the store
               echo json_encode(array("message" => "Product was updated."));
           }else{

               // set response code - 503 service unavailable
               http_response_code(503);

               // tell the store
               echo json_encode(array("message" => "Unable to update product1."));
           }

        }else{

            // set response code - 503 service unavailable
            http_response_code(503);

            // tell the store
            echo json_encode(array("message" => "Unable to update product."));
        }
}
// tell the store data is incomplete
else{

    // set response code - 400 bad request
    http_response_code(400);

    // tell the store
    echo json_encode(array("message" => "Unable to create product Data is incomplete."));
}


?>
