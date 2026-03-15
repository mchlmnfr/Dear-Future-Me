<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Goal;

/**
 * GoalController handles the participant wellness goal submission.
 */
class GoalController extends Controller
{
    /**
     * Show the goal submission form and handle submission. Participants can
     * submit one goal per challenge.
     */
    public function submit(): void
    {
        $this->requireParticipant();
        // Update theme if provided in query
        $this->handleTheme();
        $participantId = (int)$_SESSION['user_id'];
        $goalModel = new Goal($this->config);
        $existingGoal = $goalModel->findByParticipant($participantId);
        $errors = [];
        $success = null;
        // If already submitted, show message
        if ($existingGoal) {
            $success = 'You have already submitted your goal.';
            $this->render('participant/goal_form', [
                'existingGoal' => $existingGoal,
                'success'      => $success,
            ]);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $goalType      = trim($_POST['goal_type'] ?? '');
            $baseline      = trim($_POST['baseline_value'] ?? '');
            $target        = trim($_POST['target_value'] ?? '');
            $details       = trim($_POST['goal_details'] ?? '');
            // Validate input
            if ($goalType === '') {
                $errors[] = 'Please select a goal type.';
            }
            if ($baseline === '' || $target === '') {
                $errors[] = 'Baseline and target values are required.';
            }

            // Map goal type to verification type. This determines what proof will be required.
            $verificationMap = [
                'lose_5kg'                        => 'official_weigh_in',
                'gain_5kg'                        => 'official_weigh_in',
                'reduce_waist_4in'                => 'proper_measurement',
                'improve_bmi'                     => 'bmi_or_body_composition_assessment',
                'other_safe_goal'                 => 'activity_logs_or_equivalent_proof',
            ];
            $verificationType = $verificationMap[$goalType] ?? 'manual_review';

            if (empty($errors)) {
                $goalId = $goalModel->create($participantId, $goalType, $baseline, $target, $details, $verificationType);
                if ($goalId) {
                    // Redirect to dashboard after successful goal submission
                    $_SESSION['flash_success'] = 'Goal submitted successfully!';
                    header('Location: /participant/dashboard');
                    exit;
                } else {
                    $errors[] = 'Failed to submit goal.';
                }
            }
        }
        $this->render('participant/goal_form', [
            'errors'  => $errors,
            'success' => $success,
        ]);
    }
}