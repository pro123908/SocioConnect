<?php
    require_once('header.php');
    if (isset($_SESSION['user_id'])) {
        if (isset($_GET['id'])) {
            $partnerID = $_GET['id'];
            $_SESSION['partner'] = $partnerID;
            $partner = queryFunc("select first_name from users where user_id =".$partnerID);
            $partner = isRecord($partner);
            $id = $partnerID;
        }
        ?>
        <div class="recent_chats_area">
            <div class="recent_chats">
                <h2>Recent Chats<h2>
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
                    echo "<h2>You and ". $partner['first_name'] ." </h2>";
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
        <?php
     }   
    else{
        redirection("index.php");
    }  
?>


<script src="script.js" ></script>