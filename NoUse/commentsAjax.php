<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

// For loading comments from different users using ajax without reloading the page
$counter = 0;

$lastCommentID = $_SESSION['last_comment_id'];

// Getting comments that have been inserted into the database one second before
$queryResult = queryFunc("SELECT * FROM comments WHERE comment_id > $lastCommentID");



if (isData($queryResult)) {




    while ($row = isRecord($queryResult)) {

        // Storing ID of last comment
        $_SESSION['last_comment_id'] = $row['comment_id'];

        $profilePic = getUserProfilePic($row['user_id']);

        // Making sure that the comment is not yours otherwise it will be displayed twice
        if ($_SESSION['user_id'] != $row['user_id']) {
            $userID = $row['user_id']; // Other user that inserted the comment



            
            // Getting name of that user 
            $queryName = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name from users WHERE user_id='$userID'");
            $name =  isRecord($queryName);

            // Making an array to pass on data
            // Passing postID,comment and name of the person
            $data[$counter] = array('commentID' => $row['comment_id'],'commentUserID' => $row['user_id'],'postID'=>$row['post_id'],'comment'=>$row['comment'],'profilePic' => $profilePic,'name'=>$name['name']);

            // In case there were more than one comment inserted in last second then loop will run that many times

            // counter incremented
            $counter += 1;
    }
}

// Checking if there were comments of other users in last one second
if ($counter != 0) {
    // Simple converting the array to JSON format and passing it
    echo json_encode($data);
} else {
    // If there were no comments inserted, then just giving a JSON response for avoiding error 
    echo '{"notEmpty" : "Bilal"}';
}
// If no user inserted comments or comment
} else {
echo '{"notEmpty" : "Bilal"}';
}


?>