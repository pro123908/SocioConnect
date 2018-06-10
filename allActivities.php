<?php 

require_once('header.php');
if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}


    echo '<div class="notification-display"><h1>Recent Activities</h1><div class="activities">';
    showRecentActivities(1,10);
    echo '</div></div>';
    echo "<div id='loading-activities' class='loading-messages'></div>";
?>
<script src="script.js" >

</script>
