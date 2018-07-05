<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

$friends = array();
$sorted_friends = array();
$counter = 0;
removeFriend($_POST['friendId'],"no redirection"); 
$friends_query = queryFunc("SELECT * from friends where user1 = {$_SESSION['user_id']} OR user2 = ".$_SESSION['user_id']." ".$_POST['conflict']);

if(isData($friends_query)){
    while($row = isRecord($friends_query)){
        $friend_id = ($_SESSION['user_id'] == $row['user1']) ? $row['user2'] : $row['user1'] ;
        $friend = queryFunc("SELECT user_id,profile_pic,active_ago ,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$friend_id'");   
        $friend = isRecord($friend);
       
        array_push($friends,$friend);
      }
      if(count($friends) > 1)
        sortArrayByKey($friends,false);
      foreach ($friends as $friend){
        $time = activeAgo($friend['user_id']);

        $stateClass = 'state-off';

        if ($time == 'Just Now') {
            $time = 'Now';
            $stateClass = 'state-on';
        }

        $sorted_friends[$counter] = array('user_id'=>$friend['user_id'],'name'=>$friend['name'],'profile_pic'=>$friend['profile_pic'],'time' => $time,'state' => $stateClass);
        $counter += 1;
        }

        echo json_encode($sorted_friends);

    }else{
        echo '{"notEmpty" : "Bilal"}';   
    }
    

?>