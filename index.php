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

if (isset($_POST['submit'])) { // If form is submitted
    // Email of the user
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $_SESSION['user_email'] = $email;
    //Hash of password is returned
    $password = hashString(mysqli_real_escape_string($connection, $_POST['password']));

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
}
//If user has already logged In and coming from another page to here
elseif (isset($_SESSION['user_id'])) {
    redirection('main.php');
}

?>
      <div class='header-links header-links-login'>
        <a href="signUp.php" class="header-btn mr-1">Sign Up</a>
      </div>

      <!-- div for completing header -->
    </div>

    <div class="login-container">
      <h1 class="login-heading">Welcome</h1>
      <form action="index.php" method='POST' class="login-form">
        <input type="text"  name='email' placeholder="Email" class="login-input" value= '<?php $value = isset($flag) ? $_SESSION['user_email'] : "";
echo $value;?>' required><br>
        <input type="password" name='password' placeholder="Password" class="login-input"  required><br>
        <input type="submit" name='submit' class="login-submit">
      </form>

      <?php
if (isset($_SESSION['login_message'])) {
    if ($_SESSION['login_message'] != '') {
        $data = $_SESSION['login_message'];
        $_SESSION['login_message'] = '';
    } else {
        $data = '';
    }
    echo "<h3>{$data}</h3>";
}

?>



    </div>
