<?php
session_start();
require_once '../includes/dbconfig.php';

if (isset($_POST['remove'])) {
  $user = $_SESSION['user_id'];

  $query = "SELECT group_id FROM groups WHERE admin='$user'";
  $getGroupId = mysqli_query($conn, $query);

  foreach ($getGroupId as $row) {
    $groupId = $row['group_id'];
  }

  // Delete todolist
  $query = "SELECT todolist_id FROM todo_list WHERE group_id='$groupId'";
  $result = mysqli_query($conn, $query);

  foreach ($result as $row) {
    $toDoListId = $row['todolist_id'];

    $query = "DELETE FROM todo_list_items WHERE todolist_id='$toDoListId'";
    $deleteTodolistItems = mysqli_query($conn, $query);
  }

  $query = "DELETE FROM todo_list WHERE group_id='$groupId';";
  $deleteToDoList = mysqli_query($conn, $query);

  $query = "DELETE FROM groups WHERE group_id='$groupId';";
  $query .= "UPDATE user_group SET group_id=null WHERE group_id='$groupId';";
  $query .= "DELETE FROM group_notes WHERE group_id='$groupId';";
  $query .= "DELETE FROM group_events WHERE group_id='$groupId';";

  $leaveGroup = mysqli_multi_query($conn, $query);
}
?>
