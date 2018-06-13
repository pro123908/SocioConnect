<?php 

require_once('header.php');
if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}
?>
    <div class="recent-activities-display">
        <h1>Recent Activities</h1>
        <div class="activities">
            <?php showRecentActivities(1,10,2); ?>
        </div>
    </div>
    <div id='loading-activities' class='loading-messages'></div>"


<script src="script.js" >

</script>
