<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/movie.php';

// get database connection
$database = new Database();
$db = $database-> getConnection();

// prepare product object
$movies = new Movies($db);

// set ID property of record to read
$movies->id = isset($_GET['id']) ? $_GET['id'] : die();

// read the details of product to be edited
 $stmt = $movies->readOne_movie_forUsers();
 $num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

  // get retrieved row
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $movie_ary = array();
  // set values to object properties

    extract($row);

    // create array
    $movie_ary = array(
      "imdb_id"  => $imdb_id,
      "type" =>  $type,
      "title" => $title,
      "synopsis"  =>  $synopsis,
      "year" =>  $year,
      "runtime" => $runtime,
      "released" =>   $released,
      "trailer" =>   $trailer,
      //"last_name" => html_entity_decode($user_last_name)
      //"fanart"  =>  $fanart,
      "poster"  =>  $poster,
      "votes"  =>  $votes,
      "percentage"  =>  $percentage,
      "certification"  =>  $certification,
      "genres"  =>   explode(",",$genres)   );

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($movie_ary);
}

else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("message" => "Product does not exist."));
}
?>
