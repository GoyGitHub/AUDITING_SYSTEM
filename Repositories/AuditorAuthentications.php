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
function requireAuditor() {
    requireLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'auditor') {
        header("Location: ../../LoginFunction.php");
        exit();
    }
}
?>
