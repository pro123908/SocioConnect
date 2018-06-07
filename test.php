<?php 
  require_once('functions.php');

  $query = queryFunc("SELECT * from users");

  while($row = isRecord($query)){
    test($row);
  }

  function test($row){
    echo $row['first_name'].' '.$row['last_name'] .'<br>';
  }
?>