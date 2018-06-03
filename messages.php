<link rel="stylesheet" href="styles/styles.css">
<?php

require_once('header.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}
if (isset($_GET['id'])) {
    //If Someone tries to message himself
    if($_GET['id'] == $_SESSION['user_id'])
        redirection("messages.php");
    $partnerID = $_GET['id'];
    $_SESSION['partner'] = $partnerID;
    $partner = queryFunc("select first_name from users where user_id =".$partnerID);
    $partner = isRecord($partner);
}
else{
    getRecentConvo();
}
?>
<div class="recent_chats_area">
    <h2>Recent Chats<h2>
    <div class="recent_chats">  
        <?php showRecentChats(); ?> 
    </div>
    <div class="search_user_for_chats">
        <?php searchUsersFortChats(); ?> 
    </div>
</div>    
<div class="chat_box">
    <div id="convo_area">
        <?php 
        if (isset($_GET['id'])){
            echo "<h2 id='partner_heading'>You and ". $partner['first_name'] ." </h2>";
            showMessages($partnerID);
        }
        else{
            echo "  <h2>Start a Conversation</h2><h3>&lt;==== Select Friend to start convo</h3> ";
        } 
        ?>
    </div>
    <div>
        <?php
        if (isset($_GET['id'])){
            $messageInput = <<<DELIMETER
            <form method="post" name='messageForm' action="javascript:message()">
            <textarea name="message_body" placeholder="Type your message here"  id="message_textarea"></textarea>
            <input type='hidden' name='partner' value='$partnerID'>
            <input type="submit" name="send_message" id="message_submit" value="send">
            </form>
DELIMETER;
            echo $messageInput;
        }
        ?>
    </div>    
</div>
<script src="script.js" ></script>