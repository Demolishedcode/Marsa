<?php
require_once '../includes/dbconfig.php';

session_start();

$user = $_SESSION['user_id'];

$start_year = 2010;
$end_year   = 2030;

if (isset($_POST['addEvent'])) {
  $title       = stripVariables($_POST['title']);
  $description = stripVariables($_POST['description']);
  $date        = stripVariables($_POST['date']);

  $error = '';

  if (strlen($title) < 10) {
    $error .= 'Title is to short<br>';
  }

  if (strlen($description) < 10) {
    $error .= 'Make your description more spefic<br>';
  }

  $numDate = explode("-",$date);


  if (!validateDate($date)) {
    $error .= 'No valid date';
  } else if ($numDate[0] < $start_year || $numDate[0] > $end_year) {
    $error .= "Can't plan a head so far! Choose a year between 2010 and 2030";
  }

  if ($error) {
    echo "<p style='color:red'>$error</p>";
  } else {
    $sql = "INSERT INTO group_events (group_id, event_creator, title, notes, date) VALUES ((SELECT group_id FROM user_group WHERE user_id='$user'), '$user', '$title', '$description', '$date')";
    $add = mysqli_query($conn, $sql);

    echo "<p style='color:green'>Succesfully added event!</p>";
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

function validateDate($date)  {
  $d = DateTime::createFromFormat('Y-m-d',$date);
  return $d && $d->format('Y-m-d') === $date;
}
?>
