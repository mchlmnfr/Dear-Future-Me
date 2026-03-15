<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * WeighInController handles weekly weigh-ins. Participants record their current
 * weight and height each week to track changes over time and to compute
 * updated BMI and classification.
 */
class WeighInController extends Controller
{
    /**
     * Display the weigh-in form and handle submissions. Participants should
     * ideally weigh in once per week. If the last weigh-in was within the
     * previous 7 days, they will be informed but may still proceed.
     */
    public function submit(): void
    {
        $this->requireParticipant();
        // Update theme if requested
        $this->handleTheme();

        $participantId = (int) ($_SESSION['user_id'] ?? 0);
        $errors  = [];
        $success = false;
        $old     = [];

        // Load last weigh-in to enforce weekly schedule and show previous values
        $model = new \App\Models\WeighIn($this->config);
        $lastWeighIn = $model->getLast($participantId);
        $lastDate    = $lastWeighIn['weigh_date'] ?? null;
        $canWeigh    = true;
        if ($lastDate) {
            $lastTime = strtotime($lastDate);
            $diff     = (time() - $lastTime) / 86400;
            $canWeigh = $diff >= 7;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date   = $_POST['weigh_date'] ?? date('Y-m-d');
            $weight = $_POST['weight'] ?? '';
            $height = $_POST['height'] ?? '';

            // Validate date
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors[] = 'Invalid date format.';
            }
            // Validate weight and height
            $weightVal = is_numeric($weight) ? (float)$weight : null;
            $heightVal = is_numeric($height) ? (float)$height : null;
            if ($weightVal === null || $weightVal <= 0) {
                $errors[] = 'Weight must be a positive number.';
            }
            if ($heightVal === null || $heightVal <= 0) {
                $errors[] = 'Height must be a positive number.';
            }
            $old = [
                'weigh_date' => $date,
                'weight'     => $weight,
                'height'     => $height,
            ];
            if (empty($errors)) {
                // Convert height from feet to meters (1 ft = 0.3048 m). The form accepts feet.
                $heightMeters = $heightVal * 0.3048;
                $bmi = $weightVal / ($heightMeters * $heightMeters);
                $classification = 'Normal';
                if ($bmi < 18.5) {
                    $classification = 'Underweight';
                } elseif ($bmi < 25) {
                    $classification = 'Normal';
                } elseif ($bmi < 30) {
                    $classification = 'Overweight';
                } else {
                    $classification = 'Obese';
                }
                $saved = $model->create(
                    $participantId,
                    $date,
                    $weightVal,
                    $heightVal,
                    $bmi,
                    $classification
                );
                if ($saved) {
                    $success = true;
                    $old = [];
                    // Update $lastWeighIn to new record
                    $lastWeighIn = $model->getLast($participantId);
                    $canWeigh = false; // Just weighed in
                } else {
                    $errors[] = 'Failed to save weigh-in. Please try again.';
                }
            }
        }

        $this->render('participant/weighin_form', [
            'lastWeighIn' => $lastWeighIn,
            'canWeigh'    => $canWeigh,
            'errors'      => $errors,
            'success'     => $success,
            'old'         => $old,
        ]);
    }
}