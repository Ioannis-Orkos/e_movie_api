<?php
class Movies{

    // database connection and table name
    private $conn;

    //main tables
    private $store_table             ="stores_tab"; //{$this->store_table}
    private $movies_table            ="movies_tab";
    private $users_table             ="users_tab";//{$this->user_table}

    //supporting tables
    private $movies_genres_table     ="movies_genres_tab";
    private $subscripton_table       ="user_subscripton_tab";  //{$this->table_relation}
    private $requested_movie_table   ="requested_movies_tab"; //{$this->requested_movie_table}
    private $store_tableMovies       ="stores_movies_tab"; //{$this->requested_movie_table}

    // object properties
    public $tmdb_id;
    public $imdb_id;
    public $emdb_id;

    public $type;
    public $title;
    public $synopsis;

    public $year;
    public $runtime;
    public $released;

    public $trailer;
    public $poster;
    public $fanart;

    public $percentage;
    public $votes;
    public $certification;

    public $genres=array();



  // constructor with $db as database connection
  public function __construct($db){
        $this->conn = $db;
    }



  #User section---------------------------------------------------------------------------------------------------------------------

      public function readOne_movie_forUsers(){

          // query to read single record
          $query ="SELECT {$this->movies_table}.imdb_id,{$this->movies_table}.tmdb_id,
                          {$this->movies_table}.emdb_id,{$this->movies_table}.type,
                          {$this->movies_table}.title,{$this->movies_table}.synopsis,
                          {$this->movies_table}.year, {$this->movies_table}.runtime,
                          {$this->movies_table}.trailer,{$this->movies_table}.released,
                          {$this->movies_table}.poster,{$this->movies_table}.fanart,
                          {$this->movies_table}.percentage,{$this->movies_table}.votes,
                          {$this->movies_table}.certification,
                      GROUP_CONCAT({$this->movies_genres_table}.value)  AS genres

                  FROM {$this->movies_table}
                  LEFT JOIN {$this->movies_genres_table}
                  ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                  GROUP BY {$this->movies_table}.imdb_id
                  HAVING   {$this->movies_table}.imdb_id = ? or {$this->movies_table}.tmdb_id = ?
                  LIMIT    0,1 ";

          // prepare query statement
          $stmt = $this->conn->prepare( $query );
          // sanitize
          $this->id=htmlspecialchars(strip_tags($this->id));
          // bind id of product to be updated
          $stmt->bindParam(1, $this->id);
          $stmt->bindParam(2, $this->id);
          // execute query
          $stmt->execute();

        return $stmt;
      }


      // read movies with pagination for user  // read products with pagination
      public function readPaging_movies_forUsers($from_record_num, $records_per_page,$mType,$mGenre,$mSort,$mYear,$mOrder){

          $query2 = "";

          // sanitize
          $mType=htmlspecialchars(strip_tags($mType));
          $mOrder=htmlspecialchars(strip_tags($mOrder));
          $mGenre=htmlspecialchars(strip_tags($mGenre));
          $mYear=htmlspecialchars(strip_tags($mYear));


          if($mGenre!="all")              $query2 = " WHERE {$this->movies_genres_table}.value IN ({$mGenre})  ";
          if($mYear!="all"
             && is_numeric($mYear))       $query2 = " WHERE  {$this->movies_table}.year = '{$mYear}' ";
          if(($mGenre!="all")
              && ($mYear!="all"
              && is_numeric($mYear)))
                                          $query2 =  " WHERE {$this->movies_genres_table}.value IN ({$mGenre})
                                                       AND {$this->movies_table}.year = '{$mYear}' ";
          if(!empty($mType)
              && empty($query2))          $query2 = "WHERE {$this->movies_table}.type = '{$mType}' ";
              else if(!empty($mType)
                      &&!empty($query2))  $query2 .= "AND {$this->movies_table}.type = '{$mType}' ";



          $query ="SELECT {$this->movies_table}.imdb_id , {$this->movies_table}.title,
                          {$this->movies_table}.year,     {$this->movies_table}.runtime,
                          {$this->movies_table}.released, {$this->movies_table}.synopsis,
                          {$this->movies_table}.poster,   {$this->movies_table}.certification,
                          {$this->movies_table}.type,
                      GROUP_CONCAT({$this->movies_genres_table}.value)
                                    AS genres FROM {$this->movies_table}

                  LEFT JOIN {$this->movies_genres_table}
                  ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                          {$query2}
                  GROUP BY {$this->movies_table}.imdb_id
                  ORDER BY {$this->movies_table}.released {$mOrder}
                  LIMIT   ?, ? ";

                // prepare query statement
                $stmt = $this->conn->prepare( $query );

                // bind variable values
                $stmt->bindParam(1, $from_record_num,  PDO::PARAM_INT);
                $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

                // execute query
                $stmt->execute();

                // return values from database
          return $stmt;
        }
      public function readPagingcount_movies_forUsers($mType,$mGenre,$mSort,$mYear,$mOrder){

          $query2 = "";

          // sanitize
          $mType=htmlspecialchars(strip_tags($mType));
          $mOrder=htmlspecialchars(strip_tags($mOrder));
          $mGenre=htmlspecialchars(strip_tags($mGenre));
          $mYear=htmlspecialchars(strip_tags($mYear));

          if($mGenre!="all")              $query2 = " WHERE {$this->movies_genres_table}.value IN ({$mGenre})  ";
          if($mYear!="all"
             && is_numeric($mYear))       $query2 = " WHERE  {$this->movies_table}.year = '{$mYear}' ";
          if(($mGenre!="all")
              && ($mYear!="all"
              && is_numeric($mYear)))
                                          $query2 =  " WHERE {$this->movies_genres_table}.value IN ({$mGenre})
                                                       AND {$this->movies_table}.year = '{$mYear}' ";
          if(!empty($mType)
              && empty($query2))          $query2 = "WHERE {$this->movies_table}.type = '{$mType}' ";
              else if(!empty($mType)
                      &&!empty($query2))  $query2 .= "AND {$this->movies_table}.type = '{$mType}' ";


          $query ="SELECT COUNT(*) as total_rows FROM {$this->movies_table}

                   LEFT JOIN {$this->movies_genres_table}
                   ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                      {$query2}
                   GROUP BY {$this->movies_table}.imdb_id ";

              // prepare query statement
              $stmt = $this->conn->prepare( $query );

              // execute query
              $stmt->execute();
              $row = $stmt->rowCount();
              // return values from database
        return $row;
      }



      public function readPaging_storeMovies_forUser($from_record_num, $records_per_page,$mStoreId,$mType,$mGenre,$mSort,$mYear,$mOrder){

        $mStoreId =htmlspecialchars(strip_tags($mStoreId));

        $query2 = " WHERE {$this->store_tableMovies}.store_id = {$mStoreId} ";

        // sanitize
        $mType=htmlspecialchars(strip_tags($mType));
        $mOrder=htmlspecialchars(strip_tags($mOrder));
        $mGenre=htmlspecialchars(strip_tags($mGenre));
        $mYear=htmlspecialchars(strip_tags($mYear));

        if($mGenre!="all")                           $query2 .= " AND {$this->movies_genres_table}.value IN ({$mGenre})";
        if($mYear!="all" && is_numeric($mYear))      $query2 .= " AND  {$this->movies_table}.year = '{$mYear}' ";
        if(!empty($mType))                           $query2 .= " AND {$this->movies_table}.type = '{$mType}' ";

        $query ="SELECT {$this->movies_table}.imdb_id , {$this->movies_table}.title,
                        {$this->movies_table}.year,     {$this->movies_table}.runtime,
                        {$this->movies_table}.released, {$this->movies_table}.synopsis,
                        {$this->movies_table}.poster,   {$this->movies_table}.certification,
                        {$this->movies_table}.type,

                         GROUP_CONCAT({$this->movies_genres_table}.value)  AS genres

                 FROM {$this->movies_table}

                 LEFT JOIN {$this->movies_genres_table}
                 ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id

                 RIGHT JOIN  {$this->store_tableMovies}
                 ON {$this->store_tableMovies}.movie_id = {$this->movies_table}.imdb_id

                 {$query2}

                 GROUP BY {$this->movies_table}.imdb_id
                 ORDER BY {$this->movies_table}.year {$mOrder}
                 LIMIT        ?, ? ";

              // prepare query statement
              $stmt = $this->conn->prepare($query);

              // bind variable values
              $stmt->bindParam(1, $from_record_num,  PDO::PARAM_INT);
              $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

              // execute query
              $stmt->execute();

              // return values from database
              return $stmt;
          }



          // read products with pagination
      public function readPagingcount_storeMovies_forUser($mStoreId,$mType,$mGenre,$mSort,$mYear,$mOrder){

          $mStoreId=htmlspecialchars(strip_tags($mStoreId));

          $query2 = " WHERE {$this->store_tableMovies}.store_id = {$mStoreId} ";

          // sanitize
          $mType=htmlspecialchars(strip_tags($mType));
          $mOrder=htmlspecialchars(strip_tags($mOrder));
          $mGenre=htmlspecialchars(strip_tags($mGenre));
          $mYear=htmlspecialchars(strip_tags($mYear));

          if($mGenre!="all")                           $query2 .= " AND {$this->movies_genres_table}.value IN ({$mGenre})";
          if($mYear!="all" && is_numeric($mYear))      $query2 .= " AND  {$this->movies_table}.year = '{$mYear}' ";
          if(!empty($mType))                           $query2 .= " AND {$this->movies_table}.type = '{$mType}' ";

          $query ="SELECT COUNT(*) as total_rows
                    FROM {$this->movies_table}

                   LEFT JOIN {$this->movies_genres_table}
                      ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                   RIGHT JOIN  {$this->store_tableMovies}
                      ON {$this->store_tableMovies}.movie_id = {$this->movies_table}.imdb_id

                       {$query2}

                   GROUP BY {$this->movies_table}.imdb_id ";

                // prepare query statement
                $stmt = $this->conn->prepare( $query );

                // execute query
                $stmt->execute();
                $row = $stmt->rowCount();
                // return values from database

            return $row;
      }




      // read movies with pagination for user
      public function readPaging_searchMovies_forUser($from_record_num, $records_per_page,$search,$mType,$mGenre,$mSort,$mYear,$mOrder){

          $query2 = "WHERE {$this->movies_table}.title LIKE  '%{$search}%' ";

          // sanitize
          $mType=htmlspecialchars(strip_tags($mType));
          $mOrder=htmlspecialchars(strip_tags($mOrder));
          $mGenre=htmlspecialchars(strip_tags($mGenre));
          $mYear=htmlspecialchars(strip_tags($mYear));


          if($mGenre!="all")              $query2 = " AND {$this->movies_genres_table}.value IN ({$mGenre})  ";
          if($mYear!="all"
             && is_numeric($mYear))       $query2 = " AND  {$this->movies_table}.year = '{$mYear}' ";
          if(($mGenre!="all")
              && ($mYear!="all"
              && is_numeric($mYear)))
                                          $query2 =  " AND {$this->movies_genres_table}.value IN ({$mGenre})
                                                       AND {$this->movies_table}.year = '{$mYear}' ";
          if(!empty($mType)
              && empty($query2))          $query2 = " AND {$this->movies_table}.type = '{$mType}' ";
              else if(!empty($mType)
                      &&!empty($query2))  $query2 .= " AND {$this->movies_table}.type = '{$mType}' ";



          $query ="SELECT {$this->movies_table}.imdb_id , {$this->movies_table}.title,
                          {$this->movies_table}.year,     {$this->movies_table}.runtime,
                          {$this->movies_table}.released, {$this->movies_table}.synopsis,
                          {$this->movies_table}.poster,   {$this->movies_table}.certification,
                          {$this->movies_table}.type,
                      GROUP_CONCAT({$this->movies_genres_table}.value)
                                    AS genres FROM {$this->movies_table}

                  LEFT JOIN {$this->movies_genres_table}
                  ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                          {$query2}
                  GROUP BY {$this->movies_table}.imdb_id
                  ORDER BY {$this->movies_table}.released {$mOrder}
                  LIMIT   ?, ? ";

                // prepare query statement
                $stmt = $this->conn->prepare( $query );

                // bind variable values
                $stmt->bindParam(1, $from_record_num,  PDO::PARAM_INT);
                $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

                // execute query
                $stmt->execute();

                // return values from database
          return $stmt;
        }


          // read products with pagination
      public function readPagingcount_searchMovies_forUser($search,$mType,$mGenre,$mSort,$mYear,$mOrder){

          $query2 = "WHERE {$this->movies_table}.title LIKE  '%{$search}%' ";

          // sanitize
          $mType=htmlspecialchars(strip_tags($mType));
          $mOrder=htmlspecialchars(strip_tags($mOrder));
          $mGenre=htmlspecialchars(strip_tags($mGenre));
          $mYear=htmlspecialchars(strip_tags($mYear));


          if($mGenre!="all")              $query2 = " AND {$this->movies_genres_table}.value IN ({$mGenre})  ";
          if($mYear!="all"
             && is_numeric($mYear))       $query2 = " AND  {$this->movies_table}.year = '{$mYear}' ";
          if(($mGenre!="all")
              && ($mYear!="all"
              && is_numeric($mYear)))
                                          $query2 =  " AND {$this->movies_genres_table}.value IN ({$mGenre})
                                                       AND {$this->movies_table}.year = '{$mYear}' ";
          if(!empty($mType)
              && empty($query2))          $query2 = " AND {$this->movies_table}.type = '{$mType}' ";
              else if(!empty($mType)
                      &&!empty($query2))  $query2 .= " AND {$this->movies_table}.type = '{$mType}' ";


          $query ="SELECT COUNT(*) as total_rows FROM {$this->movies_table}

                   LEFT JOIN {$this->movies_genres_table}
                   ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                      {$query2}
                   GROUP BY {$this->movies_table}.imdb_id ";

              // prepare query statement
              $stmt = $this->conn->prepare( $query );

              // execute query
              $stmt->execute();
              $row = $stmt->rowCount();
              // return values from database
        return $row;
      }




  #End User section-----------------------------------------------------------------------------------------------------------------




  //store--------------------------------------------------------------------------------------------------------------------

  // read products with pagination
  public function readPaging_movies_forStores($sgid,$from_record_num, $records_per_page,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie){

      $query2 = "";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));


      if($mGenre!="all")              $query2 = " WHERE {$this->movies_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->movies_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->movies_genres_table}.value IN ({$mGenre})
                                                   AND {$this->movies_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->movies_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->movies_table}.type = '{$mType}' ";

      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableMovies}.movie_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableMovies}.movie_id IS NOT NULL ";


      $query ="SELECT {$this->movies_table}.imdb_id , {$this->movies_table}.title,
                      {$this->movies_table}.year,     {$this->movies_table}.runtime,
                      {$this->movies_table}.released, {$this->movies_table}.synopsis,
                      {$this->movies_table}.poster,   {$this->movies_table}.certification,
                      {$this->movies_table}.type,
                  CASE WHEN stores_movies_tab.movie_id IS NOT NULL THEN 'yes' ELSE 'no' END AS 'available',
                  GROUP_CONCAT({$this->movies_genres_table}.value)
                                AS genres FROM {$this->movies_table}

              LEFT JOIN  {$this->store_tableMovies}
                ON ({$this->store_tableMovies}.store_id=  (SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableMovies}.movie_id = {$this->movies_table}.imdb_id )
              LEFT JOIN {$this->movies_genres_table}
                ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                {$query2}
              GROUP BY {$this->movies_table}.imdb_id
              ORDER BY {$this->movies_table}.released {$mOrder}
              LIMIT   ?, ? ";

            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            // bind variable values
            $stmt->bindParam(1, $from_record_num,  PDO::PARAM_INT);
            $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

            // execute query
            $stmt->execute();

            // return values from database
      return $stmt;
    }

      // read products with pagination
  public function readPagingcount_movies_forStores($sgid,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie){

      $query2 = "";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));



      if($mGenre!="all")              $query2 = " WHERE {$this->movies_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->movies_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->movies_genres_table}.value IN ({$mGenre})
                                                   AND {$this->movies_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->movies_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->movies_table}.type = '{$mType}' ";


      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableMovies}.movie_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableMovies}.movie_id IS NOT NULL ";


      $query ="SELECT COUNT(*) as total_rows FROM {$this->movies_table}

               LEFT JOIN  {$this->store_tableMovies}
                ON ({$this->store_tableMovies}.store_id=(SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableMovies}.movie_id = {$this->movies_table}.imdb_id )
               LEFT JOIN {$this->movies_genres_table}
                ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                  {$query2}
               GROUP BY {$this->movies_table}.imdb_id ";

          // prepare query statement
          $stmt = $this->conn->prepare( $query );

          // execute query
          $stmt->execute();
          $row = $stmt->rowCount();
          // return values from database
    return $row;
  }




  public function store_readRequestedMoviesPaging($from_record_num, $records_per_page,$sgid,$status="pending"){

              $query = "SELECT *,{$this->store_table}.store_id,
                        CASE WHEN  {$this->store_tableMovies}.movie_id IS NOT NULL THEN 'yes' ELSE 'no' END AS 'movie_status',
                        {$this->requested_movie_table}.req_store_msg,{$this->requested_movie_table}.req_user_msg



                        FROM {$this->requested_movie_table}
                        LEFT JOIN {$this->movies_table} ON {$this->movies_table}.imdb_id ={$this->requested_movie_table}.movie_id
                        LEFT JOIN {$this->users_table} ON {$this->users_table}.user_id ={$this->requested_movie_table}.user_id
                        LEFT JOIN {$this->store_table} ON {$this->store_table}.store_id={$this->requested_movie_table}.store_id
                        LEFT JOIN {$this->store_tableMovies} ON ({$this->store_tableMovies}.store_id={$this->requested_movie_table}.store_id
                          AND {$this->store_tableMovies}.movie_id ={$this->requested_movie_table}.movie_id)

                                  WHERE ({$this->requested_movie_table}.req_status = :status
                                          AND {$this->store_table}.store_google_id = :google_id )

                        ORDER BY {$this->requested_movie_table}.req_date  DESC
                        LIMIT :from , :to ";

        $stmt = $this->conn->prepare( $query );

        // bind variable values
        $stmt->bindParam(":google_id", $sgid);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":from", $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(":to", $records_per_page, PDO::PARAM_INT);
       // execute query
       $stmt->execute();
    return  $stmt;
  }
  public function store_readRequestedMoviesCount($sgid,$status){

      $query = "SELECT COUNT(*) as total_rows
                FROM {$this->requested_movie_table}
                LEFT JOIN {$this->movies_table} ON movies_tab.imdb_id =requested_movies_tab.movie_id
                LEFT JOIN {$this->users_table} ON users_tab.user_id =requested_movies_tab.user_id
                LEFT JOIN {$this->store_table} ON stores_tab.store_id=requested_movies_tab.store_id
                  WHERE ({$this->requested_movie_table}.req_status = :status
                        AND {$this->store_table}.store_google_id = :google_id )

                ORDER BY {$this->requested_movie_table}.req_date  DESC";

          $stmt = $this->conn->prepare( $query );

                // bind variable values
          $stmt->bindParam(":google_id", $sgid);
          $stmt->bindParam(":status", $status);

      // execute query
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      //$row = $stmt->rowCount();
      // return values from database
      //return $row;
   return $row['total_rows'];
  }





  // read movies with pagination for user
  public function readPaging_searchMovies_forStores($sgid,$from_record_num, $records_per_page,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie,$search){

      $query2 = "WHERE {$this->movies_table}.title LIKE  '%{$search}%' ";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));


      if($mGenre!="all")              $query2 = " WHERE {$this->movies_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->movies_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->movies_genres_table}.value IN ({$mGenre})
                                                   AND {$this->movies_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->movies_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->movies_table}.type = '{$mType}' ";

      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableMovies}.movie_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableMovies}.movie_id IS NOT NULL ";


      $query ="SELECT {$this->movies_table}.imdb_id , {$this->movies_table}.title,
                      {$this->movies_table}.year,     {$this->movies_table}.runtime,
                      {$this->movies_table}.released, {$this->movies_table}.synopsis,
                      {$this->movies_table}.poster,   {$this->movies_table}.certification,
                      {$this->movies_table}.type,
                  CASE WHEN stores_movies_tab.movie_id IS NOT NULL THEN 'yes' ELSE 'no' END AS 'available',
                  GROUP_CONCAT({$this->movies_genres_table}.value)
                                AS genres FROM {$this->movies_table}

              LEFT JOIN  {$this->store_tableMovies}
                ON ({$this->store_tableMovies}.store_id=  (SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableMovies}.movie_id = {$this->movies_table}.imdb_id )
              LEFT JOIN {$this->movies_genres_table}
                ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                {$query2}
              GROUP BY {$this->movies_table}.imdb_id
              ORDER BY {$this->movies_table}.released {$mOrder}
              LIMIT   ?, ? ";

            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            // bind variable values
            $stmt->bindParam(1, $from_record_num,  PDO::PARAM_INT);
            $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

            // execute query
            $stmt->execute();

            // return values from database
      return $stmt;
    }


      // read products with pagination
  public function readPagingcount_searchMovies_forStores($sgid,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie,$search){

      $query2 = "WHERE {$this->movies_table}.title LIKE  '%{$search}%' ";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));



      if($mGenre!="all")              $query2 = " WHERE {$this->movies_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->movies_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->movies_genres_table}.value IN ({$mGenre})
                                                   AND {$this->movies_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->movies_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->movies_table}.type = '{$mType}' ";


      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableMovies}.movie_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableMovies}.movie_id IS NOT NULL ";


      $query ="SELECT COUNT(*) as total_rows FROM {$this->movies_table}

               LEFT JOIN  {$this->store_tableMovies}
                ON ({$this->store_tableMovies}.store_id=(SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableMovies}.movie_id = {$this->movies_table}.imdb_id )
               LEFT JOIN {$this->movies_genres_table}
                ON {$this->movies_genres_table}.imdb_id = {$this->movies_table}.imdb_id
                  {$query2}
               GROUP BY {$this->movies_table}.imdb_id ";

          // prepare query statement
          $stmt = $this->conn->prepare( $query );

          // execute query
          $stmt->execute();
          $row = $stmt->rowCount();
          // return values from database
    return $row;
  }




  //endstore-----------------------------------------------------------------------------------------------------------------















        // used for paging products
        public function count(){
            $query = "SELECT COUNT(*) as total_rows FROM " . $this->movies_table . "";

            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['total_rows'];
        }



}
?>
