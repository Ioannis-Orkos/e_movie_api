<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/core.php';
include_once '../config/paging.php';
include_once '../config/database.php';
include_once '../objects/movie.php';

  // utilities
  $pagingUtil = new PagingUtil();

  // instantiate database and product object
  $database = new Database();
  $db = $database-> getConnection();


  $mSGID =   isset($_GET['sgid']) ? $_GET['sgid'] : '';
  $mMyMovie= isset($_GET['myMovie']) ? $_GET['myMovie'] : 'no';
  $mGenre =  isset($_GET['genre'])   ? $_GET['genre']  : "all";
    if(empty($mGenre) ||  ($mGenre == "''"))   $mGenre = "all";
  $mSort =   isset($_GET['sort'])    ? $_GET['sort']   : "all";
  $mYear =   isset($_GET['year'])    ? $_GET['year']   : "all";
  $mOrder =  isset($_GET['order'])   ? $_GET['order']  : 'DESC';
  $mType =   isset($_GET['type'])    ? $_GET['type']   : '';

// initialize object
$movies  = new Movies($db);

  $mSGID =   isset($_GET['sgid']) ? $_GET['sgid'] : '';
  $mMyMovie= isset($_GET['myMovie']) ? $_GET['myMovie'] : 'no';
  $mSort =   isset($_GET['sort'])  ? $_GET['sort']  : "all";
  $mYear =   isset($_GET['year'])  ? $_GET['year']  : "all";
  $mOrder =  isset($_GET['order']) ? $_GET['order'] : 'DESC';
  $mType =   isset($_GET['type'])  ? $_GET['type']  : '';
  $mGenre =  isset($_GET['genre']) ? $_GET['genre'] : "all";
  if(empty($mGenre) ||  ($mGenre == "''"))   $mGenre = "all";


  if(!empty($mSGID)){

      // query
      $stmt =  $movies->readPaging_movies_forStores($mSGID,$from_record_num, $records_per_page,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie);
      $num =   $stmt->rowCount();

      if($num>0){

          $movies_arr=array();
          $movies_arr["records"]=array();
          $movies_arr["paging"]=array();

          // retrieve our table contents
          // fetch() is faster than fetchAll()
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
              // extract row
              extract($row);

              $movie_list=array(
                "imdb_id"  => $imdb_id,
                "title" => $title,
                "type" => $type,
                "year" =>  $year,
                "released" =>   $released,
                "runtime" =>  html_entity_decode($runtime),
                "synopsis"  =>  $synopsis,
                "poster"  =>  $poster,
                "certification"  =>  $certification,
                "available" => $available,
                "genres"  =>   explode(",",$genres)
              );

              array_push($movies_arr["records"], $movie_list);
          }


          // include pagination
          $total_rows= $movies->readPagingcount_movies_forStores($mSGID,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie);
          $page_url="{$home_url}movies_forStores/movie_readByPaging.php?";

          // query
          $paging=$pagingUtil->getPaging($page, $total_rows, $records_per_page, $page_url);
          $movies_arr["paging"]=$paging;

          http_response_code(200);
          echo json_encode($movies_arr);

      }
      else{
        // set response code - 204 No Content
        http_response_code(204);
        // tell the user products does not exist
        echo json_encode(array("message" => "No products found."));
      }
  }
   else{
          // set response code - 404 Not found
          http_response_code(404);
          echo json_encode( array("message" => "ID not set") );
   }
?>
