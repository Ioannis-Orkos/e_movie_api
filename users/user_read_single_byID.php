<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

  function out(){
    http_response_code(400);
    echo json_encode(array("message" => "Bad Request."));
  }

  // get database connection
  $database = new Database();
  $db_conn = $database->getConnection();

  // prepare product object
  $user = new user($db_conn);

  // set ID property of record to read
  $user->id = isset($_GET['id']) ? $_GET['id'] :die(out());

  // read the details of product to be edited
  $stmt = $user->user_read_single_byID();
  $num  = $stmt->rowCount();

  // check if more than 0 record found
  if($num>0){

    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //$products_arr["records"]=array();

    // set values to object properties
      extract($row);

       //var_dump($row);

      // create array
      $user_arr = array(
        "id"  => $user_id,
        "google_id" => $user_google_id,
        "first_name" =>  $user_first_name,
        //"last_name" => html_entity_decode($user_last_name)
        "last_name" =>  html_entity_decode($user_last_name),
        "display_name"  =>  $user_display_name,
        "email"  =>  $user_email,
        "language"  =>  $user_language,
        "geolat"  =>  $user_geolat,
        "geolng"  =>  $user_geolng,
        "address"  =>  $user_address,
        "postcode" =>  $user_postcode,
        "description"  =>  $user_description,
        "reg_date"  =>  $user_reg_date,
        "prof_img"  =>  $user_prof_img,
        "banner_img" => $user_banner_img,
        "app_info" =>$app_arr = array(
                                       "app_user_ver"              =>$app_user_ver,
                                       "app_cus_Service_no"        =>$app_cus_Service_no,
                                       "app_cus_Service_email"     =>$app_cus_Service_email,
                                       "app_cus_Service_ver_msg"   =>$app_cus_Service_ver_msg
                                     )
      );




      // set response code - 200 OK
      http_response_code(200);

      // make it json format
    //  array_push($user_arr["app_info"], $app_arr);
      echo json_encode($user_arr);
  }

  else{
      // set response code - 404 Not found
      http_response_code(204);
      echo json_encode(array("message" => "User does not exist."));
  }




?>
