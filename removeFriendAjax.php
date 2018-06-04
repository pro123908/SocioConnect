<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

removeFriend($_POST['friendId'],"no redirection");
$friends = array();
$friends_array = queryFunc("SELECT friends_array from users where user_id = ".$_SESSION['user_id']);
$friends_array = isRecord($friends_array);
$friends_array = $friends_array['friends_array'];
$friends_array = explode(",",$friends_array);
$counter = 0;
while($counter < sizeof($friends_array)){
    
    $friend = queryFunc("SELECT user_id,profile_pic,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$friends_array[$counter]'");   
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