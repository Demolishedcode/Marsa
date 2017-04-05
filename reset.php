<?php
 // Connection to database
 require_once "functions/sendEmail.php";
 require_once "includes/dbconfig.php";

 // Pre-set variables for global scrope
  $messError = $messSucces = null;

 // Set new password
 if (isset($_POST['sendnew'])) {
     // Get new password
     $passwordReset = newPassword();

     // User email
     $emailReset = $_POST['emailr'];

     // Get rows where email is in, resetRows == 1 when email in database
     $resultReset = mysqli_query($conn, "SELECT * FROM user WHERE email='$emailReset'");
     $resetRows = mysqli_num_rows($resultReset);

     // Check email exsists
     if (!$resetRows) {
         // Email doesn't exsist, show error message
         $messError = "Please check your email again, something is wrong";

     } else {
         // Send user email with password
         $mail = sendEmail($emailReset,'resetPassword', $passwordReset);

         if ($mail) {
             // Encrypt new password
             $encPasswordReset = hash('sha256',$passwordReset);

             // Add new password to database
             $queryReset = "UPDATE user SET password='$encPasswordReset' WHERE email='$emailReset'";
             $resetPassword = mysqli_query($conn, $queryReset);

             // Show succes Message
             $messSucces = "A email is send to your inbox, it contains a new password. <a href='login'> Click here to login </a>";
         } else {
             $messError = "Something went wrong, please try again ";
         }
     }
 }

 // Generate new password
 function newPassword() {
     // Amount of characters in new password
     $length = 8;

     // Selecteble characters for new password
     $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#";

     // Select first 8 characters of shuffled characters
     $password = substr(str_shuffle( $chars ), 0, $length);
     return $password;
 }
 ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Reset Password - Marsa</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/style-login.css" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <link href="css/afbeeldingen/App.png" rel="shortcut icon">
        <script src="script/jquery-3.2.0.min.js"></script>
        <script src="script/jquery-ui.min.js"></script>
        <script src="script/popup/popup-script.js"></script>
    </head>
    <body>
        <section>
               <div class="succes"><?php echo $messSucces;?></div>
               <div class="alert"><?php echo $messError; ?></div>
                <div class="reset-box">
                    <div class="title">
                        <p>
                            Reset Password
                        </p>
                    </div>
                    <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>">
                        <label for="email">
                            Please enter your email
                        </label>
                        <input type="email" name="emailr" placeholder="email" autofocus>
                        <input type="submit" name="sendnew" value="SEND NEW PASSWORD">
                    </form>
                    <div class="message">
                        <p>
                            An email will be send to your inbox. This email will contain a new password. If you want to choose your own password, you can change it under the setting tab when you logged in with your new password
                        </p>
                    </div>
                </div>
        </section>
    </body>
</html>
