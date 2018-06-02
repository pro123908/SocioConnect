<?php 

require_once('functions.php');

$recentUserIds = getRecentChatsUserIds(); //IDS of users
$recentUsernames = getRecentChatsUsernames($recentUserIds); // Names of users
$counter = 0;
while($counter < sizeof($recentUsernames)){
    $lastMessageDetails = getPartnersLastMessage($recentUserIds[$counter]);
    $from = $lastMessageDetails['user_from'];
    if($from == $_SESSION['user_id'])
        $from = "You ";
    else    
        $from = getUserFirstAndLastName($from);
    $msg = $lastMessageDetails['body'];
    $at =  timeString(differenceInTime($lastMessageDetails['dateTime']));
 
    $data[$counter] = array('fromID'=>$recentUserIds[$counter],'partner'=>$recentUsernames[$counter],'from'=>$from,'msg'=>$msg,'at'=>$at);

    $counter += 1;
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

