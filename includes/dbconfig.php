<?php
//Set data connection to database
$host = "localhost";
$username = "root";
$password = "";
$database = "marsa_database";

ini_set("display_erros", "On");

// Connect to database
if (!$conn = mysqli_connect($host, $username, $password, $database)) {
    // Can't connect, show error message, stop connection
    die('Connection failed');
}
?>
