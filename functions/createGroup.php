<?php
session_start();
require_once '../includes/dbconfig.php';

/*Only make user able to create group if not in a group yet*/

// Get values of post
$groupName = isset($_POST['groupName']) ? $_POST['groupName'] : null;
$groupDescription = isset($_POST['groupDescription']) ? $_POST['groupDescription'] : null;
$groupCategory = isset($_POST['groupCategory']) ? $_POST['groupCategory'] : null;

// Error handeling
$errorMessage = "";
$block = false;

$user_email = $_SESSION['user_email'];

if (isset($_POST['run'])) {
    // Check if user is not already in a group
    $queryCheckGroup = "SELECT group_id FROM user_group WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
    $checkGroup = mysqli_query($conn, $queryCheckGroup);

    // Fetch group value
    foreach($checkGroup as $row) {$group = $row['group_id'];}

    if ($group == null) {
        // User is not in a group yet
        // Validate the variables
        if (empty($groupName)) {
            // Is group name filled in
            $errorMessage .= "Fill in a group name<br>";
            $block = true;

        } else if (strlen($groupName) < 10) {
            // Is group name long enough
            $errorMessage .= "Make your group name longer<br>";
            $block = true;
        } else {
            // Get num rows where name exsists
            $query = "SELECT name FROM groups WHERE name='$groupName'";
            $queryName = mysqli_query($conn, $query);
            $resultName = mysqli_num_rows($queryName);

            if ($resultName) {
                $errorMessage .= "This group name is already taken<br>";
                $block = true;
            }
        }

        if (empty($groupDescription)) {
            $errorMessage .= "Please enter a group description<br>";
            $block = true;
        } else if (strlen($groupDescription) < 20) {
            $errorMessage .= "Make your group description longer<br>";
            $block = true;
        }

        if ($groupCategory == null) {
            $errorMessage .= "Select a category";
            $block = true;
        }


        if ($block) {
            // Something is wrong return error message
            echo "<p style='color:red'>$errorMessage</p>";
        } else {
            $groupDescription = addslashes($groupDescription);
            // Everything is correct create new group
            $createQuery = "INSERT INTO groups (group_information_id, name, admin, description) VALUES ((SELECT group_id FROM group_information WHERE name='$groupCategory'),'$groupName',(SELECT user_id FROM user WHERE email='$user_email'),'$groupDescription')";

            // Rum the create query
            if ($createGroup = mysqli_query($conn, $createQuery)) {
                // Make creator join group
                $last_id = mysqli_insert_id($conn);
                $joinQuery = "UPDATE user_group SET group_id='$last_id' WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";

                // Run the join query
                if ($joinCreator = mysqli_query($conn, $joinQuery)) {
                    // return succes Message
                    echo "<p style='color:green;'>New group is succesfully created! Please refresh page</p>";
                    unset($_SESSION['createGroup']);

                    // Delete previous requests
                    $query = "DELETE FROM requests WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
                    $deleteRequests = mysqli_query($conn, $query);
                } else {
                    // return error Message
                    echo "<p style='color:red;'>Something went wrong, please try again!</p>";
                }

            } else {
                // return error Message
                echo "<p style='color:red;'>Something went wrong, please try again!</p>";
            }
        }

    } else {
        echo "<p style='color:red;'>You're already in a group!</p>";
    }
}


?>
