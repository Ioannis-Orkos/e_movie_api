<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
//include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/store.php';

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// initialize object
$store = new store($db);

// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";

// query products
$stmt = $store->store_search($keywords);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // products array
   $products_arr["records"]=array();
   $products_arr["total_rows"]=array();

    // retrieve our table contents
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $store_arr = array(
          "id"  => $store_id,
          "google_id" => $store_google_id,
          "first_name" =>  $store_first_name,
          //"last_name" => html_entity_decode($store_last_name)
          "last_name" =>  html_entity_decode($store_last_name),
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
          "banner_img"=>$store_banner_img

        );


        array_push($products_arr["records"], $store_arr);

    }

   array_push($products_arr["total_rows"], $num);
    // set response code - 200 OK
    http_response_code(200);

    // show products data
    echo json_encode($products_arr);
}

else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the store no products found
    echo json_encode(
        array("message" => "No products found.")
    );
}
?>
