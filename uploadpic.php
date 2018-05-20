<?php   include "functions.php"; ?>

<?php 
    $name = $_FILES['file']['name'];
    $tmp_name = $_FILES['file']['tmp_name'];
    $type = $_FILES['file']['type'];
    $extension = strtolower(pathinfo($name,PATHINFO_EXTENSION));
    if(isset($name)){
	    if(!empty($name)){
            if(($extension == "jpg" || $extension == "jpeg" || $extension == "png") && ($type == "image/png" || $type == "image/jpeg")){
                $location = 'assets/profile_pictures/';
                if(move_uploaded_file($tmp_name, $location.$name)){
                    $_SESSION['dp_upload_message'] = 'Uploaded';
                    $path = $location.$name;
    
                    global $connection;             
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