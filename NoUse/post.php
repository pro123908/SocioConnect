<?php 

require_once('functions.php');

/* DEAD */

/* 
    checks if post has pic or not?
    Returns Newly inserted post
*/

if(!isset($_SESSION['user_id'])){
  redirection('index.php');
}

// Addding new post

if (isset($_POST['post'])) {
    if(isset($_FILES['file']['name'])){
    // Calling function to add post
    $name = $_FILES['file']['name']; // name of pic
    $tmp_name = $_FILES['file']['tmp_name'];
    $type = $_FILES['file']['type'];
    $extension = strtolower(pathinfo($name,PATHINFO_EXTENSION)); // Getting extension of file

    // Unique ID for image for storing
    $uniqueID = uniqid();

    if (isset($name)) {
        if (!empty($name)) {
            // Checking the format of the image uploaded
            if (($extension == "jpg" || $extension == "jpeg" || $extension == "png") && ($type == "image/png" || $type == "image/jpeg")) {
                // Location where to save the image
                $location = 'assets/postPics/';
                if (move_uploaded_file($tmp_name, $location.$uniqueID.'.'.$extension)) {
                    $path = $location.$uniqueID.'.'.$extension; //Complete path of image
                    newPost($_POST['post'],$path);
                }
            }
        }
    }
  }
    
    else{
        // If no picture was attached with post
      newPost($_POST['post']);
    }  
}


?>