<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProgressEntry;

/**
 * ProgressController manages monthly progress submissions by participants.
 */
class ProgressController extends Controller
{
    /**
     * Display and handle monthly progress submissions. Each participant may
     * submit one progress entry per month. Uploads an optional photo.
     */
    public function submit(): void
    {
        $this->requireParticipant();
        // Update theme if requested
        $this->handleTheme();
        $participantId = (int)$_SESSION['user_id'];
        $progressModel = new ProgressEntry($this->config);
        $errors = [];
        $success = null;
        // Determine current month number relative to challenge start. For simplicity
        // we number months 1..3 based on calendar months (Jan=1, Feb=2, ...).
        $currentMonth = (int)date('n');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $monthNumber = (int)($_POST['month_number'] ?? $currentMonth);
            $note        = trim($_POST['progress_note'] ?? '');
            $photoUrl    = null;
            // Validate month and note
            if ($monthNumber < 1 || $monthNumber > 12) {
                $errors[] = 'Invalid month.';
            }
            if ($note === '') {
                $errors[] = 'Progress note is required.';
            }
            // Check if progress already submitted for this month
            if ($progressModel->findByParticipantAndMonth($participantId, $monthNumber)) {
                $errors[] = 'You have already submitted progress for month ' . $monthNumber . '.';
            }
            // Handle photo upload
            if (!empty($_FILES['progress_photo']['name'])) {
                $uploadDir = ROOT . '/public/assets/uploads/progress/' . $participantId;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName  = date('Ymd_His') . '_' . basename($_FILES['progress_photo']['name']);
                $targetPath= $uploadDir . '/' . $fileName;
                $fileExt   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed   = ['jpg','jpeg','png'];
                if (!in_array($fileExt, $allowed, true)) {
                    $errors[] = 'Only JPG and PNG photos are allowed.';
                } else {
                    if (move_uploaded_file($_FILES['progress_photo']['tmp_name'], $targetPath)) {
                        $photoUrl = str_replace(ROOT . '/public', '', $targetPath);
                    } else {
                        $errors[] = 'Failed to upload photo.';
                    }
                }
            }
            if (empty($errors)) {
                $entryId = $progressModel->create($participantId, $monthNumber, $note, $photoUrl, 'pending');
                if ($entryId) {
                    $success = 'Progress submitted successfully and awaiting verification.';
                } else {
                    $errors[] = 'Failed to save progress entry.';
                }
            }
        }
        $this->render('participant/progress_form', [
            'errors'      => $errors,
            'success'     => $success,
            'currentMonth'=> $currentMonth,
        ]);
    }
}