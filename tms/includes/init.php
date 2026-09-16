<?php
/**
 * init.php
 * Central bootstrap: starts the session, loads configuration and
 * autoloads all OOP classes used across the system.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Student.php';
require_once __DIR__ . '/../classes/Tutor.php';
require_once __DIR__ . '/../classes/Notification.php';
require_once __DIR__ . '/../classes/Attendance.php';
require_once __DIR__ . '/../classes/Payment.php';
require_once __DIR__ . '/../classes/Timetable.php';
require_once __DIR__ . '/../classes/Auth.php';

/** Simple flash-message helper used across the app */
function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
