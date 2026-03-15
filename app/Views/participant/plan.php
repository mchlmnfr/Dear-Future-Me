<?php
// Plan page using shared participant layout.
// Expects variables: $classification (string), $suggestions (array with keys 'workouts' and 'diets')

// Set the active page slug for sidebar highlighting
$activePage = 'plan';

// Begin capturing page content
ob_start();
?>
<section class="content pt-3">
    <div class="container-fluid">
        <h2 class="mb-4">Your Wellness Plan</h2>
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h3 class="card-title mb-0">BMI Classification: <?= htmlspecialchars($classification) ?></h3>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    Based on your goal baseline, you fall under the <strong><?= htmlspecialchars($classification) ?></strong> category.
                    Below is a suggested workout and diet/fasting plan to support your wellness journey.
                </p>
            </div>
        </div>
        <div class="row g-3">
            <!-- Workouts Column -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Workout Plan</h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($suggestions['workouts'] as $workout): ?>
                            <div class="mb-4">
                                <h4 class="mb-1"><?= htmlspecialchars($workout['name']) ?></h4>
                                <p class="mb-1"><strong>Level:</strong> <?= htmlspecialchars($workout['level']) ?></p>
                                <p class="mb-1"><strong>Duration:</strong> <?= htmlspecialchars($workout['duration']) ?></p>
                                <p class="mb-1"><strong>Estimated Calories Burned:</strong> <?= htmlspecialchars($workout['calories']) ?></p>
                                <?php
                                    // Equipment may be a string or array; handle both by joining array values
                                    $equipment = $workout['equipment'];
                                    if (is_array($equipment)) {
                                        $equipmentList = implode(', ', array_map('htmlspecialchars', $equipment));
                                    } else {
                                        $equipmentList = htmlspecialchars($equipment);
                                    }
                                ?>
                                <p class="mb-1"><strong>Equipment:</strong> <?= $equipmentList ?></p>
                                <p class="mb-1"><strong>Steps:</strong></p>
                                <ol class="ps-4 mb-2">
                                    <?php foreach ($workout['steps'] as $step): ?>
                                        <li><strong><?= htmlspecialchars($step['title']) ?></strong>: <?= htmlspecialchars($step['instruction']) ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Diet/Fasting Column -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Diet &amp; Fasting Plan</h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($suggestions['diets'] as $diet): ?>
                            <div class="mb-4">
                                <h4 class="mb-1"><?= htmlspecialchars($diet['name']) ?></h4>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($diet['description'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/participant_layout.php';
