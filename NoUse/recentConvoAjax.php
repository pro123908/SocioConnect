<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}


$recentUserIds = array();
//Getting ids of all the users where messages are received from
$lastMsg = $_SESSION['last_message_retrieved_for_recent_convos'];

$senderOfRecentMsgs = queryFunc("SELECT id,user_from,user_to FROM messages where (user_to = {$_SESSION['user_id']} or user_from = {$_SESSION['user_id']}) AND id > $lastMsg AND deleted = 0 ORDER BY id DESC");

$flag = 0; 
if (isData($senderOfRecentMsgs)) {
    while ($row = isRecord($senderOfRecentMsgs)) {
        //if user logged in is the sender then store reciever's id, else store sender's id
        $idToPush = ($row['user_from'] == $_SESSION['user_id'] ? $row['user_to'] : $row['user_from']);
        //Check whether that sender is already in the list, if not, only then push his id
        if (array_search($idToPush, $recentUserIds) === false) {
            array_push($recentUserIds, $idToPush);
            if ($flag == 0) {
                $_SESSION['last_message_retrieved_for_recent_convos'] = $row['id'] ;
                $flag = 1;
            }
        }
    }
    
    $recentUsernames = getRecentChatsUsernames($recentUserIds); // Names of users
    $counter = 0;
    while ($counter < sizeof($recentUsernames)) {
        $lastMessageDetails = getPartnersLastMessage($recentUserIds[$counter]);
        $profilePic = getUserProfilePic($recentUserIds[$counter]);
        $from = $lastMessageDetails['user_from'];
        if ($from == $_SESSION['user_id']) {
            $from = "You : ";
        } else {
            $from = '';
        }
    
        $msg = $lastMessageDetails['body'];
        $at =  getTime($lastMessageDetails['dateTime']);
        
 
        $data[$counter] = array('fromID'=>$recentUserIds[$counter],'partner'=>$recentUsernames[$counter],'from'=>$from,'msg'=>$msg,'at'=>$at,'pic' => $profilePic);

        $counter += 1;
    }
      echo json_encode($data);

}
else{
    echo '{"notEmpty" : "Bilal"}';
}

?>