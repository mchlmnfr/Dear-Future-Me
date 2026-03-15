<?php
// Goal submission view using the dark pastel dashboard theme.
// Participants can submit their wellness goals with auto-calculated BMI
// and recommended goal type. Validation messages and success messages
// appear at the top of the form.

// Determine flash success or error messages
$success = $success ?? null;
$errors  = $errors  ?? [];
$old     = $old     ?? [];
$goalTypes = [
    'lose_5kg'         => 'Lose at least 5 kg',
    'gain_5kg'         => 'Gain up to 5 kg through healthy habits',
    'reduce_waist_4in' => 'Reduce waistline by at least 4 inches',
    'improve_bmi'      => 'Improve BMI or body composition to a healthier range',
    'other_safe_goal'  => 'Other safe and appropriate goal',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Goal</title>
    <link rel="stylesheet" href="/assets/css/dash.css">
    <script>
    // Simple BMI calculation on the client side
    function calculateBMI() {
        const weight = parseFloat(document.getElementById('weight').value);
        const heightFt = parseFloat(document.getElementById('height').value);
        if (!weight || !heightFt) return;
        const heightM = heightFt * 0.3048;
        const bmi = weight / (heightM * heightM);
        document.getElementById('bmi').value = bmi.toFixed(2);
    }
    </script>
</head>
<body>
<nav class="sidebar">
    <a href="/participant/dashboard" class="nav-link">Dashboard</a>
    <a href="/activity/log" class="nav-link">Log Activity</a>
    <a href="/weighin/submit" class="nav-link">Weekly Weigh-In</a>
    <a href="/progress/submit" class="nav-link">Monthly Progress</a>
    <a href="/leaderboard/index" class="nav-link">Leaderboard</a>
    <a href="/participant/plan" class="nav-link">Your Plan</a>
    <a href="/auth/logout" class="nav-link">Logout</a>
</nav>
<main class="content">
    <h2>Submit Your Wellness Goal</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="/goal/submit">
        <div class="grid grid-two">
            <div class="card-lg">
                <label for="weight">Weight (kg)</label><br>
                <input type="number" step="0.1" id="weight" name="weight" value="<?= htmlspecialchars($old['weight'] ?? '') ?>" oninput="calculateBMI()" required style="width:100%; padding:0.5rem; margin-bottom:1rem; border-radius:0.5rem; border: none; background-color:#2c2c31; color:#e6e6e6;">
                <label for="height">Height (ft)</label><br>
                <input type="number" step="0.1" id="height" name="height" value="<?= htmlspecialchars($old['height'] ?? '') ?>" oninput="calculateBMI()" required style="width:100%; padding:0.5rem; margin-bottom:1rem; border-radius:0.5rem; border: none; background-color:#2c2c31; color:#e6e6e6;">
                <label for="bmi">BMI</label><br>
                <input type="text" id="bmi" name="bmi" value="<?= htmlspecialchars($old['bmi'] ?? '') ?>" readonly style="width:100%; padding:0.5rem; margin-bottom:1rem; border-radius:0.5rem; border: none; background-color:#2c2c31; color:#e6e6e6;">
                <label for="goal_type">Goal Type</label><br>
                <select id="goal_type" name="goal_type" style="width:100%; padding:0.5rem; margin-bottom:1rem; border-radius:0.5rem; border: none; background-color:#2c2c31; color:#e6e6e6;">
                    <?php foreach ($goalTypes as $value => $label): ?>
                        <option value="<?= $value ?>" <?php if (($old['goal_type'] ?? '') === $value) echo 'selected'; ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="baseline_value">Baseline Details</label><br>
                <textarea id="baseline_value" name="baseline_value" rows="3" style="width:100%; padding:0.5rem; border-radius:0.5rem; border: none; background-color:#2c2c31; color:#e6e6e6;"><?= htmlspecialchars($old['baseline_value'] ?? '') ?></textarea>
                <label for="target_value" style="margin-top:1rem;">Target Value</label><br>
                <input type="text" id="target_value" name="target_value" value="<?= htmlspecialchars($old['target_value'] ?? '') ?>" style="width:100%; padding:0.5rem; margin-bottom:1rem; border-radius:0.5rem; border: none; background-color:#2c2c31; color:#e6e6e6;">
                <label for="goal_details">Goal Details (optional)</label><br>
                <textarea id="goal_details" name="goal_details" rows="3" style="width:100%; padding:0.5rem; border-radius:0.5rem; border: none; background-color:#2c2c31; color:#e6e6e6;"><?= htmlspecialchars($old['goal_details'] ?? '') ?></textarea>
            </div>
            <div class="card-lg">
                <p>After submitting your goal, you will be redirected to the dashboard.</p>
                <p>Your BMI and baseline details will be saved automatically.</p>
                <button type="submit" class="btn" style="margin-top:1rem;">Submit Goal</button>
            </div>
        </div>
    </form>
</main>
</body>
</html>