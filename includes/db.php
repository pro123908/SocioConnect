<?php 
  
  $db['db_host'] = 'localhost'; // Database host
  $db['db_user'] = 'root';  // Database User
  $db['db_pass'] = 'home123'; // Database Password
  $db['db_name'] = 'socioconnect';  // Database Name

  // Defining elements of db array as constants
  foreach($db as $key => $value){
    define(strtoupper($key),$value);
  }

  // Initializing the connection
  $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
  
  // If connection failed to database
  if(!$connection){
    die('Connection Failed to database'.mysqli_error($connection));
  }
  
?>