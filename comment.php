

<?php 

require('functions.php'); 


	$userID = $_SESSION['user_id'];
	$postID = $_POST['post_id'];
	$comment = $_POST['comment'];

	$commentID = addComment($userID,$postID,$comment);

	echo $commentID;
	

?>