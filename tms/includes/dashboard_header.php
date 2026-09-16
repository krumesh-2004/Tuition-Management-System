<?php
/**
 * Dashboard layout header. Expects $pageTitle, $pageSubtitle (optional), $activeMenu.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Dashboard') ?> | TMS</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div>
        <h1><?= e($pageTitle ?? '') ?></h1>
        <?php if (!empty($pageSubtitle)): ?><div class="subtitle"><?= e($pageSubtitle) ?></div><?php endif; ?>
      </div>
      <button id="sidebarToggle" class="btn btn-outline btn-sm" style="display:none;">&#9776;</button>
    </div>
    <div class="page-body">
      <?php
        if ($msg = flash('success')) echo '<div class="alert alert-success" data-autohide>' . e($msg) . '</div>';
        if ($msg = flash('error')) echo '<div class="alert alert-error" data-autohide>' . e($msg) . '</div>';
        if ($msg = flash('info')) echo '<div class="alert alert-info" data-autohide>' . e($msg) . '</div>';
      ?>
