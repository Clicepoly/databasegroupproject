<?php
$host = 'localhost';
$dbuser = 'root';
$dbpassword = '1234';
$dbname = 'teachers'; // Replace with your actual database name

$mysqli = new mysqli($host, $dbuser, $dbpassword, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set the character set to utf8
$mysqli->set_charset("utf8");
?>