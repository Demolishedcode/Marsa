<?php
session_start();
 if (!isset($_SESSION['user_name'])) {
   header('Location: login');
   exit();
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
    <link rel="stylesheet" href="css/style-userinterface.css" type="text/css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="script/user-interface/script-userinterface.js"></script>
    <script>
    $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
    });

    function reload(form){
      var month_val=document.getElementById('month').value;
      var year_val=document.getElementById('year').value;
      self.location='calendar?month=' + month_val + '&year=' + year_val ;
    }
  </script>
  </head>
  <body>
    <div class="add-event">
        <div class="add-inner">
            <p>Add new event<span><img src="css/afbeeldingen/close.png" alt="Close"></span></p>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
                <label for="event-title" style="font-size: 10pt"></label>
                <input type="text" name="event-title" placeholder="Title" autofocus required><br>
                <textarea name="event_description" placeholder="Event description" cols="30" rows="10" required></textarea><br>
                <input type="text" name="date" placeholder="YYYY-MM-DD" id="datepicker" required>
                <input type="submit" value="Add event" name="event">
            </form>
        </div>
    </div>
    <!-- <div class="show-event" style="display: block">
      <div class="show-inner">
        <p>Event</p>
        <form action="<?php //echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
            <label for="title" style="font-size: 10pt"></label>
            <input type="text" name="title" placeholder="Title" autofocus required><br>
            <textarea name="notes" placeholder="Event description" cols="30" rows="10" required></textarea><br>
            <input type="text" name="date" placeholder="YYYY-MM-DD" id="datepicker" required>
            <input type="hidden" name="id" value="">
            <div class="change" >
              <input type="submit" value="Save" name="save"><span>Delete</span><span>Cancel</span>
            </div>
            <div class="edit" style="display:none">
              <span>Edit Event</span>
            </div>
        </form>
      </div>
    </div> -->
    <div class="overlay"></div>
    <section>
      <div class="section-title">
          Calendar
      </div>
      <div class="container">
        <table class="calender">
        <?php
        $start_year = 2010;
        $end_year   = 2030;

        @$month = $_GET['month'];
        @$year  = $_GET['year'];

        if (!($month < 13 && $month > 0)) {
          $month = date('m'); // Set default month
        }

        if (!($year <= $end_year && $year >= $start_year)) {
          $year = date('Y'); // Set default year
        }

        $year_month = $year. '-' . $month;
        $year_month = new DateTime("$year-$month-1");
        $year_month = $year_month->format('Y-m');


        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');

        echo "<tr =><th colspan='7'>$monthName / $year</th></tr>";

        $days_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_day  = date('w', mktime(0,0,0,$month,1,$year)); // Take a look at this

        $first_day -= 1;
        if ($first_day < 0) {$first_day = 6;}

        $cur_day        = date('d');
        $cur_year_month = new DateTime();
        $cur_year_month = $cur_year_month->format('Y-m');


        $adj_start = str_repeat("<td class='none'>*&nbsp;</td>",$first_day);

        $blank_end  = 42 - $days_month - $first_day;
        if ($blank_end >= 7) {
          $blank_end = $blank_end - 7;
        }

        $adj_end = str_repeat("<td class='none'>*&nbsp;</td>",$blank_end);

        echo "<tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr><tr>";

        $sqlEvents = "SELECT * FROM group_events WHERE group_id=(SELECT group_id FROM user_group WHERE user_id={$_SESSION['user_id']})";
        $queryEvents = mysqli_query($conn, $sqlEvents);
        $numEvents   = mysqli_num_rows($queryEvents);

        // foreach ($queryEvents as $key) {
        //   $creator = $key['event_creator'];
        //   $title   = $key['title'];
        //   $notes   = $key['notes'];
        //   $date    = $key['date'];
        //
        //   $sqlSelect = "SELECT fname, sname FROM user WHERE user_id={$creator}";
        //   $querySelect = mysqli_query($conn, $sqlSelect);
        //
        //   foreach ($querySelect as $row) {
        //     $creatorName = $row['fname'] . ' ' . $row['sname'];
        //   }
        // }

        $count_days = 0 + $first_day;

        for ($i = 1; $i <= $days_month; $i++) {
          // if ($i == $cur_day && $cur_year_month == $year_month) {
          //     echo $adj_start . "<td bgcolor='#ff5400'><a href='#' style='color:white'>$i</a></td>";
          // } else {
          //   echo $adj_start . "<td><a href='#'>$i</a></td>";
          // }

          // $sqlSelect = "SELECT fname, sname FROM user WHERE user_id={$creator}";
          // $querySelect = mysqli_query($conn, $sqlSelect);
          //
          // foreach ($querySelect as $row) {
          //   $creatorName = $row['fname'] . ' ' . $row['sname'];
          // }
          $selectionDate = $year.'-'.$month.'-'.$i;

          $sqlEvent = "SELECT event_id, date FROM group_events WHERE date='$selectionDate' AND group_id=(SELECT group_id FROM user_group WHERE user_id={$_SESSION['user_id']})";
          $queryEvent = mysqli_query($conn, $sqlEvent);
          $amount = mysqli_num_rows($queryEvent);

          if ($amount > 0) {
            echo $adj_start . "<td bgcolor='#ff9000' data-day='$selectionDate'><a href='#' style='color:white'>$i</a></td>";
          } else if ($i == $cur_day && $cur_year_month == $year_month) {
            echo $adj_start . "<td bgcolor='#ff5400' data-day='$selectionDate'><a href='#' style='color:white'>$i</a></td>";
          }  else {
            echo $adj_start . "<td data-day='$selectionDate'><a href='#'>$i</a></td>";
          }


          $adj_start = '';

          $count_days++;

          if ($count_days == 7) {
            echo '</tr><tr>';
            $count_days = 0;
          }
        }

        echo $adj_end;
        ?>
        </table>
        <div class="side-container">
          <div class="side-box">
            <div class="box-title">
              Options
            </div>
            <?php
            echo "<select class=\"month\" id='month' onchange=\"reload(this.form)\">
            <option value=''>Select Month</option>";

            for ($i=1; $i <= 12 ; $i++) {
              $dateObject = DateTime::createFromFormat('!m', $i);
              $monthName  = $dateObject->format('F');

              if ($month == $i) {
                echo "<option value='$i' selected>$monthName</option>";
              } else {
                echo "<option value='$i'>$monthName</option>";
              }
            }

            echo '</select>
            <select class=\'year\' id=\'year\' onchange=\'reload(this.form)\'>
            <option value=\'\'>Select Year</option>';

            for ($o = $start_year; $o <= $end_year; $o++) {
              if ($year == $o) {
                echo "<option value='$o' selected>$o</option>";
              } else {
                echo "<option value='$o'>$o</option>";
              }
            }

            echo "</select>";
            ?>
            <div class="calender-options">
              <hr>
              <span id='add'>Add new event</span>
            </div>
          </div>
          <div class="side-box">
            <div class="box-title">
              Event(s) <span></span>
            </div>
            <div class="events">
              <!-- Here wil the events be shown -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>
