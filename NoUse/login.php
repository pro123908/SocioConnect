<?php

  // Checked 

    /* DEAD */

    
  require_once('functions.php');

  

  // POST request made to this file
  // Passed login information with the request

  if ($_POST['submit']) { // If form is submitted
      // Email of the user
      $email = mysqli_real_escape_string($connection, $_POST['email']);
      //Hash of password is returned
      $password = hashString(mysqli_real_escape_string($connection, $_POST['password']));
    
      // Checking if user exists or getting that user from database
      $queryResult = queryFunc("SELECT * FROM users WHERE email = '$email'");

      
      if (!isData($queryResult)) {
          // If user doesn't exist
          echo "User doesn't exist";
      } else {
          $row = isRecord($queryResult);
          // Hash from database is compared with the hash created now.
          if ($row['password'] === $password) {
              turnOnline($row['user_id']);
              $_SESSION['user_id'] = $row['user_id'];
              $_SESSION['user'] = $row['first_name'].' '.$row['last_name'];
              // If password matches, redirect to main.php
              redirection('main.php');
          } else {
              echo 'Wrong Password';
          }
      }
  }
  //If user has already logged In and coming from another page to here
  elseif (isset($_SESSION['user_id'])) {
      redirection('main.php');
  }
  // If user is coming directly to this page without authentication
  else {
      redirection("index.php");
  }


?>

