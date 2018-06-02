<?php 

require_once('functions.php');

$recentUserIds = array();
//Getting ids of all the users where messages are received from
$lastMsg = $_SESSION['last_message_retrieved_for_recent_convos'];
$senderOfRecentMsgs = queryFunc("SELECT id,user_from,user_to FROM messages where (user_to = ".$_SESSION['user_id']." or user_from = ".$_SESSION['user_id'].") AND id > ".$lastMsg ." ORDER BY id DESC");
// 
$flag = 0; 
if(isData($senderOfRecentMsgs)){
    while($row = isRecord($senderOfRecentMsgs)){   
        //if user logged in is the sender then store reciever's id, else store sender's id
        $idToPush = ($row['user_from'] == $_SESSION['user_id'] ? $row['user_to'] : $row['user_from']);
        //Check whether that sender is already in the list, if not, only then push his id
        if(array_search($idToPush,$recentUserIds) === false ){
            array_push($recentUserIds,$idToPush);
            if($flag == 0 ){
                $_SESSION['last_message_retrieved_for_recent_convos'] = $row['id'] ;
                $flag = 1;
            }     
        }
    }
    
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

}
else{
    echo '{"notEmpty" : "Bilal"}';
}