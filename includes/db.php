<?php
$host = "localhost";
$user = "root";   
$pass = "";       
$db   = "elderly_care";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
