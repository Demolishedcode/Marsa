<?php
session_start();
require_once '../includes/dbconfig.php';

// Marks item as done
if (isset($_POST['mark']) && !empty($_POST['mark'])) {
    $item = $_POST['mark'];
    $item = explode(',',$item);

    for ($i = 0; $i < count($item); $i++) {
      $markQuery = "UPDATE todo_list_items SET done='1' WHERE item_id='" . $item[$i] . "'";
      $mark = mysqli_query($conn, $markQuery);
    }
}

if (isset($_POST['unmark']) && !empty($_POST['unmark'])) {
    $item = $_POST['unmark'];
    $item = explode(',',$item);

    for ($i = 0; $i < count($item); $i++) {
      $unmarkQuery = "UPDATE todo_list_items SET done='0' WHERE item_id='" . $item[$i] . "'";
      $unmark = mysqli_query($conn, $unmarkQuery);
    }
}

if (isset($_POST['delete'])  && !empty($_POST['delete'])) {
  $item = $_POST['delete'];
  $item = explode(',',$item);

  for ($i = 0; $i < count($item); $i++) {
    $deleteQuery = "DELETE FROM todo_list_items WHERE item_id='" . $item[$i] . "'";
    $delete = mysqli_query($conn, $deleteQuery);
  }
}

if (isset($_POST['delete_list'])) {
  $listId = $_POST['listId'];

  $deleteListQuery = "DELETE FROM todo_list WHERE todolist_id='$listId';";
  $deleteListQuery .= "DELETE FROM todo_list_items WHERE todolist_id='$listId'";
  $deleteList = mysqli_multi_query($conn, $deleteListQuery);

}
?>
