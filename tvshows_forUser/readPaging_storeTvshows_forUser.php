<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/core.php';
include_once '../config/paging.php';
include_once '../config/database.php';
include_once '../objects/tvshow.php';

// utilities
$pagingUtil = new PagingUtil();

// instantiate database and product object
$database = new Database();
$db = $database-> getConnection();


$mGenre = isset($_GET['genre']) ? $_GET['genre'] : "all";
if(empty($mGenre) ||  ($mGenre == "''"))   $mGenre = "all";
$mSort = isset($_GET['sort']) ? $_GET['sort'] :"all";
$mYear =  isset($_GET['year']) ? $_GET['year'] :"all";
$mOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$mType = isset($_GET['type']) ? $_GET['type'] : '';
$mStoreId = isset($_GET['storeId']) ? $_GET['storeId'] : '';

//enum('western', 'local', 'others')



// initialize object
$tvshow = new Tvshow($db);

// query products
$stmt =  $tvshow->readPaging_storeTvshows_forUser($from_record_num,$records_per_page,$mStoreId,$mType,$mGenre,$mSort,$mYear,$mOrder);
$num =   $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // products array
    $tvshow_arr=array();
    $tvshow_arr["records"]=array();
    $tvshow_arr["paging"]=array();

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $movie_one=array(
          "imdb_id"  => $imdb_id,
          "title" => $title,
          "type" => $type,
          "year" =>  $year,
          "released" =>   $released,
          "last_updated" =>   $last_updated,
          "runtime" =>  html_entity_decode($runtime),
          "synopsis"  =>  $synopsis,
          "poster"  =>  $poster,
          "certification"  =>  $certification,
          "genres"  =>   explode(",",$genres)
        );

        array_push($tvshow_arr["records"], $movie_one);
    }


    // include paging
    $total_rows= $tvshow->readPagingcount_storeTvshows_forUser($mStoreId,$mType,$mGenre,$mSort,$mYear,$mOrder);
    $page_url="{$home_url}tvshow/tvshow_listStoreByPaging.php?";
    $paging=$pagingUtil->getPaging($page, $total_rows, $records_per_page, $page_url);
    $tvshow_arr["paging"]=$paging;
    //  $products_arr["total_page"]=$paging["total_page"];
    //  $products_arr["total_rows"]=$paging["total_rows"];
    //  $products_arr["current_page"]=$paging["current_page"];

    // set response code - 200 OK
    http_response_code(200);
    echo json_encode($tvshow_arr);

}

else{
  // set response code - 204 No Content
  http_response_code(204);
  // tell the user products does not exist
  echo json_encode(array("message" => "No products found."));
    
}
?>
