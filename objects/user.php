<?php
class user{

    // database connection and table name
    private $conn;

    private $app_table =                "app_info_tab";//{$this->app_table}

    private $users_table =                "users_tab";//{$this->users_table}
    private $store_table =                "stores_tab"; //{$this->store_table}
    private $subscripton_table =          "user_subscripton_tab";  //{$this->table_relation}

    private $movies_table  =              "movies_tab";
    private $movies_genres_table =        "movies_genres_tab";
    private $requested_movie_table=       "requested_movies_tab"; //{$this->requested_movie_table}

    private $tvshow_table  =              "tvshow_tab";
    private $tvshow_genres_table =        "tvshow_genres_tab";
    private $requested_tvshow_table=      "requested_tvshow_tab"; //{$this->requested_movie_table}


    // user properties
    public $id;
    public $google_id;
    public $first_name;
    public $last_name;
    public $display_name;
    public $email;
    public $language;
    public $prof_img;
    public $banner_img;
    public $description;
    public $address;
    public $postcode;
    public $geolng;
    public $geolat;
    public $notif_token;
    public $reg_date;


    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    #User section---------------------------------------------------------------------------------------------------------------------

      // create users on first login
      function user_register_onLogin(){

        try {
            // query to insert record
            $query = "INSERT IGNORE INTO {$this->users_table}
                      (user_google_id, user_first_name, user_last_name,
                      user_display_name,user_email) values (? ,? ,? ,? ,?)";

            // prepare query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->google_id=htmlspecialchars(strip_tags($this->google_id));
            $this->first_name=htmlspecialchars(strip_tags($this->first_name));
            $this->last_name=htmlspecialchars(strip_tags($this->last_name));
            $this->display_name =htmlspecialchars(strip_tags($this->display_name));
            $this->email=htmlspecialchars(strip_tags($this->email));

            // bind values
            $stmt->bindParam(1, $this->google_id);
            $stmt->bindParam(2, $this->first_name);
            $stmt->bindParam(3, $this->last_name);
            $stmt->bindParam(4, $this->display_name);
            $stmt->bindParam(5, $this->email);

                // execute query
                if  ($stmt->execute()) return "true";

        }catch(PDOException $exception){
            return  $exception->getCode();
        }
      }


      // used when filling up the update product form
      function user_read_appInfo(){

          $app_id = 1;

          //$this->google_id=htmlspecialchars(strip_tags($this->google_id));

          // query to read single record
          $query = "SELECT *
                    FROM   {$this->app_table}
                      WHERE app_read ={$app_id}";

          // prepare query statement
          $stmt = $this->conn->prepare( $query );

          // execute query
          $stmt->execute();

        return $stmt;
      }

      // used when filling up the update product form
      function user_read_single_byID(){

          $app_id = 1;
          // sanitize
          $this->id=htmlspecialchars(strip_tags($this->id));
          //$this->google_id=htmlspecialchars(strip_tags($this->google_id));

          // query to read single record
          $query = "SELECT *
                    FROM   {$this->users_table}
                    LEFT JOIN  {$this->app_table} ON {$this->app_table}.app_read ={$app_id}
                      WHERE user_google_id = ?  OR user_id = ?
                    LIMIT 0,1";

          // prepare query statement
          $stmt = $this->conn->prepare( $query );



          // bind id of product to be updated
          $stmt->bindParam(1, $this->id);
          $stmt->bindParam(2, $this->id, PDO::PARAM_INT);

          // execute query
          $stmt->execute();

        return $stmt;
      }

      // update the user
      function user_update_bywhat($what){

        // sanitize
        $what=htmlspecialchars(strip_tags($what));
        $this->google_id=htmlspecialchars(strip_tags($this->google_id));
        $this->first_name=htmlspecialchars(strip_tags($this->first_name));
        $this->last_name=htmlspecialchars(strip_tags($this->last_name));
        $this->display_name =htmlspecialchars(strip_tags($this->display_name));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->geolat=htmlspecialchars(strip_tags($this->geolat));
        $this->geolng=htmlspecialchars(strip_tags($this->geolng));
        $this->language=htmlspecialchars(strip_tags($this->language));
        $this->address=htmlspecialchars(strip_tags($this->address));
        $this->postcode=htmlspecialchars(strip_tags($this->postcode));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->notif_token=htmlspecialchars(strip_tags($this->notif_token));
        $this->prof_img=htmlspecialchars(strip_tags($this->prof_img));

        try{
          switch ($what) {
              case "location":
                     $query = "UPDATE {$this->users_table} SET
                               user_geolng =:geolng,user_geolat =:geolat,user_address =:address,user_postcode =:postcode
                               WHERE    user_google_id =:google_id LIMIT 1";

                         // prepare query statement
                         $stmt = $this->conn->prepare($query);
                          // bind id of product to be updated
                         $stmt->bindParam(":google_id", $this->google_id);
                         $stmt->bindParam(":geolat", $this->geolat);
                         $stmt->bindParam(":geolng", $this->geolng);
                         $stmt->bindParam(":address", $this->address);
                         $stmt->bindParam(":postcode", $this->postcode);
                     break;
                case "description":
                     $query = "UPDATE {$this->users_table} SET   user_description =:description
                             WHERE       user_google_id =:google_id LIMIT 1";
                             // prepare query statement
                             $stmt = $this->conn->prepare($query);
                             // bind id of product to be updated
                             $stmt->bindParam(":google_id", $this->google_id);
                             $stmt->bindParam(":description", $this->description);
                      break;
                 case "display_name":
                           $query = "UPDATE {$this->users_table} SET   user_display_name =:display_name
                                   WHERE       user_google_id =:google_id LIMIT 1";
                                   // prepare query statement
                                   $stmt = $this->conn->prepare($query);
                                    // bind id of product to be updated
                                   $stmt->bindParam(":google_id", $this->google_id);
                                   $stmt->bindParam(":display_name", $this->display_name);
                      break;
                 case "update_detail":
                           $query = "UPDATE {$this->users_table} SET   user_display_name =:display_name,
                                   user_description =:description, user_language =:language
                                   WHERE       user_google_id =:google_id LIMIT 1";
                                   // prepare query statement
                                   $stmt = $this->conn->prepare($query);
                                    // bind id of product to be updated
                                   $stmt->bindParam(":google_id", $this->google_id);
                                   $stmt->bindParam(":display_name", $this->display_name);
                                   $stmt->bindParam(":description", $this->description);
                                   $stmt->bindParam(":language", $this->language);
                      break;
                 case "notification":
                         $query = "UPDATE {$this->users_table} SET   user_notif_token =:notif_token
                                 WHERE user_google_id =:google_id LIMIT 1";
                                 // prepare query statement
                                 $stmt = $this->conn->prepare($query);
                                  // bind id of product to be updated
                                 $stmt->bindParam(":google_id", $this->google_id);
                                 $stmt->bindParam(":notif_token", $this->notif_token);
                        break;
                case "language":
                        $query = "UPDATE {$this->users_table} SET   user_language =:language
                                WHERE user_google_id =:google_id LIMIT 1";
                                // prepare query statement
                                $stmt = $this->conn->prepare($query);
                                 // bind id of product to be updated
                                $stmt->bindParam(":google_id", $this->google_id);
                                $stmt->bindParam(":language", $this->language);
                       break;
                  case "prof_img":
                         $query = "UPDATE {$this->users_table} SET   user_prof_img =:prof_img
                                   WHERE user_google_id =:google_id LIMIT 1";
                                   // prepare query statement
                                   $stmt = $this->conn->prepare($query);
                                   // bind id of product to be updated
                                   $stmt->bindParam(":google_id", $this->google_id);
                                   $stmt->bindParam(":prof_img", $this->prof_img);
                         break;
                  default:
                        return false;
                        break;

                  //code to be executed if n is different from all labels;
          }
           // update query
           if($stmt->execute()){
             //var_dump($stmt->rowCount());
             //if($stmt->rowCount()==0) return false;
             return true;
           }else {
             return false;
           }

          }catch(PDOException $exception){
               return false;
              //echo "Connection error: " . $exception->getMessage();
              //echo $exception->getCode();
            //  return  $exception->getCode();
          }

      }


      public function user_readMovieTransactionPaging($from_record_num, $records_per_page,$ugid,$status){

            $ugid=htmlspecialchars(strip_tags($ugid));
            $status=htmlspecialchars(strip_tags($status));

                  $query2=" ";

                  if($status == 'rejected')       $query2 = " AND {$this->requested_movie_table}.req_status = 'rejected'";
                  else if ($status == 'pending')  $query2 = " AND {$this->requested_movie_table}.req_status = 'pending'";
                  else if ($status == 'ready')    $query2 = " AND {$this->requested_movie_table}.req_status = 'ready'";


                  $query = "SELECT *
                            ,{$this->requested_movie_table}.req_store_msg,{$this->requested_movie_table}.req_user_msg


                            FROM {$this->requested_movie_table}
                            LEFT JOIN {$this->users_table}    ON {$this->users_table}.`user_google_id` = :google_id
                            LEFT JOIN {$this->store_table}   ON {$this->store_table}.`store_id` = {$this->requested_movie_table}.`store_id`
                            LEFT JOIN {$this->movies_table} ON {$this->movies_table}.`imdb_id` = {$this->requested_movie_table}.`movie_id`


                            WHERE  {$this->requested_movie_table}.`user_id`= {$this->users_table}.`user_id`
                                        {$query2}
                            ORDER BY {$this->requested_movie_table}.req_date  DESC
                            LIMIT :from , :to ";

            $stmt = $this->conn->prepare( $query );

            // bind variable values
            $stmt->bindParam(":google_id", $ugid);
            $stmt->bindParam(":from", $from_record_num, PDO::PARAM_INT);
            $stmt->bindParam(":to", $records_per_page, PDO::PARAM_INT);

           // execute query
           $stmt->execute();

        return  $stmt;
      }
      public function user_readMovieTransactionCount($ugid,$status){

        $ugid=htmlspecialchars(strip_tags($ugid));
        $status=htmlspecialchars(strip_tags($status));

          $query2=" ";

          if($status == 'rejected')       $query2 = " AND {$this->requested_movie_table}.req_status = 'rejected'";
          else if ($status == 'pending')  $query2 = " AND {$this->requested_movie_table}.req_status = 'pending'";
          else if ($status == 'ready')    $query2 = " AND {$this->requested_movie_table}.req_status = 'ready'";

          $query = "SELECT COUNT(*) as total_rows
                    FROM {$this->requested_movie_table}
                    LEFT JOIN {$this->users_table}    ON {$this->users_table}.`user_google_id` = :google_id
                    LEFT JOIN {$this->store_table}   ON {$this->store_table}.`store_id` = {$this->requested_movie_table}.`store_id`
                    LEFT JOIN {$this->movies_table} ON {$this->movies_table}.`imdb_id` = {$this->requested_movie_table}.`movie_id`

                    WHERE  {$this->requested_movie_table}.`user_id`= {$this->users_table}.`user_id`
                                {$query2} ";

                $stmt = $this->conn->prepare( $query );


          $stmt->bindParam(":google_id", $ugid);

          // execute query
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          //$row = $stmt->rowCount();
          // return values from database
          //return $row;
       return $row['total_rows'];
      }


      public function user_readTvshowTransactionPaging($from_record_num, $records_per_page,$ugid,$status){

        $ugid=htmlspecialchars(strip_tags($ugid));
        $status=htmlspecialchars(strip_tags($status));

                $query2=" ";

                if($status == 'rejected')       $query2 = " AND {$this->requested_tvshow_table}.req_status = 'rejected'";
                else if ($status == 'pending')  $query2 = " AND {$this->requested_tvshow_table}.req_status = 'pending'";
                else if ($status == 'ready')    $query2 = " AND {$this->requested_tvshow_table}.req_status = 'ready'";


                $query = "SELECT *
                          ,{$this->requested_tvshow_table}.req_store_msg,{$this->requested_tvshow_table}.req_user_msg


                          FROM {$this->requested_tvshow_table}
                          LEFT JOIN {$this->users_table}    ON {$this->users_table}.`user_google_id` = :google_id
                          LEFT JOIN {$this->store_table}   ON {$this->store_table}.`store_id` = {$this->requested_tvshow_table}.`store_id`
                          LEFT JOIN {$this->tvshow_table} ON {$this->tvshow_table}.`imdb_id` = {$this->requested_tvshow_table}.`tvshow_id`


                          WHERE  {$this->requested_tvshow_table}.`user_id`= {$this->users_table}.`user_id`
                                      {$query2}
                          ORDER BY {$this->requested_tvshow_table}.req_date  DESC
                          LIMIT :from , :to ";

          $stmt = $this->conn->prepare( $query );
                          // bind variable values
          $stmt->bindParam(":google_id", $ugid);
          $stmt->bindParam(":from", $from_record_num, PDO::PARAM_INT);
          $stmt->bindParam(":to", $records_per_page, PDO::PARAM_INT);

         // execute query
         $stmt->execute();

         return  $stmt;
       }
      public function user_readTvshowTransactionCount($ugid,$status){

        $ugid=htmlspecialchars(strip_tags($ugid));
        $status=htmlspecialchars(strip_tags($status));

          $query2=" ";

          if($status == 'rejected')       $query2 = " AND {$this->requested_tvshow_table}.req_status = 'rejected'";
          else if ($status == 'pending')  $query2 = " AND {$this->requested_tvshow_table}.req_status = 'pending'";
          else if ($status == 'ready')    $query2 = " AND {$this->requested_tvshow_table}.req_status = 'ready'";

          $query = "SELECT COUNT(*) as total_rows
                    FROM {$this->requested_tvshow_table}
                    LEFT JOIN {$this->users_table}    ON {$this->users_table}.`user_google_id` = :google_id
                    LEFT JOIN {$this->store_table}   ON {$this->store_table}.`store_id` = {$this->requested_tvshow_table}.`store_id`
                    LEFT JOIN {$this->tvshow_table} ON {$this->tvshow_table}.`imdb_id` = {$this->requested_tvshow_table}.`tvshow_id`

                    WHERE    {$this->requested_tvshow_table}.`user_id`= {$this->users_table}.`user_id`
                             {$query2} ";

                $stmt = $this->conn->prepare( $query );


          $stmt->bindParam(":google_id", $ugid);

          // execute query
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          //$row = $stmt->rowCount();
          // return values from database
          //return $row;
       return $row['total_rows'];
      }

    #End User section-----------------------------------------------------------------------------------------------------------------



    #Store section---------------------------------------------------------------------------------------------------------------------

      function store_manage_request($user_id,$store_g_id,$req_status){
          // select all query
          $query = "UPDATE {$this->subscripton_table}
                          SET {$this->subscripton_table}.subscription_status = :req_status
                    WHERE ({$this->subscripton_table}.store_id=
                           (SELECT {$this->store_table}.store_id FROM {$this->store_table}
                            WHERE {$this->store_table}.store_google_id = :s_g_id)&&
                            {$this->subscripton_table}.user_id=:u_id
                          ) ";

       $stmt = $this->conn->prepare($query);
        // bind id of product to be updated
       $stmt->bindParam(":u_id", $user_id);
       $stmt->bindParam(":s_g_id", $store_g_id);
       $stmt->bindParam(":req_status", $req_status);
          // execute query
       $stmt->execute();

       return $stmt;
      }

      function store_manage_subscritionNotification($user_id,$store_g_id,$req_status){

        $query = "SELECT {$this->users_table}.user_notif_token,{$this->users_table}.user_display_name,
                         {$this->users_table}.user_prof_img,
                         {$this->store_table}.store_display_name as sdn,{$this->store_table}.store_banner_img,
                         {$this->store_table}.store_prof_img

                  FROM {$this->store_table}
                  LEFT JOIN {$this->users_table} ON {$user_id} = {$this->users_table}.user_id
                  WHERE {$this->store_table}.store_google_id = {$store_g_id}";

        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        //var_dump($row);
       $notification = new notification($this->conn);
       $notification->send_subscriptonNotif_toUser($user_notif_token,$user_display_name,$sdn,$store_prof_img,$store_banner_img,$req_status);

      }

      //list subscribed users for store
      function store_UserSubs($sgid,$r_status='request'){

        // select all query
          $query = "SELECT
               {$this->users_table}.user_id ,{$this->users_table}.user_google_id,{$this->users_table}.user_display_name
              ,{$this->users_table}.user_first_name,{$this->users_table}.user_last_name
              ,{$this->users_table}.user_email,{$this->users_table}.user_geolat,{$this->users_table}.user_geolng
              ,{$this->users_table}.user_description,{$this->users_table}.user_prof_img,{$this->users_table}.user_banner_img
              ,{$this->users_table}.user_reg_date
              ,{$this->users_table}.user_language,{$this->users_table}.user_address,{$this->users_table}.user_notif_token


              ,{$this->subscripton_table}.subscription_status AS subscription_status

             FROM {$this->users_table}
             LEFT JOIN {$this->store_table} ON {$this->store_table}.store_google_id = :google_id
             RIGHT JOIN {$this->subscripton_table} ON ({$this->users_table}.user_id = {$this->subscripton_table}.user_id &&
                                                      {$this->subscripton_table}.store_id ={$this->store_table}.store_id &&
                                                     {$this->subscripton_table}.subscription_status = '{$r_status}')
             WHERE    {$this->users_table}.user_id={$this->users_table}.user_id

             ORDER BY {$this->subscripton_table}.user_request_date";

              $stmt = $this->conn->prepare($query);
              // bind id of user to be updated
              $stmt->bindParam(":google_id", $sgid);
              $stmt->execute();

              return $stmt;

      }


    #End Store section-----------------------------------------------------------------------------------------------------------------



}
?>
