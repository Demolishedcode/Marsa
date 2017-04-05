<?php
session_start();
 if (!isset($_SESSION['user_name'])) {
   header('Location: login');
   exit();
 }

include 'includes/navigation.php'; // Add navigation
 ?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/style-userprofile.css" type="text/css"/>
        <script src="script/user-profile/user-profile.js"></script>
    </head>
    <body>
       <?php
        // Pre-set variables for global scope
        $infoMessage = $user_about = $groupName = $requestTitle = $requests = null;
        $groupTitle = "Group";

        // Get user email for ID selection
        $user_email = $_SESSION['user_email'];

        // Get user Register date
        $resultRegister = mysqli_query($conn, "SELECT date FROM user WHERE email='$user_email'");

        foreach ($resultRegister as $row) {
            $user_register = $row['date'];
        }

        // Fetch user meta_value about
        function getAbout() {
            // Fetch global scope variables
            global $conn, $user_email, $user_about;

            // Get the information in meta_value for about
            $queryAbout = "SELECT meta_value FROM user_meta WHERE meta_key='about' AND user_id=(SELECT user_id FROM user WHERE email='$user_email')";
            $resultAbout = mysqli_query($conn, $queryAbout);

            // Get the meta value
            foreach ($resultAbout as $row) {
                $user_about = $row['meta_value'];
            }

            // Check if about has a value
            if (empty($user_about)) {
                // If no value set standard text
                $user_about = "No user description given! Click 'Change about' to give yourself a cool description";
            }
        }

        getAbout();

        // Set new user information
        if (isset($_POST['submit_info'])) {
            // Get form values
            $newInfo = stripVariables($_POST['user_information']);

            // Set new password
            $infoQuery = "UPDATE user_meta SET meta_value='$newInfo' WHERE meta_key='about' AND user_id=(SELECT user_id FROM user WHERE email='$user_email')";

            $addInfo = mysqli_query($conn, $infoQuery);
            // Update user information
            getAbout();
        }


        // Change the password of the user
        if (isset($_POST['change'])) {
            // Fetch form values
            $oldPassowrd = stripVariables($_POST['opassword']);
            $password = stripVariables($_POST['npassword']);
            $confirmPassword = stripVariables($_POST['cpassword']);
            $blockChange = false; // Prevent reset if something is not valid

            // Get old password for comparison
            $queryPassword = "SELECT password FROM user WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
            $getPassowrd = mysqli_query($conn, $queryPassword);

            // Fetch Password
            foreach ($getPassowrd as $row) {
                $userPassword = $row['password'];
            }

            if (hash('sha256',$oldPassowrd) != $userPassword) {
                $infoMessage = "<p class='error'>Wrong old password<p>"; // Error message
                $blockChange = true;
            }

            // Check if one is empty
            if (empty($password) || empty($confirmPassword || empty($oldPassowrd))) {
                $infoMessage = "<p class='error'>Please fill in all fields<p>"; // Error message
                $blockChange = true;
            }

            // Check password length
            if (strlen($password) < 8) {
                $infoMessage = "<p class='error'>Your password is to short!<p>"; // Error message
                $blockChange = true;
            }

            // If passwords don't match
            if ($password != $confirmPassword) {
                $infoMessage = "<p class='error'>Your passwords don't match!<p>"; // Error message
                $blockChange = true;
            }

            // Check if no invalid information
            if (!$blockChange) {
                $password = hash('sha256',$password); // Encrypt the password

                // Add new password to the database
                $changePassword = mysqli_query($conn, "UPDATE user SET password='$password' WHERE email='$user_email'");

                // Create succes message
                $infoMessage = "<p class='succes'>Password changed succesfully<p>";
            }
        }

        // Get group name of joined group
        $querySelect = "SELECT group_id FROM user_group WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
        $select = mysqli_query($conn, $querySelect);

        // Get value of select
        $group_id = null;

        foreach ($select as $row) {
            global $group_id;

            $group_id = $row['group_id'];
        }

        // Set groupname
        if (!empty($group_id)) {
            // Search for group name
            $queryGroup = "SELECT name FROM groups WHERE group_id='$group_id'";

            if ($group = mysqli_query($conn, $queryGroup)) {
                // Get groupname
                foreach ($group as $row) {
                    $groupName = substr($row['name'], 0, 15) . "<span class='leave'>&#x2613</span>";
                }
            }// Add if necessary error

            // If admin in group show requests
            $getAdmin = "SELECT admin FROM groups WHERE group_id='$group_id'";
            $resultAdmin = mysqli_query($conn, $getAdmin);

            // Get results
            foreach($resultAdmin as $row) {
                $admin_id = $row['admin'];
            }

            if ($admin_id == $_SESSION['user_id']) {
                // If user is admin
                $requestTitle = 'Group Join Requests';
                $groupTitle = "Group - <span class='remove'><a href=''#''>Delete group</a></span>";

                // Get all group requests
                $queryAllRequests = "SELECT user_id FROM requests WHERE group_id='$group_id'";
                $allRequests = mysqli_query($conn, $queryAllRequests);

                if(mysqli_num_rows($allRequests) > 0) {

                  foreach ($allRequests as $row) {
                      $userID_Request = $row['user_id'];
                      $userRequest = "SELECT fname, sname FROM user WHERE user_id='$userID_Request'";
                      $resultUser = mysqli_query($conn, $userRequest);

                      // Fetch all data
                      foreach ($resultUser as $row) {
                          $userRequestName = $row['fname'] . ' ' . $row['sname'];

                          $requests .= "<tr class='requests' id='$userID_Request'><td>$userRequestName</td><td><span>Accept</span><span>Decline</span></li></td></tr>";
                      }
                  }
                } else {
                  $requests = "<tr><td>No requests found!</td></tr>";
                }
            }
        } else {
            // User did not joined a group
            $groupName = "<a href='groups'>Join / Create group</a>";

            // Show send request
            $requestTitle = 'Send Request';

            // Get user requests
            $queryRequest = "SELECT group_id FROM requests WHERE user_id=(SELECT user_id FROM user WHERE email='$user_email')";
            $resultRequests = mysqli_query($conn, $queryRequest);

            if (mysqli_num_rows($resultRequests) > 0) {
                foreach ($resultRequests as $row) {
                    $request = $row['group_id'];

                    // Get group name
                    $queryName = "SELECT name FROM groups WHERE group_id='$request'";
                    $resultName = mysqli_query($conn, $queryName);

                    foreach ($resultName as $row) {$name = $row['name'];}

                    $requests .= "<tr class='send' id=" . $_SESSION['user_id'] . "><td>$name</td><td><span>Cancel</span></td>";
                }
            } else {
                $requests = "<tr><td>No requests found! Goto groups and find a group</td></tr>";
            }
        }

        // Prevent Injections
        function stripVariables($input){
            $var = trim($input);
            $var = strip_tags($input);
            $var = stripslashes($input);
            $var = htmlspecialchars($input);

            return $var;
        }
        ?>
        <div class="change-about">
            <div class="change-title">
                Change your information
            </div>
            <div class="count-chars">
                You have <span>255</span> chars left<br>
                <span style="color:red">Atleast 10 characters needed</span>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                <textarea name="user_information" cols="35" rows="10" placeholder="Your information" maxlength="255"></textarea><br>
                <input type="submit" value="Submit" name="submit_info"><div class="about-cancel">cancel</div>
            </form>
        </div>
        <div class="bg-notification"></div>
        <section>
            <div class="section-title">
                My Profile
            </div>
            <div class="user-content">
                <div class="user-info">
                    <div class="info-main">
                        <p><?php echo $_SESSION['user_name'];?></p>
                        <p>E-mail Adress:<br>
                        <span><?php echo $user_email;?></span>
                        </p>
                        <p>Registered Since:<br>
                        <span><?php echo $user_register;?></span></p>
                    </div>
                    <div class="info-about">
                        <p><?php echo $user_about;?></p>
                        <p>Change about</p>
                    </div>
                    <div class="view-reset">
                        <div class="title">
                            <p>
                                Change your password<br>
                                <span>your password has to be atleast 8 characters long!</span><br><br>
                                <span><?php echo $infoMessage;?></span>
                            </p>
                        </div>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                           <input type="text" placeholder="Old password" name="opassword"><br>
                            <input type="text" placeholder="New password" name="npassword"><br>
                            <input type="text" placeholder="Confirm new password" name="cpassword"><br>
                            <input type="submit" value="Change" name="change">
                        </form>
                    </div>
                </div>
                <div class="user-view">
                    <div class="view-groups">
                        <div class="title">
                            <p>
                                <?php echo $groupTitle; ?>
                            </p>
                        </div>
                        <div class="group">
                            <!--Get Group Name-->
                            <p><?php echo $groupName; ?></p>
                        </div>
                    </div>
                    <div class="view-requests">
                        <div class="title">
                            <?php echo $requestTitle;?>
                        </div>
                        <div class="requests-container">
                            <table>
                              <?php echo $requests;?>
                               <!--Here will group request be shown-->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
