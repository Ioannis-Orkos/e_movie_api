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
  $mids_add = isset($_POST["mids_add"])?$_POST["mids_add"]:"";
  $mids_del = isset($_POST["mids_del"])?$_POST["mids_del"]:"";

   // make sure data is not empty
  if(
      (!empty ($mids_add) || !empty ($mids_del) )&&
      !empty($sgi)
  ){


    if(!empty($mids_del)){
    $del_movie_ids = explode(",", $mids_del);
    $query_data_del="";
    foreach ($del_movie_ids as  $value) {
      if(empty ($value)) continue;
      $query_data_del .= " (store_id=@v1 AND movie_id={$value}) OR";
    }
    $query_data_del = rtrim($query_data_del, "OR");
    $query_data_del .= "LIMIT " .(count($del_movie_ids)-1);
    //var_dump($query_data_del);
    $response = $store->store_del_movies($query_data_del,$sgi);
  }

  if(!empty($mids_add)){
    $add_movie_ids = explode(",", $mids_add);
    $query_data_add="";
    foreach ($add_movie_ids as  $value) {

      if(empty ($value)) continue;
      $query_data_add .= "(@v1,{$value}),";
    }
    $query_data_add = rtrim($query_data_add, ", ");
    //var_dump($query_data_add);
    $response = $store->store_add_movies($query_data_add,$sgi);
  }




      if($response=="true"){
          // set response code - 201 created
          http_response_code(200);
          echo json_encode(array("message" => "Changed."));
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
