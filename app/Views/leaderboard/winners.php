<?php
// Winners page using shared participant layout.
// Expects $winners array with keys: rank_position, full_name, prize_amount.

// Set active page. Use leaderboard so sidebar highlights Leaderboard menu when viewing winners.
$activePage = 'leaderboard';

// Begin capturing page content
ob_start();
?>
<section class="content pt-3">
    <div class="container-fluid">
        <h2 class="mb-4">Top 3 Participants</h2>
        <?php if (empty($winners)): ?>
            <p>No winners declared yet.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($winners as $winner): ?>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h1 class="card-title mb-1">#<?= htmlspecialchars($winner['rank_position']) ?></h1>
                                <p class="h4 mb-2"><?= htmlspecialchars($winner['full_name']) ?></p>
                                <p class="text-muted mb-0">Prize: Php <?= htmlspecialchars($winner['prize_amount']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="mt-3">
            <a href="/leaderboard/index" class="btn btn-secondary">Back to Leaderboard</a>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/participant_layout.php';
