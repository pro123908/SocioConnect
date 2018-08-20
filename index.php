<?php

// Checked

require_once dirname(__FILE__) . '/includes/header.php';

// If user has already logged in, then redirect user to main.php
if (isset($_SESSION['user_id'])) {
    redirection('main.php');
}

?>

<!-- Login page, the first page where user comes -->

  <?php

// POST request made to this file
// Passed login information with the request

if (isset($_POST['loginSubmit'])) { // If form is submitted
    // Email of the user
    $email = clearString($_POST['email']);
    $_SESSION['user_email'] = $email;
    //Hash of password is returned
    $password = hashString(clearString($_POST['password']));

    // Checking if user exists or getting that user from database
    $queryResult = queryFunc("SELECT * FROM users WHERE email = '$email'");

    if (!isData($queryResult)) {
        // If user doesn't exist
        $_SESSION['login_message'] = "User doesn't exist";

    } else {
        $row = isRecord($queryResult);
        // Hash from database is compared with the hash created now.
        if ($row['password'] === $password) {
            turnOnline($row['user_id']);
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user'] = $row['first_name'] . ' ' . $row['last_name'];
            // If password matches, redirect to main.php
            redirection('main.php');
        } else {
            $flag = 1;
            $_SESSION['login_message'] = "Wrong Password";

        }
    }
} elseif (isset($_POST['signSubmit'])) {

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
    if (!(formValidation($email, $_POST['password'], $_POST['repeatPassword'],$age))) {
        redirection('index.php');
    } else {
        // If fields are validated then adding the user to database
        if ($gender == "Female") {
            $profile_pic = "female.jpg";
        } else {
            $profile_pic = "male.jpg";
        }

        $cover_pic = 'cover.png';
        $answer = hashString(strtolower($answer));
        $queryResult = queryFunc("INSERT INTO users(first_name,last_name,email,password,age,gender,profile_pic,cover_pic,question,answer) VALUES('$fname','$lname','$email','$password','$age','$gender','$profile_pic','$cover_pic','$question','$answer')");

        //Selecting ID of new inserted user
        $ID = mysqli_insert_id($connection);

        if ($queryResult && $ID) {
            sendReqFromDefaultAccount($ID);
            $_SESSION['user'] = $fname . ' ' . $lname; // Name of new user inserted
            $_SESSION['user_id'] = $ID;
            turnOnline($_SESSION['user_id']);
            redirection('main.php');

        }
    }
}
//If user has already logged In and coming from another page to here
elseif (isset($_SESSION['user_id'])) {
    redirection('main.php');
}

?>

<div class="login-container">
    <div class='login-row row'>
     <div class= 'col-12 col-sm-12  col-md-12 col-lg-6 col-xl-6  login-logo'></div>
     <div class='col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6  login-info'>
      <!-- <h1 class="login-heading">Welcome</h1> -->
      <div class='row'>
      <div class='login-form-container col-10 offset-1 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-8 offset-lg-2 col-xl-9'>
        <div class='row'>

        
      <form action="index.php" method='POST' class="login-form">
        <input type="text"  name='email' placeholder="Email" class="col-xs-12 col-sm-12 col-md-12 col-lg-5 col-xl-5 login-input" value= '<?php $value = isset($flag) ? $_SESSION['user_email'] : "";
echo $value;?>' required>
        <input type="password" name='password' placeholder="Password" class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 login-input"  required>
        <input  type="hidden" name="email-for-forgot-pass" value= "<?php if (isset($_SESSION['user_email'])) {
    echo $_SESSION['user_email'];
}
?>">
        <input type="submit" name='loginSubmit' class="col-xs-12 col-sm-12 col-md-12  col-lg-2 col-xl-2 login-submit" value='Login'>
      </form>

    <?php
if (isset($_SESSION['login_message'])) {
    $display = "<div class='login-error-container col-lg-12 col-xl-12'>";
    $forgotPassword = '';
    if ($_SESSION['login_message'] != '') {
        $message = $_SESSION['login_message'];
        $_SESSION['login_message'] = '';
        if ($message == "Wrong Password") {
            $forgotPassword = "<a href= 'javascript:showForgotPassWindow()' class='forgot-password-text'>Forgotten Password?</a>";
        }

    } else {
        $message = '';
    }

    $display .= "<span class='login-error-msg'></span>";
    $display .= $forgotPassword . "</div>";
    echo $display;
}
?>

</div>
</div>


<div class='sign-form-container col-10 offset-1 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-8 offset-lg-2 col-xl-9'>
    <div class='row'>
    <form action="index.php" method='POST' class="sign-form col-lg-12 col-xl-12">
      <input type="text" class='col-xs-12 col-sm-12 col-md-12   sign-input' name='fname' placeholder='First Name' maxlength="20" minlength='3' required value=<?php if (isset($_SESSION['s_first_name'])) {
    echo $_SESSION['s_first_name'];
}?> >
  <input type="text"  name='lname' class='col-xs-12 col-sm-12 col-md-12   sign-input' placeholder='Last Name' maxlength="20" minlength='3' required value=<?php if (isset($_SESSION['s_last_name'])) {
    echo $_SESSION['s_last_name'];
}?>><br>
  <input type="email"  name='email' class='col-xs-12 col-sm-12 col-md-12  sign-input' placeholder='Email' required value=<?php if (isset($_SESSION['s_email'])) {
    echo $_SESSION['s_email'];
}?>><?php if (isset($_SESSION['s_email_error'])) {
    echo "<span class='error-msg'>{$_SESSION['s_email_error']}</span>";
}?><br>
  <input type="password"  name='password' class='col-xs-12 col-sm-12 col-md-12 sign-input' placeholder='Password' maxlength="20" minlength='8' required ><br>
  <input type="password" name='repeatPassword' class='col-xs-12 col-sm-12 col-md-12 sign-input' placeholder='Confirm Password' maxlength="20" minlength='8' required><?php if (isset($_SESSION['s_pass_error'])) {
    echo "<span class='error-msg'>{$_SESSION['s_pass_error']}</span>";
}?><br>
  <input type="text" name='age' placeholder='Birthday' onfocus="(this.type='date')"   class='col-xs-12 col-sm-12 col-md-12 sign-input' required value=<?php if (isset($_SESSION['s_age'])) {
    echo $_SESSION['s_age'];
}?>><?php if (isset($_SESSION['s_age_error'])) {
    echo "<span class='error-msg'>{$_SESSION['s_age_error']}</span>";
}?><br>
  <select name="genderBox"  required class='col-xs-12 col-sm-12 col-md-12   sign-input'>
    <option value="Male" class='option'>Male</option>
    <option value="Female" class='option'>Female</option>
    <option value="Other" class='option'>Other</option>
  </select><br>
  <input type="text" class='col-xs-12 col-sm-12 col-md-12 sign-input' name='question' placeholder='Securtiy Question' maxlength="255" minlength='3' required value="<?php if (isset($_SESSION['s_question'])) {
    echo $_SESSION['s_question'];
}?>" ><br>
  <input type="text"  name='answer' class='col-xs-12 col-sm-12 col-md-12 sign-input' placeholder='Answer' maxlength="255" minlength='3' autocomplete="off" required value="<?php if (isset($_SESSION['s_answer'])) {
    echo $_SESSION['s_answer'];
}?>"><br>

  <input type="submit" name='signSubmit' class='col-xs-12 col-sm-12 col-md-12 sign-submit' value='Register'>
      </form>

</div>
</div>
</div>
</div>
</div>


      <?php

?>
      <div class= "forgot-password-div-container">
        <div class="forgot-password-div">
            <h1 class = "forgot-password-div-heading">Security Question</h1>
            <span class="forgot-password-div-close" onclick="hideForgotPassWindow()">&times;</span>

            <div class = "forgot-password-div-content">
                <div class='forgot-password-question'></div>
                <form action = "javascript:void(0)" method = "post" id = "forgotPassForm">
                    <label class = "forgot-password-answer"><input type = "text" name = "answer" class = "forgot-password-input" autocomplete="off" maxlength= "255" required autofocus></label><br>

                    <input type = "submit" value = "Change Password" name="submit" class = "password-edit-save" onclick = "submitForgotPassForm()">

                </form>
            </div>
            <div class = "forgot-password-message"></div>
        </div>
    </div>

    <script src="./includes/script.js" ></script>