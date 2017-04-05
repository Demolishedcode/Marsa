<?php
 // Start session
 session_start();

 if (isset($_SESSION['user_name'])) {
   header('location: profile');
   exit();
 }

 $messSucces = null;

 if (isset($_GET['signup'])) {
   $messSucces = "Signup Succesfull! Please now login";
 }
 //&& is_numeric($_GET['c']) && strlen($_GET['c'] == 1)
 if (isset($_GET['c']) && is_numeric($_GET['c']) && strlen((string)$_GET['c']) == 1) {
   $_SESSION['createGroup'] = $_GET['c'];
 }

 // Connect to database
require_once "includes/dbconfig.php";

 //Pre-set variables
 $username = $password = $messError = $userError = $passError = null;

 if (isset($_POST['login'])) {
     // Get all input
     $username = stripVariables($_POST['username']);
     $password = stripVariables($_POST['password']);
     $block = false;

     // Check if inputs are not empty
     if (empty($username)) {
         $userError = "Fill in your email<br>";
         $block = true;
     }

     if (empty($password)) {
         $passError = "Check your password<br>";
         $block = true;
     }

     if ($block) {
         // If an error occurred create error message
         $messError = $userError . $passError;
         //echo $messError;
     } else {
         // Check if password is correct
         $result = mysqli_query($conn, "SELECT * FROM user WHERE email='$username' AND password='" . hash('sha256', $password) . "'");
         $resultRows = mysqli_num_rows($result);

         if (!$resultRows) {
             // If mail and password do not match
             $messError = "Account is invalid or wrong combination of email and password. Please check if everything you filled in is correct";
             //echo $messError;
             $password = $username = null;
         } else {
           // Set session variables
             $queryName = mysqli_query($conn, "SELECT fname, sname FROM user WHERE email='$username'");

             foreach ($result as $row) {
                 $name = $row['fname'] . ' ' . $row['sname'];
             }

             $queryId = mysqli_query($conn, "SELECT user_id FROM user WHERE email='$username'");

             foreach ($queryId as $row) {
               $user_id = $row['user_id'];
             }

             $_SESSION['user_name'] = $name;
             $_SESSION['user_email'] = $username;
             $_SESSION['user_id'] = $user_id;

             // If user is first logged in
             $queryFirstLogin = "SELECT meta_value FROM user_meta WHERE user_id='$user_id' AND meta_key='firstLogin';";

             // Run FirstLogin query
             $firstLogin = mysqli_query($conn, $queryFirstLogin);
             // Fetch value of firstLogin
             foreach ($firstLogin as $row) {
                 $valLogin = $row['meta_value'];
             }

                 // Set variable in session for if necessary welcome message
             $_SESSION['firstLogin'] = $valLogin;


             // Check if user is in a group, deside redirect
             $queryGroup = "SELECT group_id FROM user_group WHERE user_id='$user_id'";

             // Run Groupcheck query
             $groupCheck = mysqli_query($conn, $queryGroup);
             // Fetch avlue of groupCheck
             foreach ($groupCheck as $row) {
                 $group = $row['group_id'];
             }

             // Go to page depending user is in group or not
             if ($group == null) {
               header('Location: groups');
             } else {
               header('Location: profile');
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
 }?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login Marsa</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/style-login.css" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <script src="script/jquery-3.2.0.min.js"></script>
        <script src="script/jquery-ui.min.js"></script>
        <script src="script/popup/popup-script.js"></script>
        <link href="css/afbeeldingen/App.png" rel="icon">
    </head>
    <body>
        <section>
            <div class="alert"><?php echo $messError;?></div>
            <div class="succes"><?php echo $messSucces;?></div>
            <div class="login inline">
                <div class="logo">
                    <a href="https://www.mymarsa.com">
                        <img src="css/afbeeldingen/Logo%20small.png" alt="Marsa Logo">
                    </a>
                </div>
                <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>">
                    <input type="email" name="username" placeholder="Your Email" value="<?php echo $username;?>" autofocus>
                    <input type="password" name="password" placeholder="Your Password" value="<?php echo $password;?>"><br>
                    <input type="submit" name="login" value="LOGIN">
                </form>
                <a href="reset">
                    <div class="reset">
                        <p>
                            Forgot your password?
                        </p>
                    </div>
                </a>
                <a href="register">
                    <div class="register">
                        <p>
                            New to Marsa?
                        </p>
                    </div>
                </a>
            </div>
        </section>
    </body>
</html>
