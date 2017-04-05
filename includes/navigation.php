<?php
include_once "dbconfig.php"; // Connect to the database
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $_SESSION['user_name'];?> || Marsa</title>
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <link rel="stylesheet" href="css/navigation.css" type="text/css">
        <link href="css/afbeeldingen/App.png" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="script/jquery-3.2.0.min.js"></script>
        <script src="script/jquery-ui.min.js"></script>
        <script src="script/user-interface/script-user.js"></script>
        <meta charset="utf-8">
    </head>
    <body>
       <?php
        // Get First Letter of user name
        $firstLetter = substr($_SESSION['user_name'], 0,1)
        ?>
        <nav>
           <div class="responsive-menu">
               <img src="css/afbeeldingen/bars.png">
               <span>Menu</span>
           </div>
            <div class="nav-inner">
                <ol>
                    <li id="profile">
                        <a href="profile" class="a-icon">
                            <span class="icon"><?php echo $firstLetter;?></span>
                            <span class="icon-sub"><?php echo $_SESSION['user_name']?></span>
                        </a>
                    </li>
                    <li class="menu-trigger">
                        <span><img src="css/afbeeldingen/bars.png"></span>
                    </li>
                    <?php
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
                    if ($group != null) {
                      echo "
                      <li id='schedule'>
                          <a href='calendar'>
                              <span><img src='css/afbeeldingen/Calender.png'></span>
                              <span>Schedule</span>
                          </a>
                      </li>
                      <li id='todolist'>
                          <a href='todolist'>
                              <span><img src='css/afbeeldingen/list.png'></span>
                              <span>Shoppinglist</span>
                          </a>
                      </li>
                      ";
                    }
                    ?>
                    <li id="groups">
                        <a href="groups">
                            <span><img src="css/afbeeldingen/group-settings.png"></span>
                            <span>Groups</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout">
                            <span><img src="css/afbeeldingen/logout.png"></span>
                            <span>Logout</span>
                        </a>
                    </li>
                </ol>
            </div>
        </nav>
    </body>
</html>
