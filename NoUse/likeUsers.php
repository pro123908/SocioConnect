<?php

/* DEAD */

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}


// Getting the name of the persons who liked a certain post
if (isset($_GET['postID'])) {
    $postID = $_GET['postID']; // ID of the post

    // Getting all likes of that particular post
    $queryResult = queryFunc("SELECT user_id FROM likes WHERE post_id='$postID'");
    $counter = 0;
    if (isData($queryResult)) {
        while ($row = isRecord($queryResult)) {
            $userID = $row['user_id']; // Getting id of each user who liked the post

            // Getting name of that user
            $queryName = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$userID'");

            $nameResult = isRecord($queryName);
            $name = $nameResult['name']; // name of that user

            // Inserting user name in array
            $data[$counter] = array('name' => $name);

            // Moving to the next user by incrementing
            $counter += 1;
        }

        // Simple converting the array to JSON format and passing it
        echo json_encode($data);

        
    } else { // If there were no data
        echo '{"notEmpty" : "Bilal"}';
    }
}
?>