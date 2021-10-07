<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/core.php';
include_once '../util/paging.php';
include_once '../config/database.php';
include_once '../objects/store.php';

// utilities
$pagingUtil = new PagingUtil();

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

$sort_by =isset($_GET["sort"]) ? $_GET["sort"] : "store_reg_date";
$order =isset($_GET["order"]) ? $_GET["order"] : "DESC";
if($order!="DESC" && $order!="ASC") $order="DESC";
// initialize object
$store = new store($db);




// query products
$stmt = $store->store_read_all_byPage($from_record_num, $records_per_page,$sort_by,$order);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // products array
    $products_arr=array();
    $products_arr["records"]=array();
    $products_arr["pageInfo"]=array();

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $product_item=array(
          "id"  => $store_id,
          "google_id" => $store_google_id,
          "first_name" =>  $store_first_name,
          //"last_name" => html_entity_decode($store_last_name)
          "last_name" =>  $store_last_name,
          "display_name"  =>  $store_display_name,
          "email"  =>  $store_email,
          "language"  =>  $store_language,
          "geolat"  =>  $store_geolat,
          "geolng"  =>  $store_geolng,
          "address"  =>  $store_address,
          "description"  =>  $store_decription,
          "reg_date"  =>  $store_reg_date,
          "prof_img"  =>  $store_prof_img,
          "business_name" => $store_business_name,
          "banner_img"=>$store_banner_img,
          //"status"=> $status,
          "notif_token"=>$store_notif_token
        );

        array_push($products_arr["records"], $product_item);
    }


    // include paging
    $total_rows=$store->count();
    $page_url="{$home_url}stores/store_read_all_byPage.php?";
    $paging=$pagingUtil->getPaging($page, $total_rows, $records_per_page, $page_url);
    $products_arr["pageInfo"]=$paging;

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($products_arr);
}

else{

    // set response code - 404 Not found
    http_response_code(404);

    // tell the store products does not exist
    echo json_encode(
        array("message" => "No products found.")
    );
}
?>
