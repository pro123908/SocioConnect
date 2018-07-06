<?php 

require_once('functions.php'); 

/* DEAD */

/* 
    Adds comment to database
    returns ID of the inserted comment
*/

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

$userID = $_SESSION['user_id']; //Current User who is commenting
$postID = $_POST['post_id'];	// Post being commented 
$comment = $_POST['comment']; // Comment text

// Passing above values to this function and getting the ID of newly inserted comment as a result.
$commentID = addComment($userID,$postID,$comment);

// Giving commentID back to ajax function as a response so it can be added to the post without reloading
echo $commentID;

?>