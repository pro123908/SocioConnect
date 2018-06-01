
<?php
    require_once('header.php');
    if (isset($_SESSION['user_id'])) {
        if(isset($_GET['query'])){
            getSearchedUsers($_GET['query'],2);
        }
   
    }
?>


<script src="script.js" ></script>