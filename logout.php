<?php

require_once dirname(__FILE__) . '/includes/functions.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    redirection('index.php');
}

// Calling logout function
logout();
