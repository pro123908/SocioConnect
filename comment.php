<?php require('./header.php');?>

<?php 
	$stmt = $connection->prepare("INSERT INTO comments (user_id, post_id, comment,createdAt) VALUES (?, ?, ?,now())");
	$stmt->bind_param("iis",$_SESSION['user_id'], $_POST['post_id'], $_POST['comment']);
	$stmt->execute();
	$stmt->close();
?>