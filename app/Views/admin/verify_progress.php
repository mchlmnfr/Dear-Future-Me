<?php
// Admin review page for a single progress entry. Utilises AdminLTE layout
// to provide a streamlined review experience.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Progress Entry</title>
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
            <li class="nav-item"><a href="/auth/logout" class="nav-link">Logout</a></li>
        </ul>
    </nav>
    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/admin/dashboard" class="brand-link"><span class="brand-text font-weight-light">Admin Panel</span></a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                    <li class="nav-item"><a href="/admin/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
                    <li class="nav-item"><a href="/admin/verify_progress_list" class="nav-link active"><i class="nav-icon fas fa-check-square"></i><p>Verify Progress</p></a></li>
                    <li class="nav-item"><a href="/admin/create_participant" class="nav-link"><i class="nav-icon fas fa-user-plus"></i><p>Create Participant</p></a></li>
                    <li class="nav-item"><a href="/admin/declare_winners" class="nav-link"><i class="nav-icon fas fa-trophy"></i><p>Declare Winners</p></a></li>
                </ul>
            </nav>
        </div>
    </aside>
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0">Review Progress Entry</h1></div>
                    <div class="col-sm-6">
                        <div class="float-right"><a href="/admin/verify_progress_list" class="btn btn-secondary">Back to List</a></div>
                    </div>
                </div>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php if (!empty($entry)): ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Entry Details</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Participant:</strong> <?= htmlspecialchars($entry['full_name']) ?></p>
                                    <p><strong>Month:</strong> <?= htmlspecialchars($entry['month_number']) ?></p>
                                    <p><strong>Note:</strong><br><?= nl2br(htmlspecialchars($entry['progress_note'])) ?></p>
                                    <?php if (!empty($entry['photo_url'])): ?>
                                        <p><strong>Photo Evidence:</strong></p>
                                        <img src="<?= htmlspecialchars($entry['photo_url']) ?>" alt="Progress Photo" class="img-fluid mb-3" style="max-width:100%;height:auto;">
                                    <?php endif; ?>
                                    <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($entry['status'])) ?></p>
                                </div>
                            </div>
                            <?php if ($entry['status'] === 'pending'): ?>
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Verification</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="" method="post">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="approved">Approve</option>
                                                    <option value="rejected">Reject</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Score</label>
                                                <input type="number" name="score" class="form-control" min="0" placeholder="Enter score">
                                                <small class="form-text text-muted">Assign a score for approved entries. Leave blank or 0 when rejecting.</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Verification</button>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">This entry has already been <?= htmlspecialchars($entry['status']) ?>.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">Progress entry not found.</div>
                <?php endif; ?>
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