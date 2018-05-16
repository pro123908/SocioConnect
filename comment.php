<?php require('./header.php');?>

<?php 
	echo "test";
	$date = getdate();
	$stmt = $connection->prepare("INSERT INTO comments (user_id, post_id, comment,createdAt) VALUES (?, ?, ?,now())");
	$stmt->bind_param("iis",$_SESSION['user_id'], $_POST['post_id'], $_POST['comment']);
	$stmt->execute();
  $stmt->close();
  $_SESSION['post_for_comments'] = $_POST['post_id'];
	redirection("main.php");
?>