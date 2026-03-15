<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * ActivityController handles logging of daily activities such as steps, water intake,
 * calories burned along with what meals they consumed. Participants can record their daily
 * activity to track lifestyle habits throughout the challenge.
 */
class ActivityController extends Controller
{
    /**
     * Display the activity log form and handle submissions.
     * GET requests render the form; POST requests process the form and save
     * the entry to the database.
     */
    public function log(): void
    {
        $this->requireParticipant();
        // Update theme if requested via query parameter
        $this->handleTheme();

        $participantId = (int) ($_SESSION['user_id'] ?? 0);

        $errors = [];
        $old    = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date          = $_POST['entry_date'] ?? date('Y-m-d');
            $activityType  = $_POST['activity_type'] ?? '';
            $water         = $_POST['water_liters'] ?? '0';
            $burned        = $_POST['calories_burned'] ?? '0';
            $breakfast     = trim($_POST['breakfast'] ?? '');
            $lunch         = trim($_POST['lunch'] ?? '');
            $snack         = trim($_POST['snack'] ?? '');
            $dinner        = trim($_POST['dinner'] ?? '');

            // Basic validation
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $errors[] = 'Invalid date format.';
            }
            if ($activityType === '') {
                $errors[] = 'Please select the type of activity.';
            }
            // Convert to floats
            $waterVal    = is_numeric($water) ? (float)$water : 0.0;
            $burnedVal   = is_numeric($burned) ? (float)$burned : 0.0;

            $old = [
                'entry_date'        => $date,
                'activity_type'     => $activityType,
                'water_liters'      => $water,
                'calories_burned'   => $burned,
                'breakfast'         => $breakfast,
                'lunch'             => $lunch,
                'snack'             => $snack,
                'dinner'            => $dinner,
            ];

            if (empty($errors)) {
                // Save to database
                $model = new \App\Models\ActivityEntry($this->config);
                $newId = $model->create(
                    $participantId,
                    $date,
                    $activityType,
                    $waterVal,
                    $burnedVal,
                    $breakfast,
                    $lunch,
                    $snack,
                    $dinner
                );
                if ($newId) {
                    $success = true;
                    // Reset form values after success
                    $old = [];
                } else {
                    $errors[] = 'Unable to save activity entry. Please try again.';
                }
            }
        }

        // Render the form view
        $this->render('participant/activity_form', [
            'errors'  => $errors,
            'old'     => $old,
            'success' => $success,
        ]);
    }
}