<?php

require_once('./includes/header.php');

if (!isset($_SESSION['user_id'])) {
    redirection('index.php');
}

if (!isset($_GET['id'])) {
    redirection('main.php');
}

$content = "<div class='user-information'>";
echo $content;
$content = showUserInfo($_GET['id']);
$content .= "</div>";
echo $content;
?>


<script src="./includes/script.js" ></script>
