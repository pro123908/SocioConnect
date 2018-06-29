<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
  redirection('index.php');
}

// Addding new post
if (isset($_POST['post'])) {
    // Checking if post has pic too?
    if(isset($_FILES['file']['name'])){
    // Calling function to add post
    $name = $_FILES['file']['name']; // name of file
    $tmp_name = $_FILES['file']['tmp_name']; // storing at temp location
    $type = $_FILES['file']['type']; // type of file

    $extension = strtolower(pathinfo($name,PATHINFO_EXTENSION)); // Getting extension of file

    // Genearting Unique ID for the pic name
    $uniqueID = uniqid();

    if (isset($name)) { // If name is not null
        if (!empty($name)) { // If variable has value

            // Checking the format of the image uploaded
            if (($extension == "jpg" || $extension == "jpeg" || $extension == "png") && ($type == "image/png" || $type == "image/jpeg")) {
                // Location where to save the image
                $location = 'assets/postPics/';
                //Moving uploaded file to new location
                if (move_uploaded_file($tmp_name, $location.$uniqueID.'.'.$extension)) {
                    $path = $location.$uniqueID.'.'.$extension; // Storing path of file
                    //Calling method with post text and Pic
                    newPost($_POST['post'],$path);
                }
            }
        }
    }
  }
    
    else{
        //Calling method with post text only
      newPost($_POST['post']);
    }  
}


?>