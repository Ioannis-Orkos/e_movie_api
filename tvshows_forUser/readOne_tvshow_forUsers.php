<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/tvshow.php';

   // get database connection
   $database = new Database();
   $db = $database-> getConnection();

   // prepare product object
   $tvshow = new Tvshow($db);

   // set ID property of record to read
   $tvshow->id = isset($_GET['id']) ? $_GET['id'] : die();

   // read the details of product to be edited
   $stmt = $tvshow->readOne_tvshow_forUsers();
   $num = $stmt->rowCount();

  // check if more than 0 record found
  if($num>0){

    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $tvshow_ary = array();

      // set values to object properties
      extract($row);

      // create array
      $tvshow_ary = array(
        "imdb_id"  => $imdb_id,
        "type" =>  $type,
        "title" => $title,
        "synopsis"  =>  $synopsis,
        "year" =>  $year,
        "runtime" => $runtime,
        "released" =>   $released,
        "trailer" =>   $trailer,
        "last_updated" =>   $last_updated,
        //"last_name" => html_entity_decode($user_last_name)
        //"fanart"  =>  $fanart,
"status"=>   $status,
"num_seasons"=>   $num_seasons,
"last_episode"=>   $last_episode,
"country"=>   $country,
        "poster"  =>  $poster,
        "votes"  =>  $votes,
        "percentage"  =>  $percentage,
        "certification"  =>  $certification,
        "genres"  =>   explode(",",$genres)   );

      // set response code - 200 OK
      http_response_code(200);
      // make it json format
      echo json_encode($tvshow_ary);
  }

  else{
    // set response code - 204 No Content
    http_response_code(204);
    // tell the user products does not exist
    echo json_encode(array("message" => "No products found."));
  }
?>
