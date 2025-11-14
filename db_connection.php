<?php
$servername = "localhost";
$username = "root";
$password = "darshan@1525";
$dbname = "parking_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>