<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get database connection
include_once '../config/database.php';
// instantiate user object
include_once '../objects/user.php';

  $database = new Database();
  $db_conn = $database->getConnection();

  $user = new user($db_conn);


  $data_google_id =     isset($_POST["google_id"])?$_POST["google_id"]:"";
  $data_first_name =    isset($_POST["first_name"])?$_POST["first_name"]:"";
  $data_last_name =     isset($_POST["last_name"])?$_POST["last_name"]:"";
  $data_display_name=   isset($_POST["display_name"])?$_POST["display_name"]:"";
  $data_email=          isset($_POST["email"])?$_POST["email"]:"";


    // make sure data is not empty
    if(
        !empty ($data_google_id) &&
        !empty($data_first_name) &&
        !empty($data_last_name) &&
        !empty($data_email)
    ){

        if(empty($data_display_name)) $data_display_name=$data_first_name." ".$data_last_name;

        // set user property values
        $user->google_id =    $data_google_id;
        $user->first_name =   $data_first_name;
        $user->last_name =    $data_last_name;
        $user->display_name = $data_display_name;
        $user->email =        $data_email;

        // create the product
        $response = $user->user_register_onLogin();
        if($response==="true"){
            // set response code - 201 created
            http_response_code(201);
            echo json_encode(array("message" => "User registered."));
        }
        // if unable to reg, tell the user
        else{
            // set response code - 503 service unavailable
            http_response_code(503);
            echo json_encode(array("message" => "Unable to register User Sql_response " .$response));
        }
    }

    // tell the user data is incomplete
    else{
        // set response code - 400 bad request
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create user account. Data is incomplete."));
    }
?>
