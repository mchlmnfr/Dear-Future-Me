<?php
// Admin page listing progress entries awaiting verification. Built using
// AdminLTE layout for a consistent admin UI.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Progress</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark bg-dark">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/admin/dashboard" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="/auth/logout" class="nav-link">Logout</a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/admin/dashboard" class="brand-link"><span class="brand-text font-weight-light">Admin Panel</span></a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="/admin/dashboard" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/verify_progress_list" class="nav-link active">
                            <i class="nav-icon fas fa-check-square"></i><p>Verify Progress</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/create_participant" class="nav-link">
                            <i class="nav-icon fas fa-user-plus"></i><p>Create Participant</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/declare_winners" class="nav-link">
                            <i class="nav-icon fas fa-trophy"></i><p>Declare Winners</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0">Verify Progress</h1></div>
                    <div class="col-sm-6">
                        <div class="float-right">
                            <a href="/admin/dashboard" class="btn btn-secondary">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <?php if (empty($pendingEntries)): ?>
                            <div class="alert alert-info">No progress entries awaiting verification.</div>
                        <?php else: ?>
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Pending Progress Entries</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Participant</th>
                                                    <th>Month</th>
                                                    <th>Submitted At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pendingEntries as $entry): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($entry['id']) ?></td>
                                                    <td><?= htmlspecialchars($entry['full_name']) ?></td>
                                                    <td><?= htmlspecialchars($entry['month_number']) ?></td>
                                                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($entry['created_at']))) ?></td>
                                                    <td><a href="/verification/review/<?= htmlspecialchars($entry['id']) ?>" class="btn btn-primary btn-sm">Review</a></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <strong>&copy; <?= date('Y') ?> Dear Future Me.</strong> All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>