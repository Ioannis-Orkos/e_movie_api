<?php
class Tvshow{

    // database connection and table name
    private $conn;

    //main tables
    private $store_table =               "stores_tab"; //{$this->store_table}
    private $tvshow_table  =              "tvshow_tab";
    private $users_table             ="users_tab";//{$this->user_table}

    //supporting tables
    private $tvshow_genres_table =        "tvshow_genres_tab";
    private $subscripton_table =         "user_subscripton_tab";  //{$this->table_relation}
    private $requested_tvshow_table=      "requested_tvshow_tab"; //{$this->requested_tvshow_table}
    private $store_tableTvshow=      "stores_tvshows_tab"; //{$this->requested_tvshow_table}


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

    // used when filling up the update product form
    public function readOne_tvshow_forUsers(){

        // query to read single record
        $query ="SELECT {$this->tvshow_table}.*,

                    GROUP_CONCAT({$this->tvshow_genres_table}.value)  AS genres

                FROM {$this->tvshow_table}
                LEFT JOIN {$this->tvshow_genres_table}
                ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                GROUP BY {$this->tvshow_table}.imdb_id
                HAVING   {$this->tvshow_table}.imdb_id = ? or {$this->tvshow_table}.tmdb_id = ?
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


    // read products with pagination
    public function readPaging_tvshow_forUsers($from_record_num, $records_per_page,$mType,$mGenre,$mSort,$mYear,$mOrder){

        $query2 = "";

        // sanitize
        $mType=htmlspecialchars(strip_tags($mType));
        $mOrder=htmlspecialchars(strip_tags($mOrder));
        $mGenre=htmlspecialchars(strip_tags($mGenre));
        $mYear=htmlspecialchars(strip_tags($mYear));


        if($mGenre!="all")              $query2 = " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
        if($mYear!="all"
           && is_numeric($mYear))       $query2 = " WHERE  {$this->tvshow_table}.year = '{$mYear}' ";
        if(($mGenre!="all")
            && ($mYear!="all"
            && is_numeric($mYear)))
                                        $query2 =  " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                     AND {$this->tvshow_table}.year = '{$mYear}' ";
        if(!empty($mType)
            && empty($query2))          $query2 = "WHERE {$this->tvshow_table}.type = '{$mType}' ";
            else if(!empty($mType)
                    &&!empty($query2))  $query2 .= "AND {$this->tvshow_table}.type = '{$mType}' ";



        $query ="SELECT {$this->tvshow_table}.imdb_id , {$this->tvshow_table}.title,
                        {$this->tvshow_table}.year,     {$this->tvshow_table}.runtime,
                        {$this->tvshow_table}.released, {$this->tvshow_table}.synopsis,
                        {$this->tvshow_table}.poster,   {$this->tvshow_table}.certification,
                        {$this->tvshow_table}.type,     {$this->tvshow_table}.last_updated,
                    GROUP_CONCAT({$this->tvshow_genres_table}.value)
                                  AS genres FROM {$this->tvshow_table}

                LEFT JOIN {$this->tvshow_genres_table}
                ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                        {$query2}
                GROUP BY {$this->tvshow_table}.imdb_id
                ORDER BY {$this->tvshow_table}.last_updated {$mOrder}
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
    public function readPagingcount_tvshow_forUsers($mType,$mGenre,$mSort,$mYear,$mOrder){

        $query2 = "";

        // sanitize
        $mType=htmlspecialchars(strip_tags($mType));
        $mOrder=htmlspecialchars(strip_tags($mOrder));
        $mGenre=htmlspecialchars(strip_tags($mGenre));
        $mYear=htmlspecialchars(strip_tags($mYear));

        if($mGenre!="all")              $query2 = " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
        if($mYear!="all"
           && is_numeric($mYear))       $query2 = " WHERE  {$this->tvshow_table}.year = '{$mYear}' ";
        if(($mGenre!="all")
            && ($mYear!="all"
            && is_numeric($mYear)))
                                        $query2 =  " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                     AND {$this->tvshow_table}.year = '{$mYear}' ";
        if(!empty($mType)
            && empty($query2))          $query2 = "WHERE {$this->tvshow_table}.type = '{$mType}' ";
            else if(!empty($mType)
                    &&!empty($query2))  $query2 .= "AND {$this->tvshow_table}.type = '{$mType}' ";


        $query ="SELECT COUNT(*) as total_rows FROM {$this->tvshow_table}

                 LEFT JOIN {$this->tvshow_genres_table}
                 ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                    {$query2}
                 GROUP BY {$this->tvshow_table}.imdb_id ";

            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            // execute query
            $stmt->execute();
            $row = $stmt->rowCount();
            // return values from database
      return $row;
    }



    public function readPaging_storeTvshows_forUser($from_record_num, $records_per_page,$mStoreId,$mType,$mGenre,$mSort,$mYear,$mOrder){

      $mStoreId =htmlspecialchars(strip_tags($mStoreId));

      $query2 = " WHERE {$this->store_tableTvshow}.store_id = {$mStoreId} ";

      // sanitize
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));

      if($mGenre!="all")                           $query2 .= " AND {$this->tvshow_genres_table}.value IN ({$mGenre})";
      if($mYear!="all" && is_numeric($mYear))      $query2 .= " AND  {$this->tvshow_table}.year = '{$mYear}' ";
      if(!empty($mType))                           $query2 .= " AND {$this->tvshow_table}.type = '{$mType}' ";

      $query ="SELECT {$this->tvshow_table}.imdb_id , {$this->tvshow_table}.title,
                      {$this->tvshow_table}.year,     {$this->tvshow_table}.runtime,
                      {$this->tvshow_table}.released, {$this->tvshow_table}.synopsis,
                      {$this->tvshow_table}.poster,   {$this->tvshow_table}.certification,
                      {$this->tvshow_table}.type,     {$this->tvshow_table}.last_updated,

                       GROUP_CONCAT({$this->tvshow_genres_table}.value)  AS genres

               FROM {$this->tvshow_table}

               LEFT JOIN {$this->tvshow_genres_table}
               ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id

               RIGHT JOIN  {$this->store_tableTvshow}
               ON {$this->store_tableTvshow}.tvshow_id = {$this->tvshow_table}.imdb_id

               {$query2}

               GROUP BY {$this->tvshow_table}.imdb_id
               ORDER BY {$this->tvshow_table}.last_updated {$mOrder}
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
    public function readPagingcount_storeTvshows_forUser($mStoreId,$mType,$mGenre,$mSort,$mYear,$mOrder){

        $mStoreId=htmlspecialchars(strip_tags($mStoreId));

        $query2 = " WHERE {$this->store_tableTvshow}.store_id = {$mStoreId} ";

        // sanitize
        $mType=htmlspecialchars(strip_tags($mType));
        $mOrder=htmlspecialchars(strip_tags($mOrder));
        $mGenre=htmlspecialchars(strip_tags($mGenre));
        $mYear=htmlspecialchars(strip_tags($mYear));

        if($mGenre!="all")                           $query2 .= " AND {$this->tvshow_genres_table}.value IN ({$mGenre})";
        if($mYear!="all" && is_numeric($mYear))      $query2 .= " AND  {$this->tvshow_table}.year = '{$mYear}' ";
        if(!empty($mType))                           $query2 .= " AND {$this->tvshow_table}.type = '{$mType}' ";

        $query ="SELECT COUNT(*) as total_rows
                  FROM {$this->tvshow_table}

                 LEFT JOIN {$this->tvshow_genres_table}
                    ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                 RIGHT JOIN  {$this->store_tableTvshow}
                    ON {$this->store_tableTvshow}.tvshow_id = {$this->tvshow_table}.imdb_id

                     {$query2}

                 GROUP BY {$this->tvshow_table}.imdb_id ";

              // prepare query statement
              $stmt = $this->conn->prepare( $query );

              // execute query
              $stmt->execute();
              $row = $stmt->rowCount();
              // return values from database

          return $row;
    }


    // read products with pagination
    public function readPaging_searchTvshows_forUser($from_record_num, $records_per_page,$search,$mType,$mGenre,$mSort,$mYear,$mOrder){

        $query2 = "WHERE {$this->tvshow_table}.title LIKE  '%{$search}%' ";

        // sanitize
        $mType=htmlspecialchars(strip_tags($mType));
        $mOrder=htmlspecialchars(strip_tags($mOrder));
        $mGenre=htmlspecialchars(strip_tags($mGenre));
        $mYear=htmlspecialchars(strip_tags($mYear));


        if($mGenre!="all")              $query2 = " AND {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
        if($mYear!="all"
           && is_numeric($mYear))       $query2 = " AND  {$this->tvshow_table}.year = '{$mYear}' ";
        if(($mGenre!="all")
            && ($mYear!="all"
            && is_numeric($mYear)))
                                        $query2 =  " AND {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                     AND {$this->tvshow_table}.year = '{$mYear}' ";
        if(!empty($mType)
            && empty($query2))          $query2 = " AND {$this->tvshow_table}.type = '{$mType}' ";
            else if(!empty($mType)
                    &&!empty($query2))  $query2 .= "AND {$this->tvshow_table}.type = '{$mType}' ";



        $query ="SELECT {$this->tvshow_table}.imdb_id , {$this->tvshow_table}.title,
                        {$this->tvshow_table}.year,     {$this->tvshow_table}.runtime,
                        {$this->tvshow_table}.released, {$this->tvshow_table}.synopsis,
                        {$this->tvshow_table}.poster,   {$this->tvshow_table}.certification,
                        {$this->tvshow_table}.type,     {$this->tvshow_table}.last_updated,
                    GROUP_CONCAT({$this->tvshow_genres_table}.value)
                                  AS genres FROM {$this->tvshow_table}

                LEFT JOIN {$this->tvshow_genres_table}
                ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                        {$query2}
                GROUP BY {$this->tvshow_table}.imdb_id
                ORDER BY {$this->tvshow_table}.last_updated {$mOrder}
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
    public function readPagingcount_searchTvshows_forUser($search,$mType,$mGenre,$mSort,$mYear,$mOrder){

      $query2 = "WHERE {$this->tvshow_table}.title LIKE  '%{$search}%' ";

        // sanitize
        $mType=htmlspecialchars(strip_tags($mType));
        $mOrder=htmlspecialchars(strip_tags($mOrder));
        $mGenre=htmlspecialchars(strip_tags($mGenre));
        $mYear=htmlspecialchars(strip_tags($mYear));

        if($mGenre!="all")              $query2 = " AND {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
        if($mYear!="all"
           && is_numeric($mYear))       $query2 = " AND  {$this->tvshow_table}.year = '{$mYear}' ";
        if(($mGenre!="all")
            && ($mYear!="all"
            && is_numeric($mYear)))
                                        $query2 =  " AND {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                     AND {$this->tvshow_table}.year = '{$mYear}' ";
        if(!empty($mType)
            && empty($query2))          $query2 = " AND {$this->tvshow_table}.type = '{$mType}' ";
            else if(!empty($mType)
                    &&!empty($query2))  $query2 .= " AND {$this->tvshow_table}.type = '{$mType}' ";


        $query ="SELECT COUNT(*) as total_rows FROM {$this->tvshow_table}

                 LEFT JOIN {$this->tvshow_genres_table}
                 ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                    {$query2}
                 GROUP BY {$this->tvshow_table}.imdb_id ";

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
  public function readPaging_tvshow_forStores($sgid,$from_record_num, $records_per_page,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie){

      $query2 = "";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));


      if($mGenre!="all")              $query2 = " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->tvshow_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                   AND {$this->tvshow_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->tvshow_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->tvshow_table}.type = '{$mType}' ";

      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableTvshow}.tvshow_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableTvshow}.tvshow_id IS NOT NULL ";


      $query ="SELECT {$this->tvshow_table}.imdb_id , {$this->tvshow_table}.title,
                      {$this->tvshow_table}.year,     {$this->tvshow_table}.runtime,
                      {$this->tvshow_table}.released, {$this->tvshow_table}.synopsis,
                      {$this->tvshow_table}.poster,   {$this->tvshow_table}.certification,
                      {$this->tvshow_table}.type,
                  CASE WHEN {$this->store_tableTvshow}.tvshow_id IS NOT NULL THEN 'yes' ELSE 'no' END AS 'available',
                  GROUP_CONCAT({$this->tvshow_genres_table}.value)
                                AS genres FROM {$this->tvshow_table}

              LEFT JOIN  {$this->store_tableTvshow}
                ON ({$this->store_tableTvshow}.store_id=  (SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableTvshow}.tvshow_id = {$this->tvshow_table}.imdb_id )
              LEFT JOIN {$this->tvshow_genres_table}
                ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                {$query2}
              GROUP BY {$this->tvshow_table}.imdb_id
              ORDER BY {$this->tvshow_table}.released {$mOrder}
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
  public function readPagingcount_tvshow_forStores($sgid,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie){

      $query2 = "";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));



      if($mGenre!="all")              $query2 = " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->tvshow_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                   AND {$this->tvshow_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->tvshow_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->tvshow_table}.type = '{$mType}' ";


      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableTvshow}.tvshow_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableTvshow}.tvshow_id IS NOT NULL ";


      $query ="SELECT COUNT(*) as total_rows FROM {$this->tvshow_table}

               LEFT JOIN  {$this->store_tableTvshow}
                ON ({$this->store_tableTvshow}.store_id=(SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableTvshow}.tvshow_id = {$this->tvshow_table}.imdb_id )
               LEFT JOIN {$this->tvshow_genres_table}
                ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                  {$query2}
               GROUP BY {$this->tvshow_table}.imdb_id ";

          // prepare query statement
          $stmt = $this->conn->prepare( $query );

          // execute query
          $stmt->execute();
          $row = $stmt->rowCount();
          // return values from database
    return $row;
  }




  public function store_readRequestedTvshowsPaging($from_record_num, $records_per_page,$sgid,$status="pending"){

              $query = "SELECT *, {$this->store_table}.store_id as store_id,{$this->requested_tvshow_table}.tvshow_id as tvshow_id,
                        CASE WHEN  {$this->store_tableTvshow}.tvshow_id IS NOT NULL THEN 'yes' ELSE 'no' END AS 'tvshow_status',
                        {$this->requested_tvshow_table}.req_store_msg,{$this->requested_tvshow_table}.req_user_msg


                        FROM {$this->requested_tvshow_table}
                        LEFT JOIN {$this->tvshow_table} ON {$this->tvshow_table}.imdb_id ={$this->requested_tvshow_table}.tvshow_id
                        LEFT JOIN {$this->users_table} ON {$this->users_table}.user_id ={$this->requested_tvshow_table}.user_id
                        LEFT JOIN {$this->store_table} ON {$this->store_table}.store_id={$this->requested_tvshow_table}.store_id
                        LEFT JOIN {$this->store_tableTvshow} ON ({$this->store_tableTvshow}.store_id={$this->requested_tvshow_table}.store_id
                          AND {$this->store_tableTvshow}.tvshow_id ={$this->requested_tvshow_table}.tvshow_id)

                                  WHERE ({$this->requested_tvshow_table}.req_status = :status
                                          AND {$this->store_table}.store_google_id = :google_id )

                        ORDER BY {$this->requested_tvshow_table}.req_date  DESC
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
  public function store_readRequestedTvshowsCount($sgid,$status){

      $query = "SELECT COUNT(*) as total_rows
                FROM {$this->requested_tvshow_table}
                LEFT JOIN {$this->tvshow_table} ON {$this->tvshow_table}.imdb_id ={$this->requested_tvshow_table}.tvshow_id
                LEFT JOIN {$this->users_table} ON {$this->users_table}.user_id ={$this->requested_tvshow_table}.user_id
                LEFT JOIN {$this->store_table} ON {$this->store_table}.store_id={$this->requested_tvshow_table}.store_id
                  WHERE ({$this->requested_tvshow_table}.req_status = :status
                        AND {$this->store_table}.store_google_id = :google_id )

                ORDER BY {$this->requested_tvshow_table}.req_date  DESC";

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



  // read products with pagination
  public function readPaging_searchtvshow_forStores($sgid,$from_record_num, $records_per_page,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie,$search){

    $query2 = "WHERE {$this->tvshow_table}.title LIKE  '%{$search}%' ";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));


      if($mGenre!="all")              $query2 = " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->tvshow_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                   AND {$this->tvshow_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->tvshow_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->tvshow_table}.type = '{$mType}' ";

      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableTvshow}.tvshow_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableTvshow}.tvshow_id IS NOT NULL ";


      $query ="SELECT {$this->tvshow_table}.imdb_id , {$this->tvshow_table}.title,
                      {$this->tvshow_table}.year,     {$this->tvshow_table}.runtime,
                      {$this->tvshow_table}.released, {$this->tvshow_table}.synopsis,
                      {$this->tvshow_table}.poster,   {$this->tvshow_table}.certification,
                      {$this->tvshow_table}.type,
                  CASE WHEN {$this->store_tableTvshow}.tvshow_id IS NOT NULL THEN 'yes' ELSE 'no' END AS 'available',
                  GROUP_CONCAT({$this->tvshow_genres_table}.value)
                                AS genres FROM {$this->tvshow_table}

              LEFT JOIN  {$this->store_tableTvshow}
                ON ({$this->store_tableTvshow}.store_id=  (SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableTvshow}.tvshow_id = {$this->tvshow_table}.imdb_id )
              LEFT JOIN {$this->tvshow_genres_table}
                ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                {$query2}
              GROUP BY {$this->tvshow_table}.imdb_id
              ORDER BY {$this->tvshow_table}.released {$mOrder}
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
  public function readPagingcount_searchtvshow_forStores($sgid,$mType,$mGenre,$mSort,$mYear,$mOrder,$mMyMovie,$search){

    $query2 = "WHERE {$this->tvshow_table}.title LIKE  '%{$search}%' ";

      // sanitize
      $sgid=htmlspecialchars(strip_tags($sgid));
      $mType=htmlspecialchars(strip_tags($mType));
      $mOrder=htmlspecialchars(strip_tags($mOrder));
      $mGenre=htmlspecialchars(strip_tags($mGenre));
      $mYear=htmlspecialchars(strip_tags($mYear));
      $mMyMovie=htmlspecialchars(strip_tags($mMyMovie));



      if($mGenre!="all")              $query2 = " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})  ";
      if($mYear!="all"
         && is_numeric($mYear))       $query2 = " WHERE  {$this->tvshow_table}.year = '{$mYear}' ";
      if(($mGenre!="all")
          && ($mYear!="all"
          && is_numeric($mYear)))
                                      $query2 =  " WHERE {$this->tvshow_genres_table}.value IN ({$mGenre})
                                                   AND {$this->tvshow_table}.year = '{$mYear}' ";
      if(!empty($mType)
          && empty($query2))          $query2 = "WHERE {$this->tvshow_table}.type = '{$mType}' ";
          else if(!empty($mType)
                  &&!empty($query2))  $query2 .= "AND {$this->tvshow_table}.type = '{$mType}' ";


      if(empty($query2)&&($mMyMovie=='yes'))              $query2 =  "WHERE {$this->store_tableTvshow}.tvshow_id IS NOT NULL";
      if(!empty($query2)&&($mMyMovie=='yes'))             $query2 .= " AND {$this->store_tableTvshow}.tvshow_id IS NOT NULL ";


      $query ="SELECT COUNT(*) as total_rows FROM {$this->tvshow_table}

               LEFT JOIN  {$this->store_tableTvshow}
                ON ({$this->store_tableTvshow}.store_id=(SELECT {$this->store_table}.store_id
                                                           FROM {$this->store_table}
                                                           WHERE {$this->store_table}.store_google_id = {$sgid}) AND {$this->store_tableTvshow}.tvshow_id = {$this->tvshow_table}.imdb_id )
               LEFT JOIN {$this->tvshow_genres_table}
                ON {$this->tvshow_genres_table}.imdb_id = {$this->tvshow_table}.imdb_id
                  {$query2}
               GROUP BY {$this->tvshow_table}.imdb_id ";

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
            $query = "SELECT COUNT(*) as total_rows FROM " . $this->tvshow_table . "";

            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['total_rows'];
        }



}
?>
