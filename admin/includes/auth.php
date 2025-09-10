<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../class/class_admin.php";

// Login check
Admin::requireLogin();

// Helper functions
function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isManager()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manager';
}

function isStaff()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff';
}

// Restrict Access by Role
function requireRole($roles = [])
{
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $roles)) {
        echo "<h3 style='color:red; text-align:center; margin-top:50px;'>🚫 Access Denied!</h3>";
        exit;
    }
}
