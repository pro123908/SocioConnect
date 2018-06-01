<?php
    require_once('functions.php');
    
    // Displaying the person which was cliked on when searching
    // Checking if something was searched or not?
    getSearchedUsers($_POST['query'],$_POST['flag']);
    