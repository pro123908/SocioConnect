<?php
    require_once('functions.php');
    $limit = 10;
    if(isset($_GET['id']))
        showRecentActivities($_GET['page'],$limit,2,$_GET['id']);
    else
        showRecentActivities($_GET['page'],$limit,2);
 
?>