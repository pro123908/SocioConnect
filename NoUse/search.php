<?php
    require_once('functions.php');


    /* DEAD */

    /*
        Returns search results    
    */
        
    if(!isset($_SESSION['user_id'])){
        redirection('index.php');
    }

    //Passing input value and flag to the search functiom
    getSearchedUsers($_POST['query'],$_POST['flag']);
    