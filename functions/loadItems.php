<?php
require_once '../includes/dbconfig.php';

if (isset($_POST['loadItems'])) {
  $event_day = stripVariables($_POST['day']);

  $sqlSelect = "SELECT event_creator, title, notes, date FROM group_events WHERE date='$event_day'";
  $querySelect = mysqli_query($conn, $sqlSelect);
  $num = mysqli_num_rows($querySelect);

  $allEvents = '';

  if ($num > 0) {
    foreach ($querySelect as $key) {
      $creator = $key['event_creator'];
      $title   = $key['title'];
      $notes   = $key['notes'];
      $date    = $key['date'];

      $date    = explode(' ', $date);

      $sqlName = "SELECT fname, sname FROM user WHERE user_id='$creator'";
      $queryName = mysqli_query($conn, $sqlName);

      foreach ($queryName as $row) {
        $name = $row['fname'] . ' ' . $row['sname'];
      }

      $allEvents .= <<<HTML
      <div class='event-item' style='margin-bottom: 10px;'>
        <span><b>$title</b></span><br>
        <span>$notes</span>
      </div>
HTML;
    }

    echo $allEvents;
  } else {
    echo "No Events Found";
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
