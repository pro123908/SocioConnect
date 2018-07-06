<?php
    require_once('functions.php');


    /* DEAD */

    /*
        Take action based on action provided and return path
    */

    if(!isset($_SESSION['user_id'])){
        redirection("index.php");
    }

    if (isset($_POST['postID'])) {
        global $connection;
        $post_body = mysqli_real_escape_string($connection, $_POST['postContent']);
        $post_id = $_POST['postID'];
        $action = $_POST['action'];

        if($action == "new" ){
            // Adding new pic to post
            $name = $_FILES['file']['name'];
            $tmp_name = $_FILES['file']['tmp_name'];
            $type = $_FILES['file']['type'];
            $extension = strtolower(pathinfo($name,PATHINFO_EXTENSION)); // Getting extension of file
            $uniqueID = uniqid();        
          
            // Checking the format of the image uploaded
            if (($extension == "jpg" || $extension == "jpeg" || $extension == "png") && ($type == "image/png" || $type == "image/jpeg")) {
             
                // Location where to save the image
                $location = 'assets/postPics/';
                if (move_uploaded_file($tmp_name, $location.$uniqueID.'.'.$extension)) {
                    $path = $location.$uniqueID.'.'.$extension; 
                }
            }
        }       
        if($action == "keep"){
            // Keeping the current post pic
            queryFunc("UPDATE posts set post = '{$post_body}', edited = 1 where post_id ={$post_id}");
            $picPathQuery = queryFunc("SELECT pic from posts where post_id = {$post_id}");
            $picPath = isRecord($picPathQuery);
            // If path is null then store ""
            $path = ($picPath['pic'] != "") ? $picPath['pic'] : "";
        }
        else if($action == 'editText'){
            // If only text is edited
            $path = "";
        }
        else{
            if($action == "remove") // if pic is removed
                $path  = "";


            queryFunc("UPDATE posts set post = '{$post_body}', edited = 1, pic='{$path}' where post_id ={$post_id}");
        }
        echo $path;
   }  
?>


