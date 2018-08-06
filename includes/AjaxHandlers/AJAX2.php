<?php

require_once dirname(__FILE__,2) . '/functions.php';

if(!isset($_SESSION['user_id']) && !isset($_GET['check_answer']) && !isset($_GET['validateAnswer']) && !isset($_GET['saveNewPassword']) && !isset($_GET['checkAttempts'])){
    redirection('../../index.php');
}

if (isset($_GET['addFriend'])) {

    if (!isset($_GET['id'])) {
        redirection('../../main.php');
    }
    $_POST['id'] = clearString($_POST['id']);
    $friend = queryFunc("INSERT INTO friend_requests (to_id, from_id) values({$_POST['id']},{$_SESSION['user_id']})");

    notification($_SESSION['user_id'], $_POST['id'], 0, 'request');

} elseif (isset($_GET['cancelReq'])) {

    if (!isset($_GET['id'])) {
        redirection('../../main.php');
    }
    $_POST['id'] = clearString($_POST['id']);
    queryFunc("DELETE FROM friend_requests WHERE to_id ={$_POST['id']} AND from_id ={$_SESSION['user_id']}");

    queryFunc("DELETE from notifications where s_user_id={$_SESSION['user_id']} AND d_user_id={$_POST['id']} AND typeC='request'");

} elseif (isset($_GET['deleteConvo'])) {
    $_POST['id'] = clearString($_POST['id']);
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
    $_GET['flag'] = clearString($_GET['flag']);
    $_GET['page'] = clearString($_GET['page']);
    showPosts($_GET['flag'], $_GET['page'], $limit);

} elseif (isset($_GET['loadRA'])) {

    $limit = 10;
    $_GET['page'] = clearString($_GET['page']);
    if (isset($_GET['id'])) {
        $_GET['id'] = clearString($_GET['id']);
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
    $activity_type = clearString($_POST['activity_type']);

    if ($activity_type == 4) {
        // if unlike activity

        // Target Content ID or IDs like post etc
        $target_id = clearString($_POST['target_id']);
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
            $target_id = clearString($_POST['target_id']);
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
    $_POST['friendId'] = clearString($_POST['friendId']);
    $_POST['conflict'] = clearString($_POST['conflict']);
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
        $_GET['email'] = clearString($_GET['email']);  
        $email = queryFunc("SELECT question from users where email = '{$_GET['email']}'");
        if(isData($email)){
            $email = isRecord($email);
            echo $email['question'];
        }else{
            echo "No";
        }
    }
else if(isset($_GET['saveNewPassword'])){    
    if(isset($_POST['password'])){
        $_POST['password'] = clearString($_POST['password']);  
        $password = hashString($_POST['password']);
        $email = clearString($_POST['email']);
        queryFunc("update users set password = '$password' where email = '$email'");
        echo "ok";
    }
}
else if(isset($_GET['validateAnswer'])){
    $_GET['email'] = clearString($_GET['email']);    
    $answer = queryFunc("SELECT answer from users where email = '{$_GET['email']}'");
    if(isData($answer)){
        $answer = isRecord($answer);
        $_GET['answer'] = hashString(clearString($_GET['answer']));  
        if($answer['answer'] == $_GET['answer'])
            echo "Yes";
        else{
            echo "No";
            updateWrongAttempts($_GET['email']); 
        }
   
    }else{
        echo "No";
        updateWrongAttempts($_GET['email']);
    }
}
else if(isset($_GET['refreshRecentUploads'])){    
    getUploadedPics($_SESSION['user_id']);
}
else if(isset($_GET['checkAttempts'])){    
    checkAttempts(clearString($_GET['email']));
}
else if(isset($_GET['deleteAccount'])){    
    deleteUser(clearString($_GET['deleteAccount']));
}      
else if(isset($_GET['canPost'])){    
    checkUserPosts(clearString($_GET['canPost']));
}      
else if(isset($_GET['canComment'])){    
    checkUserComments();
}       
else if(isset($_GET['canMessage'])){    
    checkUserMessages();
}
else if(isset($_GET['canAdd'])){    
    echo checkUserRequests();
}            
else if(isset($_GET['searchDetailsByAdmin'])){    
    showUserActivitiesSummaryForAdmin(clearString($_GET['id']));
}            
?>