<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdmin() {
    return isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'Admin';
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
