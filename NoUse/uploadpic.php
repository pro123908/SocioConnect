<?php   

    require_once("functions.php"); 

    if(!isset($_SESSION['user_id'])){
        redirection('index.php');
    }

    /* DEAD */
    
    if(isset($_FILES['profile_pic'])){
    // When profile pic is uploaded

    $name = $_FILES['profile_pic']['name'];
    $tmp_name = $_FILES['profile_pic']['tmp_name'];
    $type = $_FILES['profile_pic']['type'];
    $extension = strtolower(pathinfo($name,PATHINFO_EXTENSION)); // Getting extension of file

    // $typeImage = explode('/', $type);
    $uniqueID = uniqid();

    if (isset($name)) {
        if (!empty($name)) {
            // Checking the format of the image uploaded
            if (($extension == "jpg" || $extension == "jpeg" || $extension == "png") && ($type == "image/png" || $type == "image/jpeg")) {
                // Location where to save the image
                $location = 'assets/profile_pictures/';
                if (move_uploaded_file($tmp_name, $location.$uniqueID.'.'.$extension)) {
                    $path = $location.$uniqueID.'.'.$extension;
                    profilePicChange($path);
                    echo $path;
                }
            }
        }
    }

  

}elseif(isset($_FILES['cover_pic'])){
    $name = $_FILES['cover_pic']['name'];
    $tmp_name = $_FILES['cover_pic']['tmp_name'];
    $type = $_FILES['cover_pic']['type'];
    $extension = strtolower(pathinfo($name,PATHINFO_EXTENSION)); // Getting extension of file

    // $typeImage = explode('/', $type);
    $uniqueID = uniqid();

    if (isset($name)) {
        if (!empty($name)) {
            // Checking the format of the image uploaded
            if (($extension == "jpg" || $extension == "jpeg" || $extension == "png") && ($type == "image/png" || $type == "image/jpeg")) {
                // Location where to save the image
                $location = 'assets/cover_pictures/';
                if (move_uploaded_file($tmp_name, $location.$uniqueID.'.'.$extension)) {
                    $path = $location.$uniqueID.'.'.$extension;
                    coverPicChange($path);
                    echo $path;
                }
            }
        }
    }
}
    
?>