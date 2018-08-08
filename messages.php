<?php

require_once dirname(__FILE__) . '/includes/functions.php';
require_once dirname(__FILE__) . '/includes/header.php';

if (!isset($_SESSION['user_id'])) {
    redirection('index.php');
}

if (isset($_GET['id'])) {
    //If Someone tries to message himself
    if ($_GET['id'] == $_SESSION['user_id']) {
        redirection("messages.php");
    }
    $partnerID = clearString($_GET['id']);
    $_SESSION['partner'] = $partnerID;
    $partner = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name from users where user_id ={$partnerID}");
    $partner = isRecord($partner);
      //Update opened to seen
      $userLoggedIn = $_SESSION['user_id'];
      $seen = queryFunc("UPDATE messages set opened = '1' where user_to = '$userLoggedIn' AND user_from = '$partnerID'");
} else {
    // If user comes to messages page just by clicking on messages
    getRecentConvo();
}
?>
<div class='message-area'>
    <div class="recent-chat-area">
        <div class="search-user-for-chats">
            <?php searchUsersFortChats();?>
        </div>

        <div class="recent-chats">
            <?php showRecentChats();?>
        </div>
    </div>

    <div class="chat-box">
        <div class='chat-user'>
            <?php if (isset($_GET['id'])) {
    $pic = getUserProfilePic(clearString($_GET['id']));
    $time = activeAgo(clearString($_GET['id']));
    $state = 'state-off';

    if ($time == 'Just Now') {
        $time = 'Now';
        $state = 'state-on';
    }
    $content = <<<CONTENT
                    <span class='chat-user-image'><img src='{$pic}' class='post-avatar post-avatar-40'/></span>
                    <span><a class='chat-username' href="timeline.php?visitingUserID={$_GET['id']}">{$partner['name']}</a></span>
                    <div class='time-display {$state}'>{$time}</div>
CONTENT;
    echo $content;
}
?>

        </div>

        <div class="convo-area">
            <?php if (isset($_GET['id'])) {
    $userID = $_SESSION['user_id'];
    $profilePicMe = getUserProfilePic($userID);
    ?>

            <div class='loading-message-container'>
                <div id='loading-messages' class='loading-messages'></div>
            </div>

            <?php
echo "<div class='chat-messages'>";
    showMessages($partnerID, 1, 10);
    echo '</div>';
    ?>

            <script>
                var last = document.getElementById("noMoreMessages").previousSibling;
                if (last != null) {
                    last.scrollIntoView();
                }
            </script>

            <?php
} else {
    $noMessage = <<<MESSAGE
                        <div class='No-message'>
                            <div class='no-message-icon'>
                                <i class='fas fa-arrow-left'></i>
                            </div>
                            <div class='no-message-text'>
                                <h2>No Messages</h2>
                                <span>Search a friend to begin with</span>
                            </div>

                        </div>
MESSAGE;
    echo $noMessage;
}
?>
        </div>
        <!-- Message Input Form in convo Area -->
        <?php
if (isset($_GET['id'])) {
    $messageInput = <<<DELIMETER
                        <div class='message-input-form'>
                        <form method="post" name='messageForm' action="javascript:message()">
                        <input autocomplete='off' name="message_body" placeholder="Type your message here"  class='message-input'/>
                        <input type='hidden' name='partner' value='$partnerID'>
                        <input type='hidden' name='pic' value='$profilePicMe'>
                        <input type="submit" name="send_message" id="message_submit" value="send" style='display:none;' >
                        </form>
                        </div>
DELIMETER;
    echo $messageInput;
}
?>
    </div>
</div>

<script src="./includes/script.js" ></script>