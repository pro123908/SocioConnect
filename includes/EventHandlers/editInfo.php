<?php

require_once dirname(__FILE__,2) . '/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirection('../../index.php');
}

if (!isset($_POST['password'])) {
    redirection('../../main.php');
}

//Code for mysqli escape string and trimming whitespaces from beginning and end
$pass = mysqli_real_escape_string($connection, trim($_POST['password']));
$newPass = mysqli_real_escape_string($connection, trim($_POST['newPassword']));
$school = mysqli_real_escape_string($connection, trim($_POST['school']));
$college = mysqli_real_escape_string($connection, trim($_POST['college']));
$university = mysqli_real_escape_string($connection, trim($_POST['university']));
$work = mysqli_real_escape_string($connection, trim($_POST['work']));
$contact = mysqli_real_escape_string($connection, trim($_POST['contact']));
$age = mysqli_real_escape_string($connection, trim($_POST['age']));
$gender = mysqli_real_escape_string($connection, trim($_POST['genderBox']));
$question = mysqli_real_escape_string($connection, trim($_POST['question']));
$answer = mysqli_real_escape_string($connection, trim($_POST['answer']));
echo $answer;
$answer = strtolower($answer);
$pass = hashString($pass);
if (strlen(trim($newPass)) > 7) {
    $newPass = hashString($newPass);
}
// Checking the validity of current password for saving changes
$flag = validatePassword($pass);

//If current password was correct
if ($flag) {
    saveEditedInfo($school, $college, $university, $work, $contact, $newPass, $age, $gender, $question, $answer);
    echo timeString(differenceInTime($age));

} else {
    // If current password was incorrect
    $_SESSION['edit_info_pass_error'] = true;
    $_SESSION['edit_info_user_age'] = $age;
    $_SESSION['edit_info_user_gender'] = $gender;
    $_SESSION['edit_info_user_school'] = $school;
    $_SESSION['edit_info_user_college'] = $college;
    $_SESSION['edit_info_user_university'] = $university;
    $_SESSION['edit_info_user_work'] = $work;
    $_SESSION['edit_info_user_contact'] = $contact;
    $_SESSION['edit_info_user_question'] = $question;
    $_SESSION['edit_info_user_answer'] = $answer;
}
