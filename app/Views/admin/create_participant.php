<?php
// Admin view to create a new participant. Uses AdminLTE layout and components
// for a clean and consistent look.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Participant</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
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

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand -->
        <a href="/admin/dashboard" class="brand-link"><span class="brand-text font-weight-light">Admin Panel</span></a>
        <!-- Sidebar menu -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="/admin/dashboard" class="nav-link">
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
                        <a href="/admin/create_participant" class="nav-link active">
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
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Create Participant</h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-right">
                            <a href="/admin/dashboard" class="btn btn-secondary">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Participant Details</h3>
                            </div>
                            <form action="<?= htmlspecialchars('/admin/createParticipant') ?>" method="post">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department_id" class="form-control" required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= htmlspecialchars($dept['id']) ?>">
                                                    <?= htmlspecialchars($dept['name'] ?? $dept['department_name'] ?? '') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Employee ID</label>
                                        <input type="text" name="employee_id" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Temporary Password</label>
                                        <input type="text" class="form-control" value="HandF12345" disabled>
                                        <small>The participant will be required to change their password after first login.</small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Create Participant</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong>&copy; <?= date('Y') ?> Dear Future Me.</strong> All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->
<!-- REQUIRED SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>