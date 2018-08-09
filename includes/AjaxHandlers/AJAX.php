<?php

require_once dirname(__FILE__,2) . '/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirection('../../index.php');
}

// For loading notifcaitons without reloading the page
if (isset($_GET['noti'])) {

    $counter = 0;
    $lastNotiID = $_SESSION['last_noti_id'];
    $userID = $_SESSION['user_id'];

    $queryResult = queryFunc("SELECT * from notifications WHERE (seen !=1 AND noti_id > $lastNotiID) AND d_user_id='$userID' order by noti_id desc");

    if (isData($queryResult)) {
        $notiCounter = 0;
        while ($row = isRecord($queryResult)) {
            // Selecing notificaiton based on
            // If is your notification and it is not already been seen

            $person = $row['s_user_id']; // Person who generated the notification
            $post = $row['post_id']; // Post ID
            $type = $row['typeC']; // type of the notification
            $notiID = $row['noti_id']; // Notification

            if ($notiCounter == 0) {
                $_SESSION['last_noti_id'] = $notiID;
                $notiCounter = 1;
            }

            // Selecting name of the person who geneerated the notification
            $personQuery = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$person'");
            $sPerson = isRecord($personQuery);
            $sPersonName = $sPerson['name']; // Name of that person

            $personPic = getUserProfilePic($person);

            // Inserting notifications into the array
            // PostID,type,notification ID and name of the source person
            $data[$counter] = array('lastID' => $lastNotiID, 'postID' => $post, 'type' => $type, 'notiID' => $notiID, 'name' => $sPersonName, 'profilePic' => $personPic);

            // Moving on to the next notificaiton if any
            $counter += 1;
        }

        echo json_encode($data);
    } else {
        echo '{"notEmpty" : "Bilal"}';
    }
} elseif (isset($_GET['like'])) {

    $lastLikeID = $_SESSION['last_like_id'];

// Getting likes of other users on posts without reloading

// Getting recently inserted likes which were inserted under 3 seconds
    $queryResult = queryFunc("SELECT * from likes WHERE like_id > $lastLikeID");
    $counter = 0;
    if (isData($queryResult)) {
        while ($row = isRecord($queryResult)) {
            if ($_SESSION['user_id'] != $row['user_id']) { // Checking if you are not the one who liked the post
                $postID = $row['post_id'];

                $_SESSION['last_like_id'] = $row['like_id'];

                // Getting total count of the likes of the current post
                $queryOther = queryFunc("SELECT count(*) as count FROM likes WHERE post_id='$postID'");
                $likesResult = isRecord($queryOther);
                $likes = $likesResult['count'];

                // Inserting data into the array to pass , postID and likesCount
                $data[$counter] = array('postID' => $postID, 'likes' => $likes);
                $counter += 1;
            }
        }
        // Checking if there were likes of other users in last one second
        if ($counter != 0) {
            // Simple converting the array to JSON format and passing it
            echo json_encode($data);
        } else {
            // If there were no likes inserted, then just giving a JSON response for avoiding error
            echo '{"notEmpty" : "Bilal"}';
        }
        // If no user inserted likes or like
    } else {
        echo '{"notEmpty" : "Bilal"}';
    }

} else if (isset($_GET['comment'])) {

    // For loading comments from different users using ajax without reloading the page
    $counter = 0;

    $lastCommentID = $_SESSION['last_comment_id'];

// Getting comments that have been inserted into the database one second before
    $queryResult = queryFunc("SELECT * FROM comments WHERE comment_id > $lastCommentID");

    if (isData($queryResult)) {
        while ($row = isRecord($queryResult)) {

            // Storing ID of last comment
            $_SESSION['last_comment_id'] = $row['comment_id'];

            $profilePic = getUserProfilePic($row['user_id']);

            // Making sure that the comment is not yours otherwise it will be displayed twice
            if ($_SESSION['user_id'] != $row['user_id']) {
                $userID = $row['user_id']; // Other user that inserted the comment

                // Getting name of that user
                $queryName = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name from users WHERE user_id='$userID'");
                $name = isRecord($queryName);

                // Making an array to pass on data
                // Passing postID,comment and name of the person
                $data[$counter] = array('commentID' => $row['comment_id'], 'commentUserID' => $row['user_id'], 'postID' => $row['post_id'], 'comment' => $row['comment'], 'profilePic' => $profilePic, 'name' => $name['name']);

                // In case there were more than one comment inserted in last second then loop will run that many times

                // counter incremented
                $counter += 1;
            }
        }

        echo json_encode($data);

    } else {
        echo '{"notEmpty" : "Bilal"}';
    }

} elseif (isset($_GET['message'])) {
    // Storing message to the database
    if (isset($_POST['partner'])) {
        $_POST['partner'] = clearString($_POST['partner']);
        $_POST['messageBody'] = clearString($_POST['messageBody']);
        sendMessage($_POST['partner'],$_SESSION['user_id'], $_POST['messageBody']);
        echo $_POST['partner'];
    }

// For message Refresh
    if (isset($_GET['id'])) {
        $userID = $_SESSION['user_id'];
        $partnerID = clearString($_GET['id']);
        $last_msg_id = $_SESSION['last_msg_id'];

        $profilePicYou = getUserProfilePic($partnerID);

        //   $profilePicQueryYou = queryFunc("SELECT profile_pic from users where user_id='$partnerID'");
        //   $profilePicQueryYouResult = isRecord($profilePicQueryYou);
        //   $profilePicYou = $profilePicQueryYouResult['profile_pic'];

        $queryResult = queryFunc("SELECT id,user_to,user_from,body from messages WHERE id>'$last_msg_id' AND user_to='$userID' AND user_from='$partnerID' AND deleted = 0");
        $counter = 0;

        if (isData($queryResult)) {
            while ($row = isRecord($queryResult)) {
                $messageBody = $row['body'];

                // Inserting data into the array to pass , postID and likesCount
                $data[$counter] = array('message' => $messageBody, 'partnerID' => $partnerID, 'pic' => $profilePicYou);
                $counter += 1;
                $_SESSION['last_msg_id'] = $row['id'];
                $messageOpenedQuery = queryFunc("UPDATE messages set opened=1 where id={$row['id']}");

            }

            echo json_encode($data);

        } else {
            echo '{"notEmpty" : "Bilal"}';
        }
    }

} elseif (isset($_GET['recentConvo'])) {

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
                    $_SESSION['last_message_retrieved_for_recent_convos'] = $row['id'];
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
            $at = getTime($lastMessageDetails['dateTime']);

            $data[$counter] = array('fromID' => $recentUserIds[$counter], 'partner' => $recentUsernames[$counter], 'from' => $from, 'msg' => $msg, 'at' => $at, 'pic' => $profilePic);

            $counter += 1;
        }
        echo json_encode($data);

    } else {
        echo '{"notEmpty" : "Bilal"}';
    }

}
