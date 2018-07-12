

<?php

// Checked

/* TO BE DELETED */

require_once dirname(__FILE__) . '/includes/header.php';

// Sign up form for registration of new user

if (isset($_POST['submit'])) {
    // $fname = mysqli_real_escape_string($connection, $_POST['fname']); // First name
    // $lname = mysqli_real_escape_string($connection, $_POST['lname']); // Last name
    // $email = mysqli_real_escape_string($connection, $_POST['email']); // Email
    // $password = hashString(mysqli_real_escape_string($connection, $_POST['password'])); // Password
    // $age = mysqli_real_escape_string($connection, $_POST['age']); //Age
    // $gender = mysqli_real_escape_string($connection, $_POST['genderBox']); // Gender
    // $question = mysqli_real_escape_string($connection, $_POST['question']);
    // $answer = mysqli_real_escape_string($connection, $_POST['answer']);

    $fname = clearString($_POST['fname']);
    $lname = clearString($_POST['lname']);
    $email = clearString($_POST['email']);
    $password = hashString(clearString($_POST['password']));
    $age = clearString($_POST['age']);
    $gender = clearString($_POST['genderBox']);
    $question = clearString($_POST['question']);
    $answer = clearString($_POST['answer']);

    // Placing all fields value in session variables
    $_SESSION['s_first_name'] = $fname;
    $_SESSION['s_last_name'] = $lname;
    $_SESSION['s_email'] = $email;
    $_SESSION['s_age'] = $age;
    $_SESSION['s_question'] = $question;
    $_SESSION['s_answer'] = $answer;
    // Validating the value of input fields and checking if user exists already?
    if (!(formValidation($email, clearString($_POST['password']), clearString($_POST['repeatPassword'])))) {
        redirection('signUp.php');
    } else {
        // If fields are validated then adding the user to database
        if ($gender == "female") {
            $profile_pic = "assets/profile_pictures/female.jpg";
        } else {
            $profile_pic = "assets/profile_pictures/male.jpg";
        }
        $answer = strtolower($answer);
        $queryResult = queryFunc("INSERT INTO users(first_name,last_name,email,password,age,gender,profile_pic,question,answer) VALUES('$fname','$lname','$email','$password','$age','$gender','$profile_pic','$question','$answer')");

        //Selecting ID of new inserted user
        $ID = mysqli_insert_id($connection);

        if ($queryResult && $ID) {
            $_SESSION['user'] = $fname . ' ' . $lname; // Name of new user inserted
            $_SESSION['user_id'] = $ID;
            redirection('main.php');
        }
    }
} elseif (isset($_SESSION['user_id'])) {
    // If user is logged in already
    redirection('main.php');
}
?>

<div class='header-links header-links-login'>
        <a href="index.php" class="header-btn mr-1">Login</a>
      </div>

      <!-- div for completing header -->
    </div>

   <!-- Feature => Placing all values of inputs as it is if any of the input is not validated -->
    <div class="login-container">
      <h1 class="login-heading"></h1>
      <form action="signUp.php" method='POST' class="login-form">
      <input type="text" class='login-input' name='fname' placeholder='First Name' maxlength="20" minlength='3' required value=<?php if (isset($_SESSION['s_first_name'])) {
    echo $_SESSION['s_first_name'];
}?> ><br>
  <input type="text"  name='lname' class='login-input' placeholder='Last Name' maxlength="20" minlength='3' required value=<?php if (isset($_SESSION['s_last_name'])) {
    echo $_SESSION['s_last_name'];
}?>><br>
  <input type="email"  name='email' class='login-input' placeholder='Email' required value=<?php if (isset($_SESSION['s_email'])) {
    echo $_SESSION['s_email'];
}?>><br><?php if (isset($_SESSION['s_email_error'])) {
    echo $_SESSION['s_email_error'];
}?><br>
  <input type="password"  name='password' class='login-input' placeholder='Password' maxlength="20" minlength='8' required ><br><br>
  <input type="password" name='repeatPassword' class='login-input' placeholder='Confirm Password' maxlength="20" minlength='8' required><br><?php if (isset($_SESSION['s_pass_error'])) {
    echo $_SESSION['s_pass_error'];
}?><br>
  <input type="text" name='age' placeholder='Birthday' onfocus="(this.type='date')"   class='login-input' required value=<?php if (isset($_SESSION['s_age'])) {
    echo $_SESSION['s_age'];
}?>><br>
  <select name="genderBox"  required class='login-input'>
    <option value="Male" class='option'>Male</option>
    <option value="Female" class='option'>Female</option>
    <option value="Other" class='option'>Other</option>
  </select><br>
  <input type="text" class='login-input' name='question' placeholder='Securtiy Question' maxlength="255" minlength='3' required value="<?php if (isset($_SESSION['s_question'])) {
    echo $_SESSION['s_question'];
}?>" ><br>
  <input type="text"  name='answer' class='login-input' placeholder='Answer' maxlength="255" minlength='3' required value="<?php if (isset($_SESSION['s_answer'])) {
    echo $_SESSION['s_answer'];
}?>"><br>

  <input type="submit" name='signSubmit' class='login-submit' value='Register'>
      </form>
    </div>

