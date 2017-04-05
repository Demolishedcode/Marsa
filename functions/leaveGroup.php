<?php
session_start();
require_once '../includes/dbconfig.php';

//if (isset($_POST['leave'])) {
  $user = $_SESSION['user_id'];

  // Check if admin of group
  $getAdmin = "SELECT group_id FROM groups WHERE admin='$user'";
  $resultAdmin = mysqli_query($conn, $getAdmin);
  $checkAdmin = mysqli_num_rows($resultAdmin);

  // When admin
  if ($checkAdmin) {
    foreach ($resultAdmin as $row) {
      $groupId = $row['group_id'];
    }

    // Get all left users in group
    $query = "SELECT user_id FROM user_group WHERE group_id='$groupId' AND NOT user_id='$user'";
    $resultUsers = mysqli_query($conn, $query);
    $numUsers = mysqli_num_rows($resultUsers);

    if ($numUsers > 0) {
      $leftUsers = array();

      foreach ($resultUsers as $row) {
        array_push($leftUsers, $row['user_id']);
      }

      $newAdmin = rand(0, count($leftUsers) - 1);
      $newAdmin = $leftUsers[$newAdmin];

      $query = "UPDATE groups SET admin='$newAdmin' WHERE group_id='$groupId'";
      $changeAdmin = mysqli_query($conn, $query);
    } else {
      $query = "DELETE FROM groups WHERE group_id='$groupId'";
      $deleteGroup = mysqli_query($conn, $query);
    }
  }

  // Delete todolist
  $query = "SELECT todolist_id FROM todo_list WHERE creator='$user'";
  $result = mysqli_query($conn, $query);

  foreach ($result as $row) {
    $toDoListId = $row['todolist_id'];
    echo $toDoListId;

    $query = "DELETE FROM todo_list_items WHERE todolist_id='$toDoListId'";
    $deleteTodolistItems = mysqli_query($conn, $query);
  }

  $query = "DELETE FROM todo_list WHERE creator='$user';";
  $deleteToDoList = mysqli_query($conn, $query);

  // Remove from group and delete data
  $query = "UPDATE user_group SET group_id=null WHERE user_id='$user';";
  $query .= "DELETE FROM group_notes WHERE user_id='$user';";
  $query .= "DELETE FROM group_events WHERE event_creator='$user';";
  $deleteData = mysqli_multi_query($conn, $query);
//}
?>
