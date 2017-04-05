<?php
require_once '../includes/dbconfig.php';
session_start(); // Start session

// Run when request is 'send'
if (isset($_POST['request'])) {
    // Fetch user email for id check
    $user_email = $_SESSION['user_email'];

    // Get post values
    $groupId = $_POST['groupId'];

    // Check if user hasn't send other requests
    $queryCheckRequest = "SELECT user_id FROM requests WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
    $checkUser = mysqli_query($conn, $queryCheckRequest);

    // Check if user is not already in a group
    $queryCheckGroup = "SELECT group_id FROM user_group WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
    $checkGroup = mysqli_query($conn, $queryCheckGroup);

    // Fetch group value
    foreach($checkGroup as $row) {$group = $row['group_id'];}

    if (mysqli_num_rows($checkUser) == 0) {
        // If not found, check if not in group yet
        if ($group == null) {
            // Not in group, send request
            $queryAdd = "INSERT INTO requests (group_id, user_id) VALUES ('$groupId',(SELECT user_id FROM user WHERE email='$user_email'))";

            // Check if everthing went good
            if (mysqli_query($conn, $queryAdd)) {
                // Went good, echo succes message
                echo "<li style='color:#4cd964; padding: 10px; border: none; width: 100%'>Request is send!</li>";
            } else {
                // Went wrong
                echo "<li style='color:#ff3b30; padding: 10px; border: none; width: 100%'>Oops! something went wrong, please try again!</li>";
            }
        } else {
            // already in group
            echo "<li style='color:#ff3b30; padding: 10px; border: none; width: 100%'>You're already in a group!</li>";
        }

    } else {
        // If found
        echo "<li style='color:#ff3b30; padding: 10px; border: none; width: 100%'>You already have send a request! Cancel your previous request and try again</li>";
    }
}

?>
