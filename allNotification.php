<?php
    require_once dirname(__FILE__) . '/includes/header.php';

    if (!isset($_SESSION['user_id'])) {
        redirection('index.php');
    }

    echo '<div class="notification-display"><h1>Notifications</h1><div class="notifications">';
    showNotifications(3, 1, 10);
    $content = '</div></div>';
    $content .= "<div id='loading-notis' class='loading-messages'>Show More Notifications</div>";
    echo $content;
?>
<script src="./includes/script.js" >

</script>