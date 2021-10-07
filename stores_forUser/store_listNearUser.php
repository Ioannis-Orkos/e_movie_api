<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/store.php';

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

  // initialize object
  $store = new store($db);

  // read products will be here

  $user_google_id = isset($_POST["ugid"])?$_POST["ugid"]:"";
  $user_geolat = isset($_POST["lat"])?$_POST["lat"]:"";
  $user_geolng = isset($_POST["lng"])?$_POST["lng"]:"";


  if( !empty ($user_google_id) &&  !empty ($user_geolat)  &&  !empty ($user_geolng))
  {

    // query products
    $stmt = $store->store_listNearUser($user_google_id,$user_geolat,$user_geolng);
    $num = $stmt->rowCount();

    // check if more than 0 record found
    if($num>0){

        // products array
        $result_arr=array();
        $result_arr["stores_list"]=array();
        $result_arr["stores_list_status"]=array();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);


            $store_results=array(
                "id"  => $store_id,
                "google_id" => $store_google_id,
                "first_name" =>  $store_first_name,
                //"last_name" => html_entity_decode($user_last_name)
                "last_name" =>  html_entity_decode($store_last_name),
                "display_name"  =>  $store_display_name,
                "email"  =>  $store_email,
                "language"  =>  $store_language,
                "geolat"  =>  $store_geolat,
                "geolng"  =>  $store_geolng,
                "address"  =>  $store_address,
                "description"  =>  $store_description,
                "reg_date"  =>  $store_reg_date,
                "prof_img"  =>  $store_prof_img,
                "business_name" => $store_business_name,
                "banner_img"=>$store_banner_img,
                "notif_token"=>$store_notif_token
            );
          $store_status=array(
            "id"  => $store_id,
            "google_id" => $store_google_id,
            "subscription_status"=> $subscription_status
          );

            array_push($result_arr["stores_list"], $store_results);
            array_push($result_arr["stores_list_status"], $store_status);

        }

        // set response code - 200 OK
        http_response_code(200);
       // show products data in json format
        echo json_encode($result_arr);

  }else{
      // set response code - 204 No Content
      http_response_code(204);
      // tell the user products does not exist
        echo json_encode(
            array("message" => "No products found.")
        );
  }
}



?>
