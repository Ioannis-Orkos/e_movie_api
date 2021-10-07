<?php
  // show error reporting
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  // home page url
  $home_url="http://localhost/e_movie_api/";

  // page given in URL parameter, default page is one
  $page = isset($_GET['page']) ? $_GET['page'] : 1;

  if (!is_numeric($page)) {

      // set response code - 404 Not found
      http_response_code(404);

      // tell the user products does not exist
      echo json_encode( array("message" => "page number should be numeric"));

      die();
  }

  // set number of records per page
  $records_per_page = 20;
  // calculate for the query LIMIT clause
  $from_record_num = ($records_per_page * $page) - $records_per_page;

?>
