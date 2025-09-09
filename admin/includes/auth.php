<?php
session_start();
require_once __DIR__ . "/../class/class_admin.php";

// Login check
Admin::requireLogin();

// Helper functions
function isAdmin()
{
    return $_SESSION['user_role'] === 'admin';
}

function isManager()
{
    return $_SESSION['user_role'] === 'manager' || $_SESSION['user_role'] === 'branch_manager';
}

function isStaff()
{
    return $_SESSION['user_role'] === 'staff';
}

// Restrict Access by Role
function requireRole($roles = [])
{
    if (!in_array($_SESSION['user_role'], $roles)) {
        echo "<h3 style='color:red; text-align:center; margin-top:50px;'>🚫 Access Denied!</h3>";
        exit;
    }
}
