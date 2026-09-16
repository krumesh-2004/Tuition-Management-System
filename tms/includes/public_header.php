<?php
/**
 * Public header for marketing/auth pages (index, about, contact, register).
 * Expects optional $pageTitle to be set before include.
 */
$prefix = Auth::basePathPrefix();
$pageTitle = $pageTitle ?? 'Tuition Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | TMS</title>
<link rel="stylesheet" href="<?= $prefix ?>assets/css/style.css">
</head>
<body>
<nav class="public-nav">
  <a href="<?= $prefix ?>index.php" class="brand">
    <span class="logo-mark">T</span> Brightpath Tuition
  </a>
  <div class="links">
    <a href="<?= $prefix ?>index.php">Home</a>
    <a href="<?= $prefix ?>about.php">About</a>
    <a href="<?= $prefix ?>contact.php">Contact</a>
    <a href="<?= $prefix ?>register.php" class="cta">Student Register</a>
  </div>
</nav>
<?php if ($msg = flash('info')): ?>
  <div class="alert alert-info" data-autohide style="margin:16px 40px 0; border-radius:6px;"><?= e($msg) ?></div>
<?php endif; ?>
