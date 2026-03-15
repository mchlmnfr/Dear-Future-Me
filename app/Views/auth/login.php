<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container" style="max-width: 420px; margin-top: 4rem;">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center mb-3">Sign In</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form action="<?= htmlspecialchars('/auth/login') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Login ID</label>
                    <input type="text" name="login_id" class="form-control" required />
                    <small class="form-text">Admins: use your email. Participants: use your employee ID.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required />
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn w-100">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>