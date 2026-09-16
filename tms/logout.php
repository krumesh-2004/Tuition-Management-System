<?php
require_once __DIR__ . '/includes/init.php';
Auth::logout();
session_start();
flash('info', 'You have been logged out successfully.');
header('Location: index.php');
exit;
