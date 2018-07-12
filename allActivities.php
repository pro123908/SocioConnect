<?php
    require_once dirname(__FILE__) . '/includes/header.php';

    if (!isset($_SESSION['user_id'])) {
        redirection('index.php');
    }
?>
    <div class="recent-activities-display">
        <h1>Recent Activities</h1>
        <div class="activities">
            <?php
                $id = null;
                if (isset($_GET['id'])) {
                    $id = clearString($_GET['id']);
                }
                showRecentActivities(1, 10, 2, $id);
            ?>
        </div>
    </div>
    <div id='loading-activities' class='loading-messages'></div>"

<script src="./includes/script.js" ></script>
