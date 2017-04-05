<?php
// Connection to the database
include_once "includes/dbconfig.php";


// Pre-set variables, to be able to show in html
$errorMessage = $fname = $sname = $email = $password = $cpassword = null;

// When
if (isset($_POST['sub'])) {
    // Pre-set variables for a global scope
    $emailError = $passwordError = $fnameError = $snameError = $block = null;

    // Get all form values
    $fname = stripVariables($_POST['firstname']);
    $sname = stripVariables($_POST['secondname']);
    $email = stripVariables($_POST['email']);
    $password = stripVariables($_POST['password']);
    $cpassword = stripVariables($_POST['cpassword']);

    // ** Check if all information is valid **
    $fullName = $fname . ' ' . $sname;

    if (!preg_match("/^[a-z ,.'-]+$/i", $fullName)) {
      $snameError = 'Type in a valid name<br>';
      $block = true;
      $sname = null;
      $fname = null;
    }

    // Email
    if (empty($email)) {
        // If email is empty
        $emailError = 'Enter an email<br>';
        $block = true;

    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // If email is valid
        $emailError = 'Review your email adress<br>';
        $block = true;
        $email = null;

    } else {
        // If email is available
        $emailQuery = "SELECT email FROM user WHERE email='$email'";
        $emailResult = mysqli_query($conn,$emailQuery);
        $emailRows = mysqli_num_rows($emailResult);

        if ($emailRows != 0) {
            // Email taken create error message
            $emailError = "This email is already being used<br>";
            $block = true;
            $email = null;

        }
    }

    // Password
    if (empty($password)) {
        // If password is empty
        $passwordError = 'Fill in a password<br>';
        $block = true;
        $password = $cpassword = null; // Reset password variables

    } else if (strlen($password) < 8) {
        // If password is longer than 8 characters
        $passwordError = 'Password is to short<br>';
        $block = true;
        $password = $cpassword = null; // Reset password variables

    } else if ($password != $cpassword) {
        $passwordError = 'Passwords do not match<br>';
        $block = true;
        $cpassword = null;  // Reset password variables

    } else {
        $encPassword = hash('sha256', $password); // Encrypt password
    }


    if ($block) {
        // Create error message
        $errorMessage =  $fnameError . $snameError . $emailError . $passwordError;

    } else {
        // Register user to database
        $registerQuery = "INSERT INTO user (fname, sname, email, password)
        VALUES ('$fname', '$sname','$email','$encPassword')";

        // Run registerQuery, check if no errors
        if ($registerResult = mysqli_query($conn,$registerQuery)) {
            // Get last ID, implement in other tables
            $last_id = mysqli_insert_id($conn);

            // Register user meta about and make user id in user goup
            $query = "INSERT INTO user_meta (user_id, meta_key ) VALUES ('$last_id','about');";
            $query .= "INSERT INTO user_meta (user_id, meta_key, meta_value) VALUES ('$last_id','firstLogin','true');";
            $query .= "INSERT INTO user_group (user_id) VALUES ('$last_id');";

            // Run queries above
            $registerMeta = mysqli_multi_query($conn, $query);

            // Check if error occurred
            if (!$registerMeta) {
                echo "Woops! An error has occurred!";
            } else {
                // Go to login in page
                header('Location: login?signup=true');
            }

        }
    }
}

// Prevent Injections
function stripVariables($input){
    $var = trim($input);
    $var = strip_tags($input);
    $var = stripslashes($input);
    $var = htmlspecialchars($input);

    return $var;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Sign Up || Marsa</title>
        <link rel="stylesheet" href="css/style-signup.css" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <link href="css/afbeeldingen/App.png" rel="icon">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="script/jquery-3.2.0.min.js"></script>
        <script src="script/jquery-ui.min.js"></script>
        <script src="script/popup/popup-script.js"></script>
    </head>
    <body>
        <section>
            <div class="alert"><?php echo $errorMessage;?></div>
            <div class="logo">
                <a href="https://www.mymarsa.com">
                    <img src="css/afbeeldingen/Logo%20small.png" alt="Marsa Logo">
                </a>
            </div>
            <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>">
                <input type="text" name="firstname" placeholder="Your Name" value="<?php echo $fname;?>">
                <input type="text" name="secondname" placeholder="Your Second Name" value="<?php echo $sname;?>">
                <input type="text" name="email" placeholder="Your E-mail" value="<?php echo $email;?>">
                <input type="password" name="password" placeholder="Choose a Password" value="<?php echo $password;?>">
                <input type="password" name="cpassword" placeholder="Confirm your Password" value="<?php echo $cpassword;?>"><br>
                <input type="submit" name="sub" value="LOGIN">
            </form>
        </section>
    </body>
</html>
