<?php   

    require_once("functions.php"); 

    if(!isset($_SESSION['user_id'])){
        redirection('index.php');
    }
    
    // When profile pic is uploaded

    $name = $_FILES['file']['name'];  // Getting the name of the file
    $tmp_name = $_FILES['file']['tmp_name'];    // Storing it at  temp location
    $type = $_FILES['file']['type']; // Getting file type
    $extension = strtolower(pathinfo($name,PATHINFO_EXTENSION)); // Getting extension of file
    
    if(isset($name)){
	    if(!empty($name)){
            // Checking the format of the image uploaded
            if(($extension == "jpg" || $extension == "jpeg" || $extension == "png") && ($type == "image/png" || $type == "image/jpeg")){
                // Location where to save the image
                $location = 'assets/profile_pictures/';
                if(move_uploaded_file($tmp_name, $location.$name)){
                    $_SESSION['dp_upload_message'] = 'Uploaded';
                    $path = $location.$name;
    
                    global $connection;             
                    // Setting the location of profile pic in database
                    $stmt = $connection->prepare("update users set profile_pic = ? where user_id =?");
                    $stmt->bind_param("si",$path,$_SESSION['user_id']);
                    $stmt->execute();
                    $stmt->close();
                }
            } 
            else
	            $_SESSION['dp_upload_message'] = "Only JPEG or PNG files are allowed!";	
        }    
        else
	        $_SESSION['dp_upload_message'] = "Please Choose a file";	
    }
    redirection("timeline.php");
?>