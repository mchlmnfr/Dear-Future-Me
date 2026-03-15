<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Step Screenshot</title>
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet" />
</head>
<body>
<div class="page page-center">
    <div class="container-tight py-4">
        <div class="card card-md">
            <div class="card-body">
                <h2 class="card-title text-center mb-3">Daily Step Submission</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form action="<?= htmlspecialchars('/step/upload') ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Step Screenshot</label>
                        <input type="file" name="screenshot" class="form-control" accept="image/*" required />
                        <small class="form-hint">Upload a screenshot of your step counter.</small>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Upload</button>
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