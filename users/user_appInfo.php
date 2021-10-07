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


  // read the details of product to be edited
  $stmt = $user->user_read_appInfo();
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
      $app_info = array(
                       "app_user_ver"              =>$app_user_ver,
                       "app_cus_Service_no"        =>$app_cus_Service_no,
                       "app_cus_Service_email"     =>$app_cus_Service_email,
                       "app_cus_Service_ver_msg"   =>$app_cus_Service_ver_msg
      );




      // set response code - 200 OK
      http_response_code(200);

      // make it json format
    //  array_push($user_arr["app_info"], $app_arr);
      echo json_encode($app_info);
  }

  else{
      // set response code - 404 Not found
      http_response_code(204);
      echo json_encode(array("message" => "appinfo does not exist."));
  }




?>
