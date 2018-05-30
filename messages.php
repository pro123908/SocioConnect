<?php
     require_once('header.php');
     if (isset($_SESSION['user_id'])) {
        ?>
        <div class="recent_chats">
            <?php showRecentChats();?>
        </div>
        <?php
    }   
    else{
        redirection("index.php");
    }  
?>