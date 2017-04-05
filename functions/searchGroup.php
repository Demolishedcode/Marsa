<?php
require_once '../includes/dbconfig.php';

// If something is posted
if (isset($_POST['search'])) {
    $word = stripVariables($_POST['search']);

    $querySearch = "SELECT name, description, group_id FROM groups WHERE name LIKE '%$word%'";
    $resultSearch = mysqli_query($conn, $querySearch);
    $numberSearch = mysqli_num_rows($resultSearch);

    // If something is found
    if ($numberSearch >= 1) {
        $end_result = '';

        // Fetch search values, create search results
        foreach ($resultSearch as $row) {
            global $word;

            // Get group values
            $name = $row['name'];
            $description = substr($row['description'], 0, 40);
            $groupId = $row['group_id'];

            // Make typed in word bold
            $bold = '<b>' . $word .'</b>';
            $fullWord = str_ireplace($word, $bold, $name);

            // Create list items for each search result
            $end_result .= "<li class='search-item'><div class='list-text'>" . $fullWord . "<br><b style='font-size:10pt; font-weight:normal'><span id='group_id'>" . $groupId . "</span> ,  " . $description ."...</b></div><div class='list-join'><div id='send_request'><p>Send Request</p></div><div><p>Cancel</p></div></div></li>";

        }

        echo $end_result;

    } else {
        // Nothing found five error
        echo "<li style='color:red; padding: 10px; border: none;'>No results found!</li>";
    }

}

// Prevent injections
function stripVariables($input){
    $var = trim($input);
    $var = strip_tags($var);
    $var = stripslashes($var);
    $var = htmlspecialchars($var);

    return $var;
}
?>
