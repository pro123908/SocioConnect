<?php

require_once('functions.php'); 
require_once('db.php'); 

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

if(!isset($_POST['password']))
    redirection('main.php');

//Code for mysqli escape string and trimming whitespaces from beginning and end
$pass = trim(mysqli_real_escape_string($connection, $_POST['password']));
$school = trim(mysqli_real_escape_string($connection, $_POST['school'])); 
$college = trim(mysqli_real_escape_string($connection, $_POST['college'])); 
$university = trim(mysqli_real_escape_string($connection, $_POST['university'])); 
$work = trim(mysqli_real_escape_string($connection, $_POST['work'])); 
$contact = trim(mysqli_real_escape_string($connection, $_POST['contact'])); 

$pass = hashString($pass);
$flag = validatePassword($pass);

if($flag)
    saveEditedInfo($pass,$school,$college,$university,$work,$contact);
else{
    $_SESSION['edit_info_pass_error'] = true;
    $_SESSION['edit_info_user_school'] = $school;
    $_SESSION['edit_info_user_college'] =$college ;
    $_SESSION['edit_info_user_university'] = $university;
    $_SESSION['edit_info_user_work'] = $work;
    $_SESSION['edit_info_user_contact'] = $contact;
}
redirection("about.php?id=".$_SESSION['user_id']);
?>