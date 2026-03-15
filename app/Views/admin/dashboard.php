<?php
// Admin dashboard using AdminLTE. Displays key metrics, department counts,
// and recent progress submissions. Utilises AdminLTE components for a
// professional, clean layout.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Optional custom styles can be defined here -->
    <style>
        .table th { text-transform: uppercase; font-size: 0.75rem; }
    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark bg-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/admin/dashboard" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="/auth/logout" class="nav-link">Logout</a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/admin/dashboard" class="brand-link">
            <span class="brand-text font-weight-light">Admin Panel</span>
        </a>
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="/admin/dashboard" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/verify_progress_list" class="nav-link">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Verify Progress</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/create_participant" class="nav-link">
                            <i class="nav-icon fas fa-user-plus"></i>
                            <p>Create Participant</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/declare_winners" class="nav-link">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Declare Winners</p>
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
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Admin Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h3><?= (int)$stats['participants'] ?></h3>
                                <p>Participants</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3><?= (int)$stats['goals'] ?></h3>
                                <p>Goals Submitted</p>
                            </div>
                            <div class="icon"><i class="fas fa-bullseye"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-warning">
                            <div class="inner">
                                <h3><?= (int)$stats['pendingVerifications'] ?></h3>
                                <p>Pending Verifications</p>
                            </div>
                            <div class="icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-info">
                            <div class="inner">
                                <h3><?= (int)$stats['activityEntries'] ?></h3>
                                <p>Activity Entries</p>
                            </div>
                            <div class="icon"><i class="fas fa-list"></i></div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Participants by Department</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Department</th>
                                                <th>Participants</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($departments as $dept): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($dept['department_name']) ?></td>
                                                    <td><?= (int)$dept['participant_count'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Recent Progress Submissions</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Participant</th>
                                                <th>Month</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recentProgress)): ?>
                                                <?php foreach ($recentProgress as $row): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                                                        <td><?= (int)$row['month_number'] ?></td>
                                                        <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                                                        <td><?= htmlspecialchars(date('M d, Y', strtotime($row['created_at']))) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center">No recent submissions.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>&copy; <?= date('Y') ?> Dear Future Me.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline">
            Wellness Challenge Admin Panel
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>