
<?php
require_once('./includes/header.php');
if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

// Displaying all results for search
if(isset($_GET['query'])){
    getSearchedUsers($_GET['query'],2);
}
?>


<script src="./includes/script.js" ></script>