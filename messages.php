<?php

require_once('header.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}
if (isset($_GET['id'])) {
    //If Someone tries to message himself
    if ($_GET['id'] == $_SESSION['user_id']) {
        redirection("messages.php");
    }
    $partnerID = $_GET['id'];
    $_SESSION['partner'] = $partnerID;
    $partner = queryFunc("select first_name from users where user_id =".$partnerID);
    $partner = isRecord($partner);
}
else{
    getRecentConvo();
}
?>
<div class='message-area'>
<div class="recent-chat-area">
    
    <div class="search-user-for-chats">
        <?php searchUsersFortChats(); ?> 
    </div>
    <div class="recent-chats">  
        <?php showRecentChats(); ?> 
    </div>
    
</div>    
<div class="chat-box">
<div class='chat-user'>
                <span class='chat-username'><?php echo $partner['first_name'] ?></span>
</div>
    <div class="convo-area">
        <?php 
        if (isset($_GET['id'])){
            $userID = $_SESSION['user_id'];
            $profilePicMe = getUserProfilePic($userID);


            $message =<<<MESSAGE
            
            <div class='chat-messages'>
MESSAGE;
            echo $message;
            showMessages($partnerID);
            
             $message = '</div>';
            echo $message;
            ?>
            <script> 
                var last = document.querySelector(".chat-message:last-child");
                last.scrollIntoView();
            </script> 
            <?php
        }
        else{
            echo "  <h2>Start a Conversation</h2><h3>&lt;==== Select Friend to start convo</h3> ";
        } 
        ?>
        
    </div>
    <div class='message-input-form'>
        <?php
        if (isset($_GET['id'])){
            $messageInput = <<<DELIMETER
            <form method="post" name='messageForm' action="javascript:message()">
            <input autocomplete='off' name="message_body" placeholder="Type your message here"  class='message-input'></input>
            <input type='hidden' name='partner' value='$partnerID'>
            <input type='hidden' name='pic' value='$profilePicMe'>
            <input type="submit" name="send_message" id="message_submit" value="send" style='display:none'>
            </form>
DELIMETER;
            echo $messageInput;
        }
        ?>
    </div>    
    
</div>
</div>
<!-- <div style='height:200px;'></div> -->
<script src="script.js" ></script>