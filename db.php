<?php
$host = 'localhost';
$dbname = 'rmotorsi_basi';
$username = 'rmotorsi_dualam';
$password = 'F#xrHngWNRJk5';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
