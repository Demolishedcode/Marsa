<?php
session_start();
 if (!isset($_SESSION['user_name'])) {
   header('Location: login');
   exit();
 }
?>
<!DOCTYPE html>
<html>
   <?php
    include 'includes/navigation.php'; // Add navigation

    function check ($n) {
      if (isset($_SESSION['createGroup'])) {
        $number = $_SESSION['createGroup'];

        //return "checked";
        if ($n == $number) {
          return "checked";
        }
      }
    }

    $user_email = $_SESSION['user_email'];

    // Check if user is not already in a group
    $queryCheckGroup = "SELECT group_id FROM user_group WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
    $checkGroup = mysqli_query($conn, $queryCheckGroup);

    $group = null;

    // Fetch group value
    foreach($checkGroup as $row) {
      $group = $row['group_id'];
    }
    ?>
    <head>
        <link rel="stylesheet" href="css/style-usergroup.css" type="text/css"/>
        <script src="script/user-group/script-usergroup.js"></script>
    </head>
    <body>
        <div class="group-create">
            <div class="create-inner">
                <p>Create your own group<span><img src="css/afbeeldingen/close.png" alt="Close"></span></p>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" <?php if ($group != null) {echo "class='disabled'";} ?>>
                    <label for="group_name" style="font-size: 10pt"><?php if ($group != null) {echo "You're already in a group!";} ?></label>
                    <input type="text" name="group_name" placeholder="Group name" autofocus><br>
                    <textarea name="group_description" placeholder="Small group description" cols="30" rows="10"></textarea><br>
                    <p>Choose your catergory</p>
                    <div class="wrap-categories">
                        <input type="radio" name="category" value="family" <?php echo check(1);?>>Family<br>
                        <div class="features-family features">
                            <ol>
                                <li>Lorem Ipsum</li>
                                <li>Lorem Ipsum</li>
                            </ol>
                        </div>
                        <input type="radio" name="category" value="business" <?php echo check(2);?>>Business<br>
                        <div class="features-business features">
                            <ol>
                                <li>Lorem Ipsum</li>
                                <li>Lorem Ipsum</li>
                            </ol>
                        </div>
                        <input type="radio" name="category" value="friends" <?php echo check(3);?>>Friends<br>
                        <div class="features-friends features">
                            <ol>
                                <li>Lorem Ipsum</li>
                                <li>Lorem Ipsum</li>
                            </ol>
                        </div>
                        </div>
                    <input type="submit" value="Create group" name="create">
                </form>
            </div>
        </div>
        <div class="group-join">
            <div class="join-inner">
                <p>Group search <span><img src="css/afbeeldingen/close.png" alt="Close"></span></p>
                <form action="searchGroup.php" method="post">
                    <input type="text" name="search" placeholder="search" autofocus>
                    <input type="image" name="submit" src="css/afbeeldingen/search-submit.png">
                </form>
                <div class="search-result">
                    <ol class= "results">
                     <!--Here is where the search results wil be shown-->
                    </ol>
                </div>
            </div>
        </div>
        <div class="overlay"></div>
        <section>
            <div class="section-title">
                Group Setup
            </div>

            <div class="group-option">
                <div class="option-inner">
                    <div class="join">
                        <div>
                            <img src="css/afbeeldingen/search-white.png" alt="Search for group">
                            <p>Join a group</p>
                        </div>
                    </div>
                    <div class="create">
                        <div>
                            <img src="css/afbeeldingen/join.png" alt="Create a group">
                            <p>Create a new group</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
