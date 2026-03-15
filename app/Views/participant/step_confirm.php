<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Steps</title>
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet" />
</head>
<body>
<div class="page page-center">
    <div class="container-tight py-4">
        <div class="card card-md">
            <div class="card-body">
                <h2 class="card-title text-center mb-3">Confirm Step Count</h2>
                <p>Please review your uploaded screenshot and confirm your step count for <?= htmlspecialchars($entryDate) ?>.</p>
                <div class="mb-3 text-center">
                    <img src="<?= htmlspecialchars($screenshotPath) ?>" alt="Screenshot" style="max-width:100%; height:auto;" />
                </div>
                <form action="<?= htmlspecialchars('/step/upload') ?>" method="post">
                    <input type="hidden" name="screenshot_path" value="<?= htmlspecialchars($screenshotPath) ?>" />
                    <input type="hidden" name="extracted_steps" value="<?= htmlspecialchars($extracted ?? '') ?>" />
                    <input type="hidden" name="entry_date" value="<?= htmlspecialchars($entryDate) ?>" />
                    <div class="mb-3">
                        <label class="form-label">Detected Steps</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($extracted ?? 'N/A') ?>" disabled />
                        <small class="form-hint">This is the step count detected by the system. You can adjust below if necessary.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Steps</label>
                        <input type="number" name="confirmed_steps" class="form-control" value="<?= htmlspecialchars($extracted ?? '') ?>" required />
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Save Entry</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-muted mt-3">
            <a href="/participant/dashboard">Back to Dashboard</a>
        </div>
    </div>
</div>
<script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>