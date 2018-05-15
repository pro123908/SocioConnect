<?php
  session_start();
  include "db.php";
  include "functions.php";
  

  if(isset($_POST['submit'])){
    
    $username = mysqli_real_escape_string($connection,$_POST['username']);
    $password = hashString(mysqli_real_escape_string($connection,$_POST['password']));
    echo "password : ".$password;

    $queryResult = queryFunc("INSERT INTO users(username,password) VALUES('$username','$password')");
    
    $queryResult2 = queryFunc("SELECT user_id from users where username='$username'");

    if($queryResult && $queryResult2){
      $row = isRecord($queryResult2);
      $_SESSION['user'] = $username;
      $_SESSION['user_id'] = $row['user_id'];
      redirection('main.php');
    }
  

  }

?>

