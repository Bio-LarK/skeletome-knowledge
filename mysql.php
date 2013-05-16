<?php

$host = "localhost";
$user = "drupal";
$pass = "sk3l3tom3";

$r = mysql_connect($host, $user, $pass);

if (!$r) {
    echo "Could not connect to server\n";
    trigger_error(mysql_error(), E_USER_ERROR);
} else {
    echo "Connection established\n";
}

echo mysql_get_server_info() . "\n";

$result = mysqli_query($con,"SELECT * FROM Persons");

while($row = mysqli_fetch_array($result))
{
    echo $row['FirstName'] . " " . $row['LastName'];
    echo "<br>";
}


mysql_close();

?>E_ALL | E_STRICT