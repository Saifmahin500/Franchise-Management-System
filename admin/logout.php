<?php // admin/logout.php
require_once __DIR__ . '/dbConfig.php';
require_once __DIR__ . '/class/class_admin.php';
Admin::logout();
header('Location: login.php');
exit;
