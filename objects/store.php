<?php
class store{

    // database connection and table name
    private $conn;

    private $store_table =               "stores_tab"; //{$this->store_table}
    private $user_table =                "users_tab";//{$this->user_table}
    private $subscripton_table =         "user_subscripton_tab";  //{$this->table_relation}

    private $movies_table =              "movies_tab";//{$this->movies_table}
    private $requested_movie_table=      "requested_movies_tab"; //{$this->requested_movie_table}
    private $store_tableMovies =         "stores_movies_tab";//{$this->store_tableMovies}

    private $tvshow_table  =              "tvshow_tab";
    private $requested_tvshow_table=      "requested_tvshow_tab"; //{$this->requested_movie_table}
    private $store_tableTvshow =          "stores_tvshows_tab";//{$this->store_tableMovies}





    // store account properties
    public $id;
    public $google_id;
    public $first_name;
    public $last_name;
    public $display_name;
    public $email;
    public $language;
    public $prof_img;
    public $description;
    public $business_name;
    public $banner_img;
    public $address;
    public $postcode;
    public $geolng;
    public $geolat;
    public $reg_date;
    public $notif_token;

    public $store_user_relation_status;
    public $store_have_movie;
    public $store_user_movie_status;


    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }


    #User section---------------------------------------------------------------------------------------------------------------------

      function store_listNearUser($uid,$lat,$lng){

        // select all query
          $query = "SELECT
               {$this->store_table}.store_id ,{$this->store_table}.store_google_id,{$this->store_table}.store_display_name
              ,{$this->store_table}.store_first_name,{$this->store_table}.store_last_name
              ,{$this->store_table}.store_email,{$this->store_table}.store_geolat,{$this->store_table}.store_geolng
              ,{$this->store_table}.store_description,{$this->store_table}.store_prof_img,{$this->store_table}.store_banner_img
              ,{$this->store_table}.store_business_name,{$this->store_table}.store_reg_date
              ,{$this->store_table}.store_language,{$this->store_table}.store_address,{$this->store_table}.store_notif_token

              , {$this->user_table}.user_id  As user_id
              ,{$this->subscripton_table}.subscription_status AS subscription_status

             FROM {$this->store_table}
             LEFT JOIN {$this->user_table} ON {$this->user_table}.user_google_id = :google_id
             LEFT JOIN {$this->subscripton_table} ON ({$this->store_table}.store_id = {$this->subscripton_table}.store_id &&
                                                      {$this->subscripton_table}.user_id ={$this->user_table}.user_id)

             WHERE ({$this->store_table}.store_geolat  BETWEEN :lat-50.0 AND :lat+100.0 &&
                    {$this->store_table}.store_geolng  BETWEEN :lng-50.0 AND :lng+100.0 )

             GROUP BY store_id";

              $stmt = $this->conn->prepare($query);
              // bind id of user to be updated
              $stmt->bindParam(":google_id", $uid);
              $stmt->bindParam(":lat", $lat);
              $stmt->bindParam(":lng", $lng);
              $stmt->execute();

              return $stmt;


      }

      function store_listUserSubs($ugid){

        $ugid=htmlspecialchars(strip_tags($ugid));

          // select all query
           $query = "SELECT
           {$this->store_table}.store_id ,{$this->store_table}.store_google_id,{$this->store_table}.store_display_name
          ,{$this->store_table}.store_first_name,{$this->store_table}.store_last_name,{$this->store_table}.store_description
          ,{$this->store_table}.store_email,{$this->store_table}.store_language,{$this->store_table}.store_geolat
          ,{$this->store_table}.store_prof_img,{$this->store_table}.store_banner_img
          ,{$this->store_table}.store_business_name,{$this->store_table}.store_reg_date
          ,{$this->store_table}.store_geolng,{$this->store_table}.store_address,{$this->store_table}.store_notif_token

          ,{$this->subscripton_table}.subscription_status AS subscription_status


        FROM {$this->store_table}
        RIGHT JOIN (SELECT * FROM {$this->subscripton_table}
                             WHERE ({$this->subscripton_table}.user_id=(
                                    SELECT {$this->user_table}.user_id FROM {$this->user_table}
                                    WHERE {$this->user_table}.user_google_id = :google_id )
                                          && {$this->subscripton_table}.subscription_status != 'rejected'
                   )) AS {$this->subscripton_table}
        ON {$this->store_table}.store_id = {$this->subscripton_table}.store_id";


         $stmt = $this->conn->prepare($query);
          // bind id of product to be updated
         $stmt->bindParam(":google_id", $ugid);
            // execute query
         $stmt->execute();
        return $stmt;
      }

      function store_unsubscribe($user_g_id,$store_id){

        $user_g_id=htmlspecialchars(strip_tags($user_g_id));
        $store_id=htmlspecialchars(strip_tags($store_id));

          // select all query
        $query = "DELETE FROM {$this->subscripton_table}
      	WHERE {$this->subscripton_table} .
      	user_id = (SELECT {$this->user_table}.user_id FROM {$this->user_table} WHERE {$this->user_table}.user_google_id = ?)
      	AND {$this->subscripton_table}.store_id = ?
        LIMIT 1";

              $stmt = $this->conn->prepare($query);
              // bind id of product to be updated
              $stmt->bindParam(1, $user_g_id);
              $stmt->bindParam(2, $store_id);
              // execute query
            $stmt->execute();

          return $stmt;
      }

      function store_subscribe($user_g_id,$store_id){

        $user_g_id=htmlspecialchars(strip_tags($user_g_id));
        $store_id=htmlspecialchars(strip_tags($store_id));

          // select all query
          $query = "INSERT IGNORE INTO {$this->subscripton_table} (user_id, store_id,
                    user_request_msg, user_request_date)
                    VALUES (
                        (SELECT {$this->user_table}.user_id FROM {$this->user_table}
                        WHERE {$this->user_table}.user_google_id = ?),
                        ?, '', CURRENT_TIMESTAMP ) ";

       $stmt = $this->conn->prepare($query);
        // bind id of product to be updated
       $stmt->bindParam(1, $user_g_id);
       $stmt->bindParam(2, $store_id);
          // execute query
       $stmt->execute();

       //return $stmt;
       return $stmt;
      }

      function store_subscribeNotification($user_g_id,$store_id){

        $query = "SELECT {$this->store_table}.store_notif_token,
                         {$this->store_table}.store_display_name as  sdn,{$this->store_table}.store_banner_img,
                         {$this->store_table}.store_prof_img,
                         {$this->user_table}.user_display_name, {$this->user_table}.user_prof_img

                  FROM {$this->store_table}
                  LEFT JOIN {$this->user_table} ON {$user_g_id} = {$this->user_table}.user_google_id
                  WHERE {$this->store_table}.store_id = {$store_id}";

        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        //var_dump($row);
       $notification = new notification($this->conn);
       $notification->send_subscriptonNotif_toStore($store_notif_token,$sdn,$user_display_name,$user_prof_img);

      }


      //check for the stores that have connection with the user and if they have the movie
      function store_listForRentMovie($user_google_id,$movie_id,$store_id){
         $query2 = "";

         if($store_id!=-1)     $query2 = "and {$this->store_table}.store_id={$store_id}";

          // select all query
          $query = "SELECT         {$this->store_table}.*
                                ,( {$this->subscripton_table}.subscription_status )     As subscription_status
                                ,  {$this->subscripton_table}.user_id                   As user_id

              ,(CASE WHEN {$this->requested_movie_table}.req_status IS NOT NULL then {$this->requested_movie_table}.req_status
                                                                                    ELSE  'unknown'  END) As movie_status
              ,(CASE WHEN {$this->store_tableMovies}.movie_id=:movie_id  then 'yes' ELSE  'no'  END) As movie_available
              ,{$this->requested_movie_table}.req_store_msg,{$this->requested_movie_table}.req_user_msg


              FROM       {$this->store_table}
              RIGHT JOIN {$this->subscripton_table}  ON ({$this->store_table}.store_id = {$this->subscripton_table}.store_id )
              Left JOIN  {$this->store_tableMovies}  ON ({$this->subscripton_table}.store_id = {$this->store_tableMovies} .store_id
                                                     AND {$this->store_tableMovies}.movie_id=:movie_id)
              Left JOIN  {$this->requested_movie_table} ON ({$this->requested_movie_table}.user_id = {$this->subscripton_table}.user_id
                                                        AND {$this->subscripton_table}.store_id =  {$this->requested_movie_table}.store_id
                                                        and {$this->requested_movie_table}.movie_id=:movie_id)

              WHERE( {$this->subscripton_table}.user_id=(SELECT {$this->user_table}.user_id
                                                                  FROM {$this->user_table}
                                                                  WHERE {$this->user_table}.user_google_id=:google_id)
                                                                	and  {$this->subscripton_table}.subscription_status ='subscribed'
                                                                  {$query2}  )";


          $stmt = $this->conn->prepare($query);


          // bind id of product to be updated
          $stmt->bindParam(":google_id", $user_google_id);
          $stmt->bindParam(":movie_id", $movie_id);
          // execute query
          $stmt->execute();

          return $stmt;
      }

      function store_requestMovieRent($movie_id,$user_g_id,$store_id,$user_msg){

          // select all query
          $query = "INSERT IGNORE INTO {$this->requested_movie_table}
                                       (`movie_id`, `user_id`,
                                       `store_id`, `req_status`, `req_user_msg`,`req_date`)
                           VALUES (  :movie_id,    (SELECT {$this->user_table}.user_id
                                                            FROM {$this->user_table}
                                                            WHERE {$this->user_table}.user_google_id = :user_g_id),
                                     :store_id, 'pending', :user_msg ,CURRENT_TIMESTAMP ) ";

         $stmt = $this->conn->prepare($query);
        // bind id of product to be updated
        //$stmt->bindParam(":movie_t", $movie_type);
         $stmt->bindParam(":movie_id", $movie_id);
         $stmt->bindParam(":user_g_id", $user_g_id);
         $stmt->bindParam(":store_id", $store_id);
         $stmt->bindParam(":user_msg", $user_msg);


          // execute query
         $stmt->execute();

        return $stmt;
      }

      function store_requestMovieRentNotification($user_g_id,$store_id,$mid){

        $query = "SELECT {$this->store_table}.store_notif_token,
                         {$this->store_table}.store_display_name as  sdn,{$this->store_table}.store_banner_img,
                         {$this->store_table}.store_prof_img,
                         {$this->user_table}.user_display_name, {$this->user_table}.user_prof_img,
                         {$this->movies_table}.poster,{$this->movies_table}.title

                  FROM {$this->store_table}
                  LEFT JOIN {$this->user_table} ON {$user_g_id} = {$this->user_table}.user_google_id
                  LEFT JOIN {$this->movies_table} ON {$this->movies_table}.imdb_id = '{$mid}'
                  WHERE {$this->store_table}.store_id = {$store_id}";

        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        //  var_dump($row);

       $notification = new notification($this->conn);
       $notification->send_RentNotif_toStore($store_notif_token,$sdn,$user_display_name,$title,$user_prof_img,$poster);

      }

      function store_cancelRequestMovieRent($movie_id,$user_g_id,$store_id){

          // select all query

        $query = "DELETE FROM {$this->requested_movie_table}
                         WHERE  {$this->requested_movie_table}.movie_id = :movie_id
                         AND    {$this->requested_movie_table}.user_id = (SELECT {$this->user_table}.user_id
                                                                          FROM {$this->user_table}
                                                                          WHERE {$this->user_table}.user_google_id = :user_g_id)
                         AND    {$this->requested_movie_table}.store_id  = :store_id
                         LIMIT 1";

         $stmt = $this->conn->prepare($query);
          // bind id of product to be updated

         $stmt->bindParam(":movie_id", $movie_id);
         $stmt->bindParam(":user_g_id", $user_g_id);
         $stmt->bindParam(":store_id", $store_id);

        // execute query
         $stmt->execute();

         return $stmt;
      }


      //shows list of user subscribed stores to rent a movie
      function store_listForRentTvshow($user_google_id,$tvshow_id,$store_id){
         $query2 = "";

         if($store_id!=-1)     $query2 = "and {$this->store_table}.store_id={$store_id}";

          // select all query
        $query = "SELECT         {$this->store_table}.*
                              ,( {$this->subscripton_table}.subscription_status )     As subscription_status
                              ,  {$this->subscripton_table}.user_id                   As user_id

            ,(CASE WHEN {$this->requested_tvshow_table}.req_status IS NOT NULL then {$this->requested_tvshow_table}.req_status
                                                                                  ELSE  'unknown'  END) As tvshow_status
            ,(CASE WHEN {$this->store_tableTvshow}.tvshow_id=:tvshow_id  then 'yes' ELSE  'no'  END) As tvshow_available
            ,{$this->requested_tvshow_table}.req_store_msg,{$this->requested_tvshow_table}.req_user_msg


            FROM       {$this->store_table}
            RIGHT JOIN {$this->subscripton_table}  ON ({$this->store_table}.store_id = {$this->subscripton_table}.store_id )
            Left JOIN  {$this->store_tableTvshow}  ON ({$this->subscripton_table}.store_id = {$this->store_tableTvshow} .store_id
                                                   AND {$this->store_tableTvshow}.tvshow_id=:tvshow_id)
            Left JOIN  {$this->requested_tvshow_table} ON ({$this->requested_tvshow_table}.user_id = {$this->subscripton_table}.user_id
                                                      AND {$this->subscripton_table}.store_id =  {$this->requested_tvshow_table}.store_id
                                                      and {$this->requested_tvshow_table}.tvshow_id=:tvshow_id)

            WHERE( {$this->subscripton_table}.user_id=(SELECT {$this->user_table}.user_id
                                                                FROM {$this->user_table}
                                                                WHERE {$this->user_table}.user_google_id=:google_id)
                                                              	and  {$this->subscripton_table}.subscription_status ='subscribed'
                                                                {$query2}  )";


        $stmt = $this->conn->prepare($query);


        // bind id of product to be updated
        $stmt->bindParam(":google_id", $user_google_id);
        $stmt->bindParam(":tvshow_id", $tvshow_id);
        // execute query
        $stmt->execute();

        return $stmt;
      }

      function store_requestTvshowRent($tvshow_id,$user_g_id,$store_id,$user_msg){

          // select all query
          $query = "INSERT IGNORE INTO {$this->requested_tvshow_table}
                                       (`tvshow_id`, `user_id`,
                                       `store_id`, `req_status`, `req_user_msg`, `req_date`)
                           VALUES (  :tvshow_id,    (SELECT {$this->user_table}.user_id
                                                            FROM {$this->user_table}
                                                            WHERE {$this->user_table}.user_google_id = :user_g_id),
                                     :store_id, 'pending',:user_msg , CURRENT_TIMESTAMP ) ";

         $stmt = $this->conn->prepare($query);
        // bind id of product to be updated
        //$stmt->bindParam(":tvshow_t", $tvshow_type);
         $stmt->bindParam(":tvshow_id", $tvshow_id);
         $stmt->bindParam(":user_g_id", $user_g_id);
         $stmt->bindParam(":store_id", $store_id);
         $stmt->bindParam(":user_msg", $user_msg);


          // execute query
         $stmt->execute();

         return $stmt;
      }

      function store_requestTvshowRentNotification($tvshow_id,$user_g_id,$store_id){

          $query = "SELECT {$this->store_table}.store_display_name as store_dn,{$this->store_table}.store_notif_token,
                           {$this->tvshow_table}.title,{$this->tvshow_table}.poster,
                           {$this->user_table}.user_display_name, {$this->user_table}.user_prof_img

                    FROM {$this->store_table}
                    LEFT JOIN {$this->user_table} ON {$user_g_id} = {$this->user_table}.user_google_id
                    LEFT JOIN {$this->tvshow_table} ON {$this->tvshow_table}.imdb_id   = '{$tvshow_id}'
                    WHERE {$this->store_table}.store_id = {$store_id}";

          $stmt = $this->conn->prepare($query);
          // execute query
          $stmt->execute();

          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          extract($row);
          //var_dump($row);

          $notification = new notification($this->conn);
          $notification->send_RentNotif_toStore($store_notif_token,$store_dn,$user_display_name,$title,$user_prof_img,$poster);


      }

      function store_cancelRequestTvshowRent($tvshow_id,$user_g_id,$store_id){

          $query = "DELETE FROM {$this->requested_tvshow_table}
                           WHERE  {$this->requested_tvshow_table}.tvshow_id = :tvshow_id
                           AND    {$this->requested_tvshow_table}.user_id = (SELECT {$this->user_table}.user_id
                                                                            FROM {$this->user_table}
                                                                            WHERE {$this->user_table}.user_google_id = :user_g_id)
                           AND    {$this->requested_tvshow_table}.store_id  = :store_id
                           LIMIT 1";

         $stmt = $this->conn->prepare($query);
          // bind id of product to be updated

         $stmt->bindParam(":tvshow_id", $tvshow_id);
         $stmt->bindParam(":user_g_id", $user_g_id);
         $stmt->bindParam(":store_id", $store_id);

        // execute query
         $stmt->execute();

       return $stmt;
      }

    #End User section-----------------------------------------------------------------------------------------------------------------


    #Store section---------------------------------------------------------------------------------------------------------------------


      function store_add_movies($query_data,$sgi){
        try {
            // query to insert record
            $query = "SELECT {$this->store_table}.store_id INTO @v1
                              FROM {$this->store_table} WHERE {$this->store_table}.store_google_id={$sgi};
                      INSERT IGNORE INTO {$this->store_tableMovies} (`store_id`, `movie_id`)
                              VALUES {$query_data};";

            // prepare query
            $stmt = $this->conn->prepare($query);
                // execute query
                if  ($stmt->execute()) return "true";

        }catch(PDOException $exception){
          return  $exception->getCode();
        }
      }

      function store_del_movies($query_data,$sgi){
        try {
            // query to insert record
            $query = "SELECT {$this->store_table}.store_id INTO @v1
                              FROM {$this->store_table} WHERE {$this->store_table}.store_google_id={$sgi};
                      DELETE FROM stores_movies_tab WHERE {$query_data};";


            // prepare query
            $stmt = $this->conn->prepare($query);
                // execute query
                if  ($stmt->execute()) return "true";

        }catch(PDOException $exception){
          return  $exception->getCode();
        }
      }

      function store_mangRequestMovieRent($movie_id,$user_g_id,$store_id,$req_status,$store_msg){

          // select all query
          $query = "UPDATE {$this->requested_movie_table}
                          SET {$this->requested_movie_table}.`req_status`=:req_status,
                              {$this->requested_movie_table}.`req_store_msg`=:store_msg,
                              {$this->requested_movie_table}.`req_date` = CURRENT_TIMESTAMP

                           WHERE ( {$this->requested_movie_table}.`movie_id` = :movie_id  AND
                                   {$this->requested_movie_table}.`user_id` =
                                   (SELECT {$this->user_table}.user_id FROM {$this->user_table}
                                          WHERE {$this->user_table}.user_google_id = :user_g_id) AND
                                    {$this->requested_movie_table}.`store_id` = :store_id)
                           LIMIT 1";

         $stmt = $this->conn->prepare($query);
        // bind id of product to be updated
        //$stmt->bindParam(":movie_t", $movie_type);
         $stmt->bindParam(":movie_id", $movie_id);
         $stmt->bindParam(":user_g_id", $user_g_id);
         $stmt->bindParam(":store_id", $store_id);
         $stmt->bindParam(":req_status", $req_status);
         $stmt->bindParam(":store_msg", $store_msg);

          // execute query
         $stmt->execute();

        return $stmt;
      }

      function store_mangRequestMovieRentNotification($mid,$user_g_id,$store_id,$req_status){

        $query = "SELECT {$this->user_table}.user_notif_token,{$this->user_table}.user_display_name,
                         {$this->store_table}.store_display_name as  sdn,{$this->store_table}.store_banner_img,
                         {$this->store_table}.store_prof_img,
                         {$this->user_table}.user_display_name, {$this->user_table}.user_prof_img,
                         {$this->movies_table}.poster,{$this->movies_table}.title

                  FROM {$this->store_table}
                  LEFT JOIN {$this->user_table} ON {$user_g_id} = {$this->user_table}.user_google_id
                  LEFT JOIN {$this->movies_table} ON {$this->movies_table}.imdb_id = '{$mid}'
                  WHERE {$this->store_table}.store_id = {$store_id}";

        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        //  var_dump($row);

       $notification = new notification($this->conn);
       $notification->send_mangRequestRentNotif_toUser($user_notif_token,$sdn,$user_display_name,$title,$user_prof_img,$poster,$req_status);

      }


      function store_add_tvshow($query_data,$sgi){
        try {
            // query to insert record
            $query = "SELECT {$this->store_table}.store_id INTO @v1
                              FROM {$this->store_table} WHERE {$this->store_table}.store_google_id={$sgi};
                      INSERT IGNORE INTO {$this->store_tableTvshow} (`store_id`, `tvshow_id`)
                              VALUES {$query_data};";

            // prepare query
            $stmt = $this->conn->prepare($query);
                // execute query
                if  ($stmt->execute()) return "true";

        }catch(PDOException $exception){
          return  $exception->getCode();
        }
      }

      function store_del_tvshow($query_data,$sgi){
        try {
            // query to insert record
            $query = "SELECT {$this->store_table}.store_id INTO @v1
                              FROM {$this->store_table} WHERE {$this->store_table}.store_google_id={$sgi};
                      DELETE FROM {$this->store_tableTvshow} WHERE {$query_data};";

                      //var_dump($query);

            // prepare query
            $stmt = $this->conn->prepare($query);
                // execute query
                if  ($stmt->execute()) return "true";
        }catch(PDOException $exception){
          return  $exception->getCode();
        }
      }

      function store_mangRequestTvshowRent($tvshow_id,$user_g_id,$store_id,$req_status,$store_msg){

          // select all query
          $query = "UPDATE {$this->requested_tvshow_table}
                          SET {$this->requested_tvshow_table}.`req_status`=:req_status,
                              {$this->requested_tvshow_table}.`req_store_msg`=:store_msg,
                              {$this->requested_tvshow_table}.`req_date` = CURRENT_TIMESTAMP

                           WHERE ( {$this->requested_tvshow_table}.`tvshow_id` = :tvshow_id  AND
                                   {$this->requested_tvshow_table}.`user_id` =
                                   (SELECT {$this->user_table}.user_id FROM {$this->user_table}
                                          WHERE {$this->user_table}.user_google_id = :user_g_id) AND
                                    {$this->requested_tvshow_table}.`store_id` = :store_id)
                           LIMIT 1";

         $stmt = $this->conn->prepare($query);

         $stmt->bindParam(":tvshow_id",  $tvshow_id);
         $stmt->bindParam(":user_g_id",  $user_g_id);
         $stmt->bindParam(":store_id",   $store_id);
         $stmt->bindParam(":req_status", $req_status);
         $stmt->bindParam(":store_msg", $store_msg);


          // execute query
         $stmt->execute();

        return $stmt;
      }

      function store_manRequestTvshowRentNotification($tvshow_id,$user_g_id,$store_id,$req_status){

        $query = "SELECT {$this->user_table}.user_notif_token,{$this->user_table}.user_display_name,
                         {$this->store_table}.store_display_name as  sdn,{$this->store_table}.store_banner_img,
                         {$this->tvshow_table}.title,{$this->tvshow_table}.poster,
                         {$this->user_table}.user_display_name, {$this->user_table}.user_prof_img

                   FROM {$this->store_table}
                   LEFT JOIN {$this->user_table} ON {$user_g_id} = {$this->user_table}.user_google_id
                   LEFT JOIN {$this->tvshow_table} ON {$this->tvshow_table}.imdb_id   = '{$tvshow_id}'
                   WHERE {$this->store_table}.store_id = {$store_id}";

        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        //  var_dump($row);

       $notification = new notification($this->conn);
       $notification->send_mangRequestRentNotif_toUser($user_notif_token,$sdn,$user_display_name,$title,$user_prof_img,$poster,$req_status);

      }



      #stores as a user------------------------------------------------------------------------------------------------

      // create stores on first login
      function store_register_onLogin(){
        try {
            // query to insert record
            $query = "INSERT IGNORE INTO {$this->store_table}
                      (store_google_id, store_first_name, store_last_name,
                      store_display_name,store_email) values (? ,? ,? ,? ,?)";

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
      function store_read_single_byID(){

          $this->id=htmlspecialchars(strip_tags($this->id));

          // query to read single record
          $query = "SELECT *
                    FROM  {$this->store_table}
                      WHERE store_google_id = ?  OR store_id = ?
                    LIMIT  0,1";

          // prepare query statement
          $stmt = $this->conn->prepare($query);
          // sanitize

          // bind id of product to be updated
          $stmt->bindParam(1, $this->id);
          $stmt->bindParam(2, $this->id, PDO::PARAM_INT);

          // execute query
          $stmt->execute();

        return $stmt;
      }

      // update the store
      function store_update_bywhat($what){

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
                    $query = "UPDATE {$this->store_table} SET
                               store_geolng =:geolng,store_geolat =:geolat,store_address =:address,store_postcode =:postcode
                               WHERE    store_google_id =:google_id LIMIT 1";

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
                     $query = "UPDATE {$this->store_table} SET   store_description =:description
                             WHERE       store_google_id =:google_id LIMIT 1";
                             // prepare query statement
                             $stmt = $this->conn->prepare($query);
                              // bind id of product to be updated
                             $stmt->bindParam(":google_id", $this->google_id);
                             $stmt->bindParam(":description", $this->description);

                      break;
                case "update_detail":
                          $query = "UPDATE {$this->store_table} SET   store_display_name =:display_name,
                                  store_description =:description,store_business_name=:business_name, store_language =:language
                                  WHERE       store_google_id =:google_id LIMIT 1";
                                  // prepare query statement
                                  $stmt = $this->conn->prepare($query);
                                   // bind id of product to be updated
                                  $stmt->bindParam(":google_id", $this->google_id);
                                  $stmt->bindParam(":display_name", $this->display_name);
                                  $stmt->bindParam(":description", $this->description);
                                  $stmt->bindParam(":business_name", $this->business_name);
                                  $stmt->bindParam(":language", $this->language);
                     break;
                 case "display_name":
                           $query = "UPDATE {$this->store_table} SET   store_display_name =:display_name
                                   WHERE       store_google_id =:google_id LIMIT 1";
                                   // prepare query statement
                                   $stmt = $this->conn->prepare($query);
                                    // bind id of product to be updated
                                   $stmt->bindParam(":google_id", $this->google_id);
                                   $stmt->bindParam(":display_name", $this->display_name);

                        break;
                 case "notification":
                         $query = "UPDATE {$this->store_table} SET   store_notif_token =:notif_token
                                 WHERE store_google_id =:google_id LIMIT 1";
                                 // prepare query statement
                                 $stmt = $this->conn->prepare($query);
                                  // bind id of product to be updated
                                 $stmt->bindParam(":google_id", $this->google_id);
                                 $stmt->bindParam(":notif_token", $this->notif_token);

                        break;
                  case "prof_img":
                         $query = "UPDATE {$this->store_table} SET   store_prof_img =:prof_img
                                   WHERE store_google_id =:google_id LIMIT 1";
                                   // prepare query statement
                                   $stmt = $this->conn->prepare($query);
                                   // bind id of product to be updated
                                   $stmt->bindParam(":google_id", $this->google_id);
                                   $stmt->bindParam(":prof_img", $this->prof_img);
                        break;
                  case "banner_img":
                         $query = "UPDATE {$this->store_table} SET   store_banner_img =:banner_img
                                   WHERE store_google_id =:google_id LIMIT 1";
                                   // prepare query statement
                                   $stmt = $this->conn->prepare($query);
                                   // bind id of product to be updated
                                   $stmt->bindParam(":google_id", $this->google_id);
                                   $stmt->bindParam(":banner_img", $this->banner_img);
                         break;

                  default:
                        return false;
                        break;

                  //code to be executed if n is different from all labels;
          }
         // update query
         if($stmt->execute()){
          //  var_dump($stmt->rowCount());
           return true;
         }

          }catch(PDOException $exception){
               return false;
              //echo "Connection error: " . $exception->getMessage();
              //echo $exception->getCode();
            //  return  $exception->getCode();
          }

      }


      // delete the product
      function store_delete(){

          // sanitize
          $keywords=htmlspecialchars(strip_tags($this->id));
          // delete query
          $query = "DELETE FROM {$this->store_table} WHERE (store_google_id = ? OR store_id = ?) LIMIT 1";

          // prepare query
          $stmt = $this->conn->prepare($query);

          // sanitize
          $this->id=htmlspecialchars(strip_tags($this->id));

          // bind id of record to delete
          $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
          $stmt->bindParam(2, $this->id, PDO::PARAM_INT);

          // execute query

          $stmt->execute();

           return $stmt;

      }

      // search products
      function store_search($keywords){

          // select all query
          $query = "SELECT *  FROM
                   {$this->store_table} WHERE
                    store_first_name LIKE ? OR store_email LIKE ? OR store_display_name LIKE ?
                  ORDER BY
                      store_reg_date DESC";

          // prepare query statement
          $stmt = $this->conn->prepare($query);

          // sanitize
          $keywords=htmlspecialchars(strip_tags($keywords));
          $keywords = "%{$keywords}%";

          // bind
          $stmt->bindParam(1, $keywords);
          $stmt->bindParam(2, $keywords);
          $stmt->bindParam(3, $keywords);

          // execute query
          $stmt->execute();

          return $stmt;
      }

      // read products with pagination
      public function store_read_all_byPage($from_record_num, $records_per_page,$sort,$order){

          $sort=htmlspecialchars(strip_tags($sort));
          $order ==htmlspecialchars(strip_tags($order));

          // select query
          $query = "SELECT *
                  FROM
                     {$this->store_table} ORDER BY {$sort} {$order}
                  LIMIT ?, ?";

          // prepare query statement
          $stmt = $this->conn->prepare( $query );

          // bind variable values
          $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
          $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

          // execute query
          $stmt->execute();

          // return values from database
          return $stmt;
      }

      // used for paging products
      public function count(){
          $query = "SELECT COUNT(*) as total_rows FROM {$this->store_table}";

          $stmt = $this->conn->prepare( $query );
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          return $row['total_rows'];
      }


    #End Store section-----------------------------------------------------------------------------------------------------------------



}
?>
