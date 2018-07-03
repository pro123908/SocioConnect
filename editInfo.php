<?php

require_once('functions.php'); 
require_once('db.php'); 

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

if(!isset($_POST['password']))
    redirection('main.php');

//Code for mysqli escape string
$pass = mysqli_real_escape_string($connection, $_POST['password']); // First name
$school = mysqli_real_escape_string($connection, $_POST['school']); // Last name
$college = mysqli_real_escape_string($connection, $_POST['college']); // First name
$university = mysqli_real_escape_string($connection, $_POST['university']); // Last name
$work = mysqli_real_escape_string($connection, $_POST['work']); // Last name
$contact = mysqli_real_escape_string($connection, $_POST['contact']); // First name


$pass = hashString($pass);
$flag = validatePassword($pass);

if($flag)
    saveEditedInfo($pass,$school,$college,$university,$work,$contact);
else
    $_SESSION['edit_info_pass_error'] = true;
redirection("about.php?id=".$_SESSION['user_id']);
?>