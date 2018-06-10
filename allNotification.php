

<?php 

require_once('header.php');
if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}


    echo '<div class="notification-display"><h1>Notifications</h1>';
    showNotifications('all',1,10);
    echo '</div>';
?>
<script src="script.js" >

</script>