<?php
  session_start();

  include "db.php";
  include "functions.php";

  if($_POST['submit']){
    
    $email = mysqli_real_escape_string($connection,$_POST['email']);
    //Hash of password is returned
    $password = hashString(mysqli_real_escape_string($connection,$_POST['password']));
    

    $queryResult = queryFunc("SELECT * FROM users WHERE email = '$email'");

  
    if(!isData($queryResult)){
      echo "User doesn't exist";
    }
    else{
        $row = isRecord($queryResult);
      // Hash from database is compared with the hash created now.
      if($row['password'] === $password){
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user'] = $row['first_name'].' '.$row['last_name'];
        
        redirection('main.php');
        
      }else{
        echo 'Wrong Password';
      }
    
  }
    
  }
  //If user has already logged In and coming from another page to here
  else if(isset($_SESSION['user_id'])){

  }
  // If user is coming directly to this page without authentication
  else {
      redirect("index.php");
  }


?>

