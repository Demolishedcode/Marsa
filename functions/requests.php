<?php
require_once '../includes/dbconfig.php';
session_start();
$adminId = $_SESSION['user_id'];

if (isset($_POST['handleRequest'])) {
  $user_id = $_POST['user_id'];
  // Remove request
  $removeQuery = "DELETE FROM requests WHERE user_id='$user_id'";
  $removeResult = mysqli_query($conn, $removeQuery);

  if (isset($_POST['accept'])) {
    // Add user to group
    $addQuery = "UPDATE user_group SET group_id=(SELECT group_id FROM groups WHERE admin='$adminId') WHERE user_id='$user_id'";

    if (mysqli_query($conn, $addQuery)) {
      echo "User added to group";
    } else {
        echo "Something went wrong! Please ty again";
    }
  }
}
?>
