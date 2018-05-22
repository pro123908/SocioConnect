<?php

require_once('functions.php');

if(isset($_GET['postID'])){
  $postID = $_GET['postID'];

  $queryResult = queryFunc("SELECT user_id FROM likes WHERE post_id='$postID'");
  $counter = 0;
  if (isData($queryResult)) {
      while ($row = isRecord($queryResult)) {
          $userID = $row['user_id'];

          $queryName = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$userID'");

          $nameResult = isRecord($queryName);
          $name = $nameResult['name'];

          $data[$counter] = array('name' => $name);
          $counter += 1;
      }

      echo json_encode($data);
  }else{
    echo '{"notEmpty" : "Bilal"}';
  }
}

?>