<?php 
  
  $db['db_host'] = 'localhost';
  $db['db_user'] = 'root';
  $db['db_pass'] = 'home123';
  $db['db_name'] = 'socioconnect';

  foreach($db as $key => $value){
    define(strtoupper($key),$value);
  }

  $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
  
  if(!$connection){
    die('Connection Failed to database'.mysqli_error($connection));
  }

//   ob_start();

// session_start();
// //session_destroy();

// defined("DB_HOST") ? null : define("DB_HOST","localhost");

// defined("DB_USER") ? null : define("DB_USER","root");

// defined("DB_PASS") ? null : define("DB_PASS","");

// defined("DB_NAME") ? null : define("DB_NAME","SocioConnect");

// $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

?>