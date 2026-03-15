<?php
$theme = $_SESSION['theme'] ?? 'light';
?>
<!doctype html>
<html lang="en" data-bs-theme="<?= htmlspecialchars($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Declare Winners</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin/dashboard">Admin Panel</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <?php if ($theme === 'dark'): ?>
                    <a class="nav-link" href="?theme=light">Light Mode</a>
                <?php else: ?>
                    <a class="nav-link" href="?theme=dark">Dark Mode</a>
                <?php endif; ?>
            </li>
            <li class="nav-item"><a class="nav-link" href="/admin/logout">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="sidebar border-end">
    <ul class="nav flex-column pt-3">
        <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/verify_progress_list">Verify Progress</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/create_participant">Create Participant</a></li>
        <li class="nav-item"><a class="nav-link active" href="/admin/declare_winners">Declare Winners</a></li>
    </ul>
</div>

<main class="container-fluid">
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12 mt-3 d-flex justify-content-between align-items-center">
                <h2>Declare Winners</h2>
                <div>
                    <a href="/admin/dashboard" class="btn btn-secondary me-2">Back to Dashboard</a>
                    <a href="/leaderboard/index" class="btn">View Leaderboard</a>
                </div>
            </div>
        </div>
        <div class="row mt-3 g-3">
            <div class="col-md-8">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <p>Unable to declare winners. Please try again later.</p>
            </div>
        </div>
    </div>
</main>
</body>
</html>