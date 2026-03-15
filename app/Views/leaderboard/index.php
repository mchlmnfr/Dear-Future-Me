<?php
// Leaderboard page using shared participant layout.
// Expects $ranking array with keys: full_name, total_score, total_burned.

// Set active page for sidebar highlighting
$activePage = 'leaderboard';

// Begin capturing page content
ob_start();
?>
<section class="content pt-3">
    <div class="container-fluid">
        <h2 class="mb-4">Challenge Leaderboard</h2>
        <?php if (empty($ranking)): ?>
            <p>No participants have scores yet.</p>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Total Score</th>
                                <th>Calories Burned</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $r = 1; ?>
                            <?php foreach ($ranking as $row): ?>
                                <tr>
                                    <td><?= $r ?></td>
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td><?= htmlspecialchars($row['total_score']) ?></td>
                                    <td><?= isset($row['total_burned']) ? htmlspecialchars($row['total_burned']) : '' ?></td>
                                </tr>
                                <?php $r++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <a href="/winner/index" class="btn btn-success">View Winners</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
// Render through the shared participant layout. The layout lives one directory up in layouts.
include __DIR__ . '/../layouts/participant_layout.php';
