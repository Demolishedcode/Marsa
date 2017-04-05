<?php
set_include_path(dirname(__FILE__)."../");
require_once "mailer/PHPMailerAutoload.php";
require_once 'includes/dbconfig.php';

$error = null; // Define error for global scrope
$mail = new PHPMailer;

// Define Marsa email server
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.strato.com';                      // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'support@mymarsa.com';             // SMTP username
$mail->Password = 'Marsa123';              // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to
$mail->isHTML(true);                                  // Set email format to HTML
$mail->setFrom('support@mymarsa.com', 'Marsa Support Team');

function sendEmail ($userMail, $mailType, $newPassword){
    global $conn, $mail;

    // Get the name of the user
    $query = "SELECT fname, sname FROM user WHERE email='$userMail'";
    $userName = mysqli_query($conn, $query);

    foreach ($userName as $row) {
        $userName = $row['fname'] . ' ' . $row['sname'];
    }

    $mail->addAddress($userMail, $userName);     // Add a recipient

    if ($mailType == 'resetPassword') {
        $mail->Subject = 'New Password - Marsa';
        $mail->Body    = "<p>Marsa Inc.</p><p>Dear $userName,</p><p>You requested a new password for your Marsa account</p><p><b>$newPassword</b></p><p><b>Attention:</b> Reset your password as soon as possible because this is a weak password!</p><p>Greetings,<br>Support Team, Marsa";
    }

    if(!$mail->send()) {
	   return false;
    } else {
	   return true;
    }

}

?>
