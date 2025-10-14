<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require any logged-in user
function requireLogin() {
    if (!isset($_SESSION['username'])) {
        header("Location: ../../LoginFunction.php");
        exit();
    }
}

// Require admin user
function requireAdmin() {
    requireLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../../LoginFunction.php");
        exit();
    }
}
?>
