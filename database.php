<?php
$servername = "lrgs.ftsm.ukm.my";
$username = "a185125";
$password = "hugebluelion";
$dbname = "a185125";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage()); 
?>
