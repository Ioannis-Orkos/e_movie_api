<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// initialize object
$user = new user($db);

// read products will be here

$store_google_id = isset($_POST["sgid"])?$_POST["sgid"]:"";
$req_status = isset($_POST["req_s"])?$_POST["req_s"]:"request";


if( !empty ($store_google_id))
{

// query products
$stmt = $user->store_UserSubs($store_google_id,$req_status);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // products array
    $result_arr=array();
    $result_arr["users_list"]=array();
    $result_arr["users_list_status"]=array();


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);


        $user_results=array(
            "id"  => $user_id,
            "google_id" => $user_google_id,
            "first_name" =>  $user_first_name,
            //"last_name" => html_entity_decode($user_last_name)
            "last_name" =>  html_entity_decode($user_last_name),
            "display_name"  =>  $user_display_name,
            "email"  =>  $user_email,
            "language"  =>  $user_language,
            "geolat"  =>  $user_geolat,
            "geolng"  =>  $user_geolng,
            "address"  =>  $user_address,
            "description"  =>  $user_description,
            "reg_date"  =>  $user_reg_date,
            "prof_img"  =>  $user_prof_img,
            "banner_img"=>$user_banner_img,
            "notif_token"=>$user_notif_token
        );
      $user_status=array(
        "id"  => $user_id,
        "google_id" => $user_google_id,
        "subscription_status"=> $subscription_status
      );


        array_push($result_arr["users_list"], $user_results);
        array_push($result_arr["users_list_status"], $user_status);

    }

    // set response code - 200 OK
    http_response_code(200);
   // show products data in json format
    echo json_encode($result_arr);
}else{

  // set response code - 204 No Content
  http_response_code(204);
  // tell the user products does not exist
  echo json_encode(array("message" => "No products found."));
}
}



?>
