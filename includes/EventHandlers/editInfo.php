<?php

require_once dirname(__FILE__,2) . '/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirection('../../index.php');
}

if (!isset($_POST['password'])) {
    redirection('../../main.php');
}

//Code for mysqli escape string and trimming whitespaces from beginning and end
$pass = clearString($_POST['password']);
$newPass = clearString($_POST['newPassword']);
$school = clearString($_POST['school']);
$college = clearString($_POST['college']);
$university = clearString($_POST['university']);
$work = clearString($_POST['work']);
$contact = clearString($_POST['contact']);
$age = clearString($_POST['age']);
$gender = clearString($_POST['genderBox']);
$question = clearString($_POST['question']);
$answer = clearString($_POST['answer']);
$answer = strtolower($answer);
$pass = hashString($pass);
if (strlen(trim($newPass)) > 7) {
    $newPass = hashString($newPass);
}
// Checking the validity of current password for saving changes

    $thenDate = $age;
    $currentDate = date('d-m-Y');

    $diff = abs(strtotime($currentDate) - strtotime($thenDate));

    $years = floor($diff / (365*60*60*24));

    $flag = false;
    if($years < 13){
        $_SESSION['edit_age_error'] = 'Not Old Enough!';
        $flag = false;
    }
    else{
        $flag = validatePassword($pass);
    }
    

//If current password was correct
if ($flag) {
    saveEditedInfo($school, $college, $university, $work, $contact, $newPass, $age, $gender, $question, $answer);
    unset($_SESSION['edit_age_error']);
    echo $years;

} elseif($years < 13) {
    // If current password was incorrect
    echo $years;
}else{
    echo $years;
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
