<?php
// Shared layout for participant pages.
// Expects the following variables to be set before including this file:
//   - $activePage: slug for active sidebar link (e.g. 'dashboard', 'activity', ...)
//   - $content: the HTML content of the page
// It will automatically detect the theme from session and show date/time and icons for theme toggle.

// Determine theme from session; default to dark. This influences dark-mode class on body.
$theme = $_SESSION['theme'] ?? 'dark';

// Get current date and time in Asia/Manila timezone
date_default_timezone_set('Asia/Manila');
$currentDate = date('F j, Y');
$currentTime = date('h:i A');

// Determine theme toggle icon (sun for light mode, moon for dark mode)
$toggleIcon = $theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
$toggleTitle = $theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
// Determine new theme for toggle link
$newTheme = $theme === 'dark' ? 'light' : 'dark';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Participant Portal</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- AdminLTE Skins (for gradient backgrounds) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/skins/_all-skins.min.css">
</head>
<body class="hold-transition<?= $theme === 'dark' ? ' dark-mode' : '' ?> sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand <?= $theme === 'dark' ? 'navbar-dark bg-dark' : 'navbar-light bg-white' ?>">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/participant/dashboard" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Date and time -->
            <li class="nav-item">
                <span class="nav-link">
                    <i class="far fa-calendar-alt"></i> <?= htmlspecialchars($currentDate) ?>
                    &nbsp;
                    <i class="far fa-clock"></i> <?= htmlspecialchars($currentTime) ?>
                </span>
            </li>
            <!-- Theme toggle -->
            <li class="nav-item">
                <a class="nav-link" href="?theme=<?= $newTheme ?>" title="<?= $toggleTitle ?>">
                    <i class="<?= $toggleIcon ?>"></i>
                </a>
            </li>
            <!-- Logout -->
            <li class="nav-item">
                <a class="nav-link" href="/auth/logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar <?= $theme === 'dark' ? 'sidebar-dark-primary' : 'sidebar-light-primary' ?> elevation-4">
        <!-- Brand Logo -->
        <a href="/participant/dashboard" class="brand-link">
            <span class="brand-text font-weight-light">Dear Future Me</span>
        </a>
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="/participant/dashboard" class="nav-link<?= $activePage === 'dashboard' ? ' active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/activity/log" class="nav-link<?= $activePage === 'activity' ? ' active' : '' ?>">
                            <i class="nav-icon fas fa-running"></i>
                            <p>Log Activity</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/weighin/submit" class="nav-link<?= $activePage === 'weighin' ? ' active' : '' ?>">
                            <i class="nav-icon fas fa-weight"></i>
                            <p>Weekly Weigh‑In</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/progress/submit" class="nav-link<?= $activePage === 'progress' ? ' active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Monthly Progress</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/leaderboard/index" class="nav-link<?= $activePage === 'leaderboard' ? ' active' : '' ?>">
                            <i class="nav-icon fas fa-list-ol"></i>
                            <p>Leaderboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/participant/plan" class="nav-link<?= $activePage === 'plan' ? ' active' : '' ?>">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Your Plan</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Page content -->
        <?= $content ?? '' ?>
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            Keep up the good work!
        </div>
        <strong>Dear Future Me &copy; <?= date('Y') ?></strong>
    </footer>
</div>
<!-- ./wrapper -->
<!-- AdminLTE JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>