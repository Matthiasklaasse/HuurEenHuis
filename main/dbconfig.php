<?php
$host = "localhost";
$dbusername = "root";
$password = ""; 
$database = "huureenhuis.nltest";

$conn = mysqli_connect($host, $dbusername, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
