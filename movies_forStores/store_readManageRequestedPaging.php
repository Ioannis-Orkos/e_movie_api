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
$db = $database->getConnection();

$user_g_id = isset($_GET["sgid"])?$_GET["sgid"]:"";
$status = isset($_GET["status"])?$_GET["status"]:"pending";

// initialize object
$movies = new Movies($db);

// query products
$stmt = $movies->store_readRequestedMoviesPaging($from_record_num, $records_per_page,$user_g_id,$status);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // products array
    $products_arr=array();

    $products_arr["pageInfo"]=array();
    $products_arr["results"]=array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $result_data["status"]=array(
            "movie_id" => $imdb_id,
            "store_id"  => $store_id,
            "user_id"  => $user_id,
            "req_status" =>  $req_status,
            "req_date" =>  $req_date,
            "movie_status" =>  $movie_status,
            "req_store_msg"=>$req_store_msg,
            "req_user_msg"=>$req_user_msg
        );

        $result_data["users_result"]=array(
            "id"  => $user_id,
            "google_id" => $user_google_id,
            "first_name" =>  $user_first_name,
            "last_name" =>  $user_last_name,
            "display_name"  =>  $user_display_name,
            "email"  =>  $user_email,
            "language"  =>  $user_language,
            "geolat"  =>  $user_geolat,
            "geolng"  =>  $user_geolng,
            "address"  =>  $user_address,
            //"description"  =>  $user_description,
            "reg_date"  =>  $user_reg_date,
            "prof_img"  =>  $user_prof_img
        );


        $result_data["stores_result"]=array(
          "id"  => $store_id,
          "google_id" => $store_google_id,
          "first_name" =>  $store_first_name,
          "last_name" =>  $store_last_name,
          "display_name"  =>  $store_display_name,
          "email"  =>  $store_email,
          "language"  =>  $store_language,
          "geolat"  =>  $store_geolat,
          "geolng"  =>  $store_geolng,
          "address"  =>  $store_address,
          //"description"  =>  $store_decription,
          "reg_date"  =>  $store_reg_date,
          "prof_img"  =>  $store_prof_img,
          "business_name" => $store_business_name,
          "banner_img"=>$store_banner_img,
          "notif_token"=>$store_notif_token
        );

        $result_data["movies_result"]=array(
          "imdb_id"  => $imdb_id,
          "title" => $title,
          "year" =>  $year,
          "released" =>   $released,
          "runtime" =>  html_entity_decode($runtime),
          "description"  =>  $synopsis,
          "poster"  =>  $poster,
          "certification"  =>  $certification,
          //"genres"  =>   explode(",",$genres)
        );


      //  array_push($products_arr[],$result_data);
        array_push($products_arr["results"],$result_data);
    }


    // include paging
    $total_rows=$movies->store_readRequestedMoviesCount($user_g_id,$status);
    $page_url="{$home_url}movies_forStores/store_readManageRequestedPaging.php?";
    $paging=$pagingUtil->getPaging($page, $total_rows, $records_per_page, $page_url);

    $products_arr["pageInfo"]=$paging;

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($products_arr);


}

else{

  // set response code - 204 No Content
  http_response_code(204);
  // tell the user products does not exist
  echo json_encode(array("message" => "No products found."));
}
?>
