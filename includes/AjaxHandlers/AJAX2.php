<?php

require_once dirname(__FILE__,2) . '/functions.php';

if(!isset($_SESSION['user_id']) && !isset($_GET['check_answer']) && !isset($_GET['validateAnswer']) && !isset($_GET['saveNewPassword'])){
    redirection('../../index.php');
}

if (isset($_GET['addFriend'])) {

    if (!isset($_GET['id'])) {
        redirection('../../main.php');
    }

    $friend = queryFunc("INSERT INTO friend_requests (to_id, from_id) values({$_POST['id']},{$_SESSION['user_id']})");

    notification($_SESSION['user_id'], $_POST['id'], 0, 'request');

} elseif (isset($_GET['cancelReq'])) {

    if (!isset($_GET['id'])) {
        redirection('../../main.php');
    }

    queryFunc("DELETE FROM friend_requests WHERE to_id ={$_POST['id']} AND from_id ={$_SESSION['user_id']}");

    queryFunc("DELETE from notifications where s_user_id={$_SESSION['user_id']} AND d_user_id={$_POST['id']} AND typeC='request'");

} elseif (isset($_GET['deleteConvo'])) {

    if (isset($_POST['id'])) {
        deleteConvo($_POST['id']);
        if ($_POST['id'] == $_POST['urlID']) {
            echo "Reload the page";
        } else {
            showRecentChats();
            if ($_SESSION['last_message_retrieved_for_recent_convos'] == 0) {
                echo "Reload the page";
            }

        }
    }
} elseif (isset($_GET['loadPosts'])) {

    $limit = 10;
    showPosts($_GET['flag'], $_GET['page'], $limit);

} elseif (isset($_GET['loadRA'])) {

    $limit = 10;
    if (isset($_GET['id'])) {
        showRecentActivities($_GET['page'], $limit, 2, $_GET['id']);
    } else {
        showRecentActivities($_GET['page'], $limit, 2);
    }

} elseif (isset($_GET['recentActivity'])) {

    // activity_type == 0 ==> Like
    // activity_type == 1 ==> Comment
    // activity_type == 2 ==> Post
    // activity_type == 3 ==> Added Friend
    // activity_type == 4 ==> Unlike

    $userLoggedIn = $_SESSION['user_id'];
    $activity_type = $_POST['activity_type'];

    if ($activity_type == 4) {
        // if unlike activity

        // Target Content ID or IDs like post etc
        $target_id = $_POST['target_id'];

        // Deleting the like activity
        queryFunc("DELETE from recent_activities where activity_at_id = '$target_id' AND user_id = '$userLoggedIn' and activity_type = 0");

        //Dislaying Activites
        showRecentActivities(1, 10, 1);
    } else {
        if ($activity_type == 2) {
            // If any post is added
            $target_id = queryFunc("SELECT post_id from posts order by post_id desc limit 1");
            // target content
            $target_id = isRecord($target_id);
            $target_id = $target_id['post_id'];
        } else {
            // if activity was like,comment or friend addtion
            $target_id = $_POST['target_id'];
        }

        // Storing activity in database
        queryFunc("INSERT into recent_activities (activity_type, activity_at_id, user_id) values ('$activity_type','$target_id','$userLoggedIn')");

        // Displaying activity
        addActivity($activity_type, $target_id, $userLoggedIn);
    }

} elseif (isset($_GET['removeFriend'])) {

    $friends = array();
    $sorted_friends = array();
    $counter = 0;
    removeFriend($_POST['friendId'], "no redirection");
    $friends_query = queryFunc("SELECT * from friends where user1 = {$_SESSION['user_id']} OR user2 = " . $_SESSION['user_id'] . " " . $_POST['conflict']);

    if (isData($friends_query)) {
        while ($row = isRecord($friends_query)) {
            $friend_id = ($_SESSION['user_id'] == $row['user1']) ? $row['user2'] : $row['user1'];
            $friend = queryFunc("SELECT user_id,profile_pic,active_ago ,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$friend_id'");
            $friend = isRecord($friend);

            array_push($friends, $friend);
        }
        if (count($friends) > 1) {
            sortArrayByKey($friends, false);
        }

        foreach ($friends as $friend) {
            $time = activeAgo($friend['user_id']);

            $stateClass = 'state-off';

            if ($time == 'Just Now') {
                $time = 'Now';
                $stateClass = 'state-on';
            }

            $sorted_friends[$counter] = array('user_id' => $friend['user_id'], 'name' => $friend['name'], 'profile_pic' => $friend['profile_pic'], 'time' => $time, 'state' => $stateClass);
            $counter += 1;
        }

        echo json_encode($sorted_friends);

    } else {
        echo '{"notEmpty" : "Bilal"}';
    }
}
else if(isset($_GET['check_answer'])){
            
        $email = queryFunc("SELECT question from users where email = '{$_GET['email']}'");
        if(isData($email)){
            $email = isRecord($email);
            echo $email['question'];
        }else{
            echo "No";
        }
    }
else if(isset($_GET['validateAnswer'])){    
    $answer = queryFunc("SELECT answer from users where email = '{$_GET['email']}'");
    if(isData($answer)){
        $answer = isRecord($answer);
        if($answer['answer'] == $_GET['answer'])
            echo "Yes";
        else
            echo "No";    
    }else{
        echo "No";
    }
}
else if(isset($_GET['saveNewPassword'])){    
    if(isset($_POST['password'])){
        $password = hashString($_POST['password']);
        $email = $_POST['email'];
        queryFunc("update users set password = '$password' where email = '$email'");
        echo "ok";
    }
}
else if(isset($_GET['refreshRecentUploads'])){    
        getUploadedPics($_SESSION['user_id']);
    }
?>