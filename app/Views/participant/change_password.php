<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container" style="max-width: 420px; margin-top: 4rem;">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center mb-3">Change Password</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="<?= htmlspecialchars('/auth/changePassword') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required />
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn w-100">Save Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>