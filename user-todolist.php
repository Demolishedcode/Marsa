<?php
session_start();
 if (!isset($_SESSION['user_name'])) {
   header('Location: login');
   exit();
 }

 if (isset($_GET['c'])) {
   header("Location: todolist");
 }

include 'includes/navigation.php'; // Add navigation

$user_id = $_SESSION['user_id'];

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
  header('Location: profile');
  exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="css/style-usertodolist.css" type="text/css"/>
    <script src="script/user-todolist/script-todolist.js"></script>
  </head>
  <body>
    <?php
    // Add new todo_list to the database
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $item = str_replace('"','',$_POST['item']);
        $itemsArray = array_filter($item);

        // Add the todo_list
        $queryList = "INSERT INTO todo_list (creator, group_id, title) VALUES ('$user_id',(SELECT group_id FROM user_group WHERE user_id='$user_id'),'$title')";
        $resultList = mysqli_query($conn, $queryList);

        // Add items to the todo_list
        $lastId = mysqli_insert_id($conn);

        // Loop through items and create seperate querys for them
        foreach ($itemsArray as $key => $text) {
          $queryItem = "INSERT INTO todo_list_items (todolist_id, item, done) VALUES ('$lastId','$text','0');";
          // Execute the queryItem
          $resultItem = mysqli_query($conn, $queryItem);
        }
    }
    ?>
    <div class="create-list">
        <div class="create-list-inner">
          <p>Create to-do list<span><img src="css/afbeeldingen/close.png" alt="Close"></span></p>
          <p></p>
          <form action="todolist?c=true" method="post">
            <table>
              <tbody>
                <tr class="create-list-item">
                  <td>
                    <input type="text" name="title" placeholder="Title" autocomplete="off" autofocus>
                  </td>
                </tr>
                <tr class="create-list-item">
                  <td>
                    <input type="text" name="item[]" placeholder="New Item" autocomplete="off">
                  </td>
                  <td><img src="css/afbeeldingen/delete.png"></td>
                </tr>
                <tr class="create-list-item">
                  <td>
                    <input type="text" name="item[]" placeholder="New Item" autocomplete="off">
                  </td>
                  <td><img src="css/afbeeldingen/delete.png"></td>
                </tr>
                <tr class="create-list-item">
                  <td>
                    <input type="text" name="item[]" placeholder="New Item" autocomplete="off">
                  </td>
                  <td><img src="css/afbeeldingen/delete.png"></td>
                </tr>
                <tr class="create-list-item">
                  <td>
                    <input type="text" name="item[]" placeholder="New Item" autocomplete="off">
                  </td>
                  <td><img src="css/afbeeldingen/delete.png"></td>
                </tr>
                <tr>
                  <td id="new-item" colspan="2">Add new row</td>
                </tr>
                <tr>
                  <td colspan="2">
                    <input type="submit" name="submit" value="Create List" id="submit">
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>
    </div>
    <div class="overlay"></div>
    <section>
      <div class="section-title">
        To-do lists
        <div class="add">
          Add
          <img src="css/afbeeldingen/add.png" alt="Add">
        </div>
      </div>
      <!-- Place for al the to-do lists -->
      <?php
      /* Render al to-do lists from group */

      // First check if there are any lists
      $resultCheck = '';

      $checkLists = "SELECT * FROM todo_list WHERE group_id=(SELECT group_id FROM user_group WHERE user_id='$user_id')";
      $resultCheck = mysqli_query($conn, $checkLists);
      $numberList = mysqli_num_rows($resultCheck);
      // Get amount of lists created
      if ($numberList > 0) {

          $title = $id = "";

          foreach ($resultCheck as $key) {
            $title = $key['title'];
            $id = $key['todolist_id'];
            $creator = $key['creator'];
            $groupId = $key['group_id'];

            $getItems = "SELECT * FROM todo_list_items WHERE todolist_id='$id'";
            $resultItems = mysqli_query($conn, $getItems);

            echo "
            <div class='todo_list' id='$id'>
              <form action='todolist' method='post'>
                <table>
                  <tbody>
                    <tr>
                      <td colspan='4' style='text-align:center; padding-bottom:10px;'>$title</td>
                    </tr>
                    <tr>
                      <td>
                        <div class='inner-table' data-state='read'>
                          <table>
                            <tbody>";
                            foreach ($resultItems as $key) {
                              $item = $key['item'];
                              $item_id = $key['item_id'];
                              $done = $key['done'];

                              if ($done == 1) {
                                $checked = "checked";
                                $classDone = "class='done'";
                              } else {
                                $checked = "";
                                $classDone = "class='notdone'";
                              }

                              echo "
                              <tr id='$item_id' style='display:block'>
                                <td><input type='checkbox' name='done' $checked></td><td><input type='text' $classDone name='load_item' value='$item' autocomplete='off' disabled></td><td><img src='css/afbeeldingen/delete.png'></td>
                              </tr>
                              ";
                            };

                            $getAdmin = "SELECT admin FROM groups WHERE group_id='$groupId'";
                            $admin = mysqli_query($conn, $getAdmin);

                            foreach ($admin as $key) {
                              $admin = $key['admin'];
                            };
            echo "
                            </tbody>
                          </table>
                        </div>
                      </td>
                    </tr>";
                    if ($admin == $_SESSION['user_id'] || $creator == $_SESSION['user_id']) {
                        echo "<tr id='save-section'>
                          <td colspan='2'><input type='submit' name='save' value='Save'><span id='cancel'>Cancel</span><span style='margin-left: 30px' id='delete'>Delete</span></td>
                        </tr>
                        <tr>
                          <td colspan='2'><span id='edit'>Edit to-do list</span></td>
                        </tr>";
                    }
            echo "
                  </tbody>
                </table>
              </form>
            </div>
            ";
        }
      }
      ?>
    </section>
  </body>
</html>
