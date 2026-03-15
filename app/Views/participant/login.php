<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Participant Login</title>
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet" />
</head>
<body>
<div class="page page-center">
    <div class="container-tight py-4">
        <div class="text-center mb-4">
            <a href="#" class="navbar-brand navbar-brand-autodark"><strong>Dear Future, Me!</strong></a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Participant Login</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form action="<?= htmlspecialchars('/auth/login') ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required />
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-muted mt-3">
            <!-- This login form is unified; admins and participants use the same credentials -->
        </div>
    </div>
</div>
<script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>