<?php
// Activity log page using shared participant layout
// Set the active page slug for sidebar highlighting
$activePage = 'activity';

// Default theme is dark unless specified in session
$theme = $_SESSION['theme'] ?? 'dark';

// List of available activity types. This should mirror the specification.
$activityOptions = [
    'running'    => 'Running / Biking (fast pace)',
    'swimming'   => 'Swimming laps',
    'yardwork'   => 'Heavy yard work (digging/shoveling)',
    'sports'     => 'Playing basketball or tennis',
    'jumping'    => 'Jumping rope',
    'weights'    => 'Lifting weights',
    'bands'      => 'Working with resistance bands',
    'bodyweight' => 'Push‑ups / Sit‑ups',
    'gardening'  => 'Heavy yard work or gardening',
    'yoga'       => 'Yoga / Pilates / Zumba',
];

// Begin capturing page content
ob_start();
?>
<section class="content pt-3">
    <div class="container-fluid">
        <h2 class="mb-4">Log Daily Activity</h2>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">Activity entry saved successfully.</div>
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
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="entry_date" class="form-control" value="<?= htmlspecialchars($old['entry_date'] ?? date('Y-m-d')) ?>" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Activity Type</label>
                            <select name="activity_type" class="form-select" required>
                                <option value="">Select activity</option>
                                <?php foreach ($activityOptions as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value) ?>" <?= (isset($old['activity_type']) && $old['activity_type'] === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Choose the activity you performed today.</small>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Water (liters)</label>
                            <input type="number" step="0.1" min="0" name="water_liters" class="form-control" placeholder="0" value="<?= htmlspecialchars($old['water_liters'] ?? '') ?>" />
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Calories Burned</label>
                            <input type="number" step="1" min="0" name="calories_burned" class="form-control" placeholder="0" value="<?= htmlspecialchars($old['calories_burned'] ?? '') ?>" />
                        </div>
                    </div>
                    <hr class="my-4" />
                    <h5 class="mb-2">Meals Consumed</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Breakfast</label>
                            <input type="text" name="breakfast" class="form-control" placeholder="e.g. Oatmeal and fruit" value="<?= htmlspecialchars($old['breakfast'] ?? '') ?>" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Lunch</label>
                            <input type="text" name="lunch" class="form-control" placeholder="e.g. Salad with chicken" value="<?= htmlspecialchars($old['lunch'] ?? '') ?>" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Snack</label>
                            <input type="text" name="snack" class="form-control" placeholder="e.g. Nuts, fruit" value="<?= htmlspecialchars($old['snack'] ?? '') ?>" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dinner</label>
                            <input type="text" name="dinner" class="form-control" placeholder="e.g. Grilled fish and veggies" value="<?= htmlspecialchars($old['dinner'] ?? '') ?>" />
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Save Activity</button>
                        <a href="/participant/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php
// Capture buffered content and render through the shared participant layout
$content = ob_get_clean();
include __DIR__ . '/../layouts/participant_layout.php';
?>