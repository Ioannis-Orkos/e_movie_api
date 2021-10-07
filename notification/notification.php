<?php
class notification{

  // database connection and table name
  private $conn;
  private $apiKey_user =    "AAAAIsXMpXM:APA91bH0DwMxVfJoREj25aB86J3beWxM9kSTbq5XsKBBZlMgNsalIOVe6g7DJo_n9U52NFtFCbxsj4x6jKdzB15289tvCKn1xrAYfaxgyy4eFn9LCxI-o4cF0xSJ58jJUf_cRIiV6mZ3";
  private $apiKey_store =   "AAAA1kYl2zM:APA91bE1aURmH9UGlznkg-RDCdsd6S8hmBBefeHRxxsf01OF2UnPCRe1J9VZcckNC6H_1mVgnOgghsBdKm3MgHQJOE4x6wqb1j5ieLhmfWHgZm8QHbU2BQo4sFyYGfJwymspUgbwRhOz";

  private $url =            "https://fcm.googleapis.com/fcm/send";

  private $store_table =    "stores_tab"; //{$this->store_table}
  private $user_table =     "users_tab";//{$this->user_table}

  // constructor with $db as database connection
  public function __construct($db){
      $this->conn = $db;
  }


  function send_subscriptonNotif_toUser($user_notif_token,$user_display_name,$store_display_name,$store_prof_img,$store_banner_img,$status){

      if(!empty($user_notif_token) && !empty($user_display_name)&& !empty($status) ){
        // Replace with the real client registration IDs
        //$registrationIDs = array($reg_id);
        $registrationIDs= explode( ',', $user_notif_token);

        // Message to be sent
          $fields = array(
            'registration_ids' => $registrationIDs,
           // 'notification' => array(
           //                 "body" => $body,
           //                 "title" =>$title ),
            'data' => array(
                "title" => "$user_display_name","body" => "Request ->" .$store_display_name . "->" .$status ,
                "large_img" => $store_prof_img,
                "type" => "subscrition","status" => $status
           ),
        );

        //var_dump($fields);

        $this->notication_sender_user($fields);
      }

  }

  function send_mangRequestRentNotif_toUser($reg_id,$title,$body,$mtitle,$large_image,$big_image,$status){

      if(!empty($reg_id) && !empty($title) ){
        // Replace with the real client registration IDs
        //$registrationIDs = array($reg_id);
        $registrationIDs= explode( ',', $reg_id);

        // Message to be sent
          $fields = array(
            'registration_ids' => $registrationIDs,
           // 'notification' => array(
           //                 "body" => $body,
           //                 "title" =>$title ),
            'data' => array(
                "title" => "$title","body" => $mtitle. "->". $status ."->". $body,
                "large_img" => $large_image,"big_img" => $big_image,
                "type" => "rent","status" => $status
           ),
        );
        $this->notication_sender_user($fields);
      }

  }



  //from #store# object under user filled funnction->store_subscribeNotification($user_g_id,$store_id)
  function send_subscriptonNotif_toStore($store_notif_token,$title,$body,$large_image){

      if(!empty($store_notif_token) && !empty($title) ){
        // Replace with the real client registration IDs
        //$registrationIDs = array($reg_id);
        $registrationIDs= explode( ',', $store_notif_token);

        // Message to be sent
          $fields = array(
            'registration_ids' => $registrationIDs,
           // 'notification' => array(
           //                 "body" => $body,
           //                 "title" =>$title ),
            'data' => array(
                "title" => "$title","body" => "You have subscription request from  {$body}",
                "large_img" => $large_image,"type" => "subscrition"
           ),
        );
        $this->notication_sender_store($fields);
      }

  }

  //from #store# object under user filled funnction->store_subscribeNotification($user_g_id,$store_id)
  function send_RentNotif_toStore($reg_id,$title,$body,$body2,$large_image,$big_image){

      if(!empty($reg_id) && !empty($title) ){
        // Replace with the real client registration IDs
        //$registrationIDs = array($reg_id);
        $registrationIDs= explode( ',', $reg_id);

        // Message to be sent
          $fields = array(
            'registration_ids' => $registrationIDs,
           // 'notification' => array(
           //                 "body" => $body,
           //                 "title" =>$title ),
            'data' => array(
                "title" => "$title","body" =>  $body2 ." -> ". $body ,
                "large_img" => $large_image,"big_img" => $big_image,
                "type" => "rent"
           ),
        );
        $this->notication_sender_store($fields);
      }

  }


  //general function to send notification
  function notication_sender_store($fields){
      if(!empty($fields)){

          $headers = array(
              'Authorization: key=' . $this->apiKey_store,
              'Content-Type: application/json'
          );

          // Open connection
          $ch = curl_init();

          // Set the URL, number of POST vars, POST data
          curl_setopt( $ch, CURLOPT_URL, $this->url);
          curl_setopt( $ch, CURLOPT_POST, true);
          curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);


          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));

          // Execute post
          $result = curl_exec($ch);

          // Close connection
          curl_close($ch);

          // print the result if you really need to print else neglate thi
          //echo $result;
          //print_r(result);
          //var_dump($result);
         //  var_dump(json_decode($result, true)["success"]);
          return json_decode($result, true)["success"];
        }
    return false;
  }

  //general function to send notification
  function notication_sender_user($fields){
      if(!empty($fields)){

          $headers = array(
              'Authorization: key=' . $this->apiKey_user,
              'Content-Type: application/json'
          );

          // Open connection
          $ch = curl_init();

          // Set the URL, number of POST vars, POST data
          curl_setopt( $ch, CURLOPT_URL, $this->url);
          curl_setopt( $ch, CURLOPT_POST, true);
          curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);


          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));

          // Execute post
          $result = curl_exec($ch);

          // Close connection
          curl_close($ch);

          // print the result if you really need to print else neglate thi
          //echo $result;
          //print_r(result);
          //var_dump($result);
        // var_dump(json_decode($result, true)["success"]);
          return json_decode($result, true)["success"];
        }
    return false;
  }



  }
?>
