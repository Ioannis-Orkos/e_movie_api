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

  $sgi = isset($_POST["sgi"])?$_POST["sgi"]:"";
  $mids = isset($_POST["mids"])?$_POST["mids"]:"";

  // make sure data is not empty
  if(
      !empty ($mids) &&
      !empty($sgi)
  ){
    $movie_ids = explode(",", $mids);
    $query_data="";


    foreach ($movie_ids as  $value) {

      if(empty ($value)) continue;
      $query_data .= "(@v1,{$value}),";
    }
    $query_data = rtrim($query_data, ", ");

    //var_dump($query_data);

      // create the product
      $response = $store->store_add_movies($query_data,$sgi);
      if($response=="true"){
          // set response code - 201 created
          http_response_code(200);
          echo json_encode(array("message" => "Added."));
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
