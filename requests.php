
<?php

require_once dirname(__FILE__) . '/includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirection('index.php');
}
if (isset($_POST['add_friend'])) {
    addFriend(clearString($_POST['userId']));
} else if (isset($_POST['cancel_req'])) {
    cancelReq(clearString($_POST['userId']));
} else if (isset($_POST['respond_to_request'])) {
    redirection("requests.php");
} else if (isset($_POST['remove_friend'])) {
    removeFriend(clearString($_POST['userId']));
}

require_once dirname(__FILE__) . '/includes/header.php';

?>
<div class='friends-page'>
<div class='friends-content'>
    <?php
// Getting all your requests from database which you have received
$userID = $_SESSION['user_id'];

$reqArray = queryFunc("SELECT * FROM friend_requests WHERE to_id ={$userID} AND status=0");
$friend_req = "";

if (isData($reqArray)) {
    $friend_req = "<div class='friend-request-area'>";
    while ($row = isRecord($reqArray)) {
        // Getting the person who sent you the request
        $from_user = queryFunc("Select first_name, last_name,profile_pic from users where user_id = " . $row['from_id']);
        $from_user = isRecord($from_user);
        $from_user['profile_pic'] = "./assets/profile_pictures/" . $from_user['profile_pic'];
        $friend_req .= <<<DELIMETER
             <div class='friend-request'>
                <div class='friend-request-image'>
                    <img src={$from_user['profile_pic']} class='post-avatar post-avatar-40'/>
                </div>
                <div class='friend-request-info'>
                    <a href="timeline.php?visitingUserID={$row['from_id']}">{$from_user['first_name']}  {$from_user['last_name']}</a>
                </div>
                <div class='friend-request-action'>
                    <form action ="./includes/EventHandlers/acceptRequest.php" method="post">
                        <input type="submit" name="accept"  class='friend-request-btn' value="Accept"> <input type="submit" name="ignore" class='friend-request-btn' value="Ignore">
                        <input type = "hidden" name = "id" value="{$row['from_id']}">
                    </form>
                </div>
            </div>
DELIMETER;

    }
    $friend_req .= "</div>";
} else {

}
// Displaying friends
$friend_req .= '<div class="friends-list"><h1>Friends</h1><div class="friends-list-elements"><div class="friends-container">';
echo $friend_req;
$id = null;
if (isset($_GET['id'])) {
    $id = clearString($_GET['id']);
}

displayFriends(null, $id);
echo "</div></div></div></div>";

?>
    <div class='people-you-may-know-area'>
        <div class='people-you-may-know-heading'> People you may know</div>
        <div class='people-you-may-know-content'>
            <?php showPeopleYouMayKnow()?>
        </div>
    </div>
</div>
</div>

<script src="./includes/script.js"></script>