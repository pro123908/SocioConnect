<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

removeFriend($_POST['friendId'],"no redirection"); 
$friends = queryFunc("SELECT * from friends where user1 = ".$_SESSION['user_id']." OR user2 = ".$_SESSION['user_id']);
while($row = isRecord($friends)){
    $friend_id = ($_SESSION['user_id'] == $row['user1']) ? $row['user2'] : $row['user1'] ;
    $friend = queryFunc("SELECT user_id,profile_pic,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$friend_id'");   
    $friend = isRecord($friend);

    $friends[$counter] = array('user_id'=>$friend['user_id'],'name'=>$friend['name'],'profile_pic'=>$friend['profile_pic']);

    $counter += 1;
  }
    // Checking if there were comments of other users in last one second
    if ($counter != 0) {
        // Simple converting the array to JSON format and passing it
        echo json_encode($friends);
    } else {
        // If there were no comments inserted, then just giving a JSON response for avoiding error 
        echo '{"notEmpty" : "Bilal"}';
    }