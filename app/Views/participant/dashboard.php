<?php
// Participant dashboard view leveraging the AdminLTE 3 layout.
// This page displays the participant's key metrics, weight history, goal details,
// activity summary and quick actions using AdminLTE components. All custom
// styling has been removed in favour of the AdminLTE design system.

// Determine flash messages for success notifications
$flash = null;
if (!empty($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// Set active page for sidebar highlighting
$activePage = $activePage ?? 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Participant Dashboard</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Optional Chart.js -->
    <?php if (!empty($weightHistory) && count($weightHistory) >= 1): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <?php endif; ?>
    <!-- Custom styles can be defined here if necessary -->
    <style>
        /* Ensure table headers are uppercase and compact */
        .table th { text-transform: uppercase; font-size: 0.75rem; }
    </style>
</head>
<body class="hold-transition dark-mode sidebar-mini">
<div class="wrapper">
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand navbar-dark bg-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/participant/dashboard" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <!-- Right navbar -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="/auth/logout" role="button">Logout</a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
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

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Welcome back<?= isset($_SESSION['user_name']) ? ', ' . htmlspecialchars($_SESSION['user_name']) : '' ?>!</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <!-- Optional breadcrumb or controls could go here -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <?php if ($flash): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($flash) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <?php endif; ?>
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h3><?= (int)$stats['score'] ?></h3>
                                <p>Total Score</p>
                            </div>
                            <div class="icon"><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-info">
                            <div class="inner">
                                <h3><?= isset($stats['metrics']['water']) ? (float)$stats['metrics']['water'] : 0 ?><sup style="font-size: 20px">L</sup></h3>
                                <p>Total Water</p>
                            </div>
                            <div class="icon"><i class="fas fa-tint"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-warning">
                            <div class="inner">
                                <h3><?= isset($stats['metrics']['burned']) ? (float)$stats['metrics']['burned'] : 0 ?></h3>
                                <p>Calories Burned</p>
                            </div>
                            <div class="icon"><i class="fas fa-fire"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3><?= isset($stats['activityCount']) ? (int)$stats['activityCount'] : 0 ?></h3>
                                <p>Activity Logs</p>
                            </div>
                            <div class="icon"><i class="fas fa-list"></i></div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Secondary cards: rank, verified months, completion, upcoming weigh-in, days remaining -->
                <div class="row">
                    <div class="col-md-2 col-sm-6">
                        <div class="info-box mb-3 bg-gradient-primary">
                            <span class="info-box-icon bg-primary"><i class="fas fa-medal"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Rank</span>
                                <span class="info-box-number"><?= $stats['rank'] !== null ? (int)$stats['rank'] : '–' ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="info-box mb-3 bg-gradient-success">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Verified</span>
                                <span class="info-box-number"><?= (int)$stats['verifiedCount'] ?>/3</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="info-box mb-3 bg-gradient-warning">
                            <span class="info-box-icon bg-warning"><i class="fas fa-percent"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Completion</span>
                                <span class="info-box-number"><?= round((float)$stats['completionPercent']) ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box mb-3 bg-gradient-info">
                            <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Until Check</span>
                                <span class="info-box-number">
                                    <?= (int)$remainingDays ?>d <?= (int)$remainingHours ?>h <?= (int)$remainingMinutes ?>m
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box mb-3 bg-gradient-secondary">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-weight"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Next Weigh‑In</span>
                                <span class="info-box-number">
                                    <?php if ($nextWeighDate): ?>
                                        <?= htmlspecialchars(date('M d, Y', strtotime($nextWeighDate))) ?>
                                    <?php else: ?>
                                        Now
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Goal and weight information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Latest Weight & BMI</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($weightInfo): ?>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr><th style="width:40%">Date</th><td><?= htmlspecialchars(date('M d, Y', strtotime($weightInfo['date']))) ?></td></tr>
                                            <tr><th>Weight</th><td><?= htmlspecialchars($weightInfo['weight']) ?> kg</td></tr>
                                            <tr><th>Height</th><td><?= htmlspecialchars($weightInfo['height']) ?> ft</td></tr>
                                            <tr><th>BMI</th><td><?= htmlspecialchars(number_format($weightInfo['bmi'], 2)) ?> (<?= htmlspecialchars($weightInfo['classification']) ?>)</td></tr>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p>No weigh‑in recorded yet. Please submit your first weekly weigh‑in.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Your Goal</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($goal)): ?>
                                    <?php
                                        $goalLabels = [
                                            'lose_5kg'         => 'Lose at least 5 kg',
                                            'gain_5kg'         => 'Gain up to 5 kg through healthy habits',
                                            'reduce_waist_4in' => 'Reduce waistline by at least 4 inches',
                                            'improve_bmi'      => 'Improve BMI or body composition to a healthier range',
                                            'other_safe_goal'  => 'Other safe and appropriate goal',
                                        ];
                                        $verificationLabels = [
                                            'official_weigh_in'                   => 'Verified through an official weigh-in.',
                                            'proper_measurement'                 => 'Verified through proper measurement procedures.',
                                            'bmi_or_body_composition_assessment' => 'Verified through official BMI/body composition assessment or approved health check.',
                                            'activity_logs_or_equivalent_proof'  => 'Proof such as logs, screenshots, or certificates may be submitted.',
                                            'manual_review'                     => 'Subject to admin review.',
                                        ];
                                    ?>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr><th style="width:40%">Type</th><td><?= htmlspecialchars($goalLabels[$goal['goal_type']] ?? $goal['goal_type']) ?></td></tr>
                                            <tr><th>Baseline</th><td><?= htmlspecialchars($goal['baseline_value']) ?></td></tr>
                                            <tr><th>Target</th><td><?= htmlspecialchars($goal['target_value']) ?></td></tr>
                                            <?php if (!empty($goal['goal_details'])): ?>
                                                <tr><th>Details</th><td><?= nl2br(htmlspecialchars($goal['goal_details'])) ?></td></tr>
                                            <?php endif; ?>
                                            <tr><th>Verification</th><td><?= htmlspecialchars($verificationLabels[$goal['verification_type']] ?? $goal['verification_type']) ?></td></tr>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p>You have not set a goal yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Weight progress chart -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Weight Progress</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($weightHistory) && count($weightHistory) >= 1): ?>
                                    <div class="chart">
                                        <canvas id="weightChart" style="min-height: 250px; height: 250px; max-height: 250px; width: 100%;"></canvas>
                                    </div>
                                <?php else: ?>
                                    <p>No weigh‑in history yet. Please record your weight to view your progress.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Recent activity table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Recent Activity</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                                <th>Water (L)</th>
                                                <th>Calories Burned</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($activities)): ?>
                                                <?php foreach ($activities as $act): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars(date('M d, Y', strtotime($act['entry_date']))) ?></td>
                                                        <td><?= htmlspecialchars($act['activity_type'] ?? '') ?></td>
                                                        <td><?= (float)$act['water_liters'] ?></td>
                                                        <td><?= (float)$act['calories_burned'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center">No recent activity entries.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Quick action buttons -->
                <div class="row">
                    <?php if (empty($goal)): ?>
                    <div class="col-md-2 col-sm-6 col-12">
                        <div class="small-box bg-light">
                            <div class="inner">
                                <p>Submit Goal</p>
                                <a href="/goal/submit" class="btn btn-primary btn-block">Submit Goal</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-2 col-sm-6 col-12">
                        <div class="small-box bg-light">
                            <div class="inner">
                                <p>Log Activity</p>
                                <a href="/activity/log" class="btn btn-primary btn-block">Log Activity</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <div class="small-box bg-light">
                            <div class="inner">
                                <p>Weekly Weigh‑In</p>
                                <a href="/weighin/submit" class="btn btn-primary btn-block">Weigh In</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <div class="small-box bg-light">
                            <div class="inner">
                                <p>Monthly Progress</p>
                                <a href="/progress/submit" class="btn btn-primary btn-block">Submit Progress</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <div class="small-box bg-light">
                            <div class="inner">
                                <p>Leaderboard</p>
                                <a href="/leaderboard/index" class="btn btn-primary btn-block">View Leaderboard</a>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($goal)): ?>
                    <div class="col-md-2 col-sm-6 col-12">
                        <div class="small-box bg-light">
                            <div class="inner">
                                <p>Your Plan</p>
                                <a href="/participant/plan" class="btn btn-primary btn-block">View Plan</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
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
            Wellness Challenge System
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<?php if (!empty($weightHistory) && count($weightHistory) >= 1): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('weightChart').getContext('2d');
        const labels = <?php echo json_encode(array_map(function($row) { return date('M d', strtotime($row['weigh_date'])); }, $weightHistory)); ?>;
        const weightData = <?php echo json_encode(array_map(function($row) { return (float)$row['weight']; }, $weightHistory)); ?>;
        const bmiData = <?php echo json_encode(array_map(function($row) { return (float)$row['bmi']; }, $weightHistory)); ?>;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Weight (kg)',
                        data: weightData,
                        borderColor: '#f472b6',
                        backgroundColor: 'rgba(244, 114, 182, 0.3)',
                        tension: 0.3,
                        yAxisID: 'y',
                    },
                    {
                        label: 'BMI',
                        data: bmiData,
                        borderColor: '#c084fc',
                        backgroundColor: 'rgba(192, 132, 252, 0.3)',
                        tension: 0.3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#374151' } },
                },
                scales: {
                    x: { ticks: { color: '#374151' }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    y: {
                        position: 'left',
                        ticks: { color: '#374151' },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                    },
                    y1: {
                        position: 'right',
                        ticks: { color: '#374151' },
                        grid: { drawOnChartArea: false, color: 'rgba(0,0,0,0.05)' },
                    },
                },
            }
        });
    });
</script>
<?php endif; ?>
</body>
</html>