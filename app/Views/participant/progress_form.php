<?php
// Monthly progress submission page using shared participant layout
$activePage = 'progress';
$theme = $_SESSION['theme'] ?? 'dark';

ob_start();
?>
<section class="content pt-3">
    <div class="container-fluid">
        <h2 class="mb-4">Submit Your Monthly Progress</h2>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
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
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= htmlspecialchars('/progress/submit') ?>" method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Month Number</label>
                            <input type="number" name="month_number" class="form-control" min="1" max="12" value="<?= htmlspecialchars($currentMonth) ?>" required />
                            <small class="form-text">Enter the month number (1–12) of your progress entry.</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Progress Note</label>
                            <textarea name="progress_note" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Photo (optional)</label>
                            <input type="file" name="progress_photo" class="form-control" accept="image/*" />
                            <small class="form-text">Upload a photo to support your progress (optional).</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Submit Progress</button>
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