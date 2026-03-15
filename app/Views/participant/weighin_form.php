<?php
// Weekly weigh‑in page using shared participant layout
$activePage = 'weighin';
$theme = $_SESSION['theme'] ?? 'dark';

ob_start();
?>
<section class="content pt-3">
    <div class="container-fluid">
        <h2 class="mb-4">Weekly Weigh‑In</h2>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">Weigh‑in recorded successfully.</div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($lastWeighIn)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Last Weigh‑In</h3>
                </div>
                <div class="card-body">
                    <p><strong>Date:</strong> <?= htmlspecialchars(date('M d, Y', strtotime($lastWeighIn['weigh_date']))) ?></p>
                    <p><strong>Weight:</strong> <?= htmlspecialchars($lastWeighIn['weight']) ?> kg</p>
                    <p><strong>Height:</strong> <?= htmlspecialchars($lastWeighIn['height']) ?> ft</p>
                    <p><strong>BMI:</strong> <?= htmlspecialchars(number_format($lastWeighIn['bmi'], 2)) ?> (<?= htmlspecialchars($lastWeighIn['classification']) ?>)</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title mb-0">Record a New Weigh‑In</h3>
            </div>
            <div class="card-body">
                <?php if (!$canWeigh): ?>
                    <div class="alert alert-info">Your last weigh‑in was recorded less than 7 days ago. You can update your weight once per week.</div>
                <?php endif; ?>
                <form method="post" action="" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" name="weigh_date" class="form-control" value="<?= htmlspecialchars($old['weigh_date'] ?? date('Y-m-d')) ?>" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.1" min="0" name="weight" class="form-control" placeholder="Weight in kilograms" value="<?= htmlspecialchars($old['weight'] ?? '') ?>" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Height (ft)</label>
                            <input type="number" step="0.01" min="0" name="height" class="form-control" placeholder="Height in feet" value="<?= htmlspecialchars($old['height'] ?? '') ?>" required />
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save Weigh‑In</button>
                        <a href="/participant/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/participant_layout.php';
?>