<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");

// get database connection
include_once '../config/database.php';
// instantiate store object
include_once '../objects/store.php';

  $database = new Database();
  $db = $database->getConnection();

  $store = new store($db);

  $data_google_id = isset($_POST["google_id"])?$_POST["google_id"]:"";
  $data_first_name = isset($_POST["first_name"])?$_POST["first_name"]:"";
  $data_last_name = isset($_POST["last_name"])?$_POST["last_name"]:"";
  $data_display_name= isset($_POST["display_name"])?$_POST["display_name"]:"";
  $data_email=  isset($_POST["email"])?$_POST["email"]:"";

  // make sure data is not empty
  if(
      !empty ($data_google_id) &&
      !empty($data_first_name) &&
      !empty($data_last_name) &&
      !empty($data_email)
  ){

      if(empty($data_display_name)) $data_display_name=$data_first_name." ".$data_last_name;

      // set store property values
      $store->google_id = $data_google_id;
      $store->first_name = $data_first_name;
      $store->last_name = $data_last_name;
      $store->display_name = $data_display_name;
      $store->email = $data_email;

      // create the product
      $response = $store->store_register_onLogin();
      if($response=="true"){
          // set response code - 201 created
          http_response_code(201);
          echo json_encode(array("message" => "store registered."));
      }
      else{
          // set response code - 503 service unavailable
          http_response_code(503);
          echo json_encode(array("message" => "Unable to register store.","Sql_response" => $response));
      }
  }
  else{
      // set response code - 400 bad request
      http_response_code(400);
      echo json_encode(array("message" => "Unable to create store account. Data is incomplete."));
  }
?>
