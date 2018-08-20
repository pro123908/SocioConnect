
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
<div class='friends-page row no-gutters'>
<div class='friends-content col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9'>
    <?php
// Getting all your requests from database which you have received
$userID = $_SESSION['user_id'];
$friend_req = '';
$friend_req = "<div class='friend-request-area'>";

$friend_req .= friendRequest();

$friend_req .= "</div>";
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
    <div class='people-you-may-know-area col-lg-3 col-xl-3'>
        <div class='people-you-may-know-heading'> People you may know</div>
        <div class='people-you-may-know-content'>
            <?php showPeopleYouMayKnow()?>
        </div>
    </div>
</div>
</div>

<script src="./includes/script.js"></script>