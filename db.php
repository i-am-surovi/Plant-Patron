<?php
// Database credentials
$servername = "localhost"; // Typically 'localhost' for local servers
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "plantcare"; // Your database name
$conn = "";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn) {
    echo "Connected!";
}
else {
    echo "Could not connect!";

}

?>