<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// detect if inside owner/ or admin/
$basePath = (strpos($_SERVER['PHP_SELF'], '/owner/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Parking System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="<?= $basePath ?>dashboard.php">Parking System</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['Role'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $basePath ?>login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
