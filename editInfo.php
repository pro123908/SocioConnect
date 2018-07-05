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
$newPass = trim(mysqli_real_escape_string($connection, $_POST['newPassword']));
$school = trim(mysqli_real_escape_string($connection, $_POST['school'])); 
$college = trim(mysqli_real_escape_string($connection, $_POST['college'])); 
$university = trim(mysqli_real_escape_string($connection, $_POST['university'])); 
$work = trim(mysqli_real_escape_string($connection, $_POST['work'])); 
$contact = trim(mysqli_real_escape_string($connection, $_POST['contact']));
$age = trim(mysqli_real_escape_string($connection, $_POST['age'])); 
$gender = trim(mysqli_real_escape_string($connection, $_POST['genderBox'])); 

$pass = hashString($pass);
if(strlen(trim($newPass)) > 7)
    $newPass = hashString($newPass);
$flag = validatePassword($pass);

if($flag)
    saveEditedInfo($school,$college,$university,$work,$contact,$newPass,$age,$gender);
else{
    $_SESSION['edit_info_pass_error'] = true;
    $_SESSION['edit_info_user_age'] = $age;
    $_SESSION['edit_info_user_gender'] =$gender;
    $_SESSION['edit_info_user_school'] = $school;
    $_SESSION['edit_info_user_college'] =$college ;
    $_SESSION['edit_info_user_university'] = $university;
    $_SESSION['edit_info_user_work'] = $work;
    $_SESSION['edit_info_user_contact'] = $contact;
}
redirection("about.php?id=".$_SESSION['user_id']);
?>