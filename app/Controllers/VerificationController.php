<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProgressEntry;
use App\Models\Verification;

/**
 * VerificationController allows admins to review and verify participant
 * progress submissions.
 */
class VerificationController extends Controller
{
    /**
     * Show a list of pending progress entries for verification. Only admins
     * can access this page.
     */
    public function index(): void
    {
        $this->requireAdmin();
        $progressModel = new ProgressEntry($this->config);
        $pending = $progressModel->getPendingVerification();
        $this->render('admin/verify_progress_list', [
            'pendingEntries' => $pending,
        ]);
    }

    /**
     * Review a specific progress entry and process verification. Admins can
     * approve or reject and assign a score.
     *
     * @param int $id Progress entry ID
     */
    public function review(int $id): void
    {
        $this->requireAdmin();
        $progressModel = new ProgressEntry($this->config);
        $verificationModel = new Verification($this->config);
        // Fetch entry
        $stmt = $progressModel->db->prepare('SELECT pe.*, p.full_name FROM progress_entries pe JOIN participants p ON pe.participant_id = p.id WHERE pe.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $entry = $stmt->fetch();
        if (!$entry) {
            $this->redirect('verification/index');
            return;
        }
        $errors = [];
        $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';
            $score  = (int)($_POST['score'] ?? 0);
            $remarks= trim($_POST['remarks'] ?? '');
            if (!in_array($status, ['approved','rejected'], true)) {
                $errors[] = 'Invalid status.';
            }
            if ($status === 'approved' && $score <= 0) {
                $errors[] = 'Score must be a positive number.';
            }
            if (empty($errors)) {
                // Update progress entry status
                $progressModel->updateStatus($entry['id'], $status);
                // Create verification record
                $verificationModel->create(
                    (int)$entry['participant_id'],
                    (int)$entry['id'],
                    (int)$_SESSION['user_id'],
                    $status,
                    $score,
                    $remarks
                );
                $success = 'Verification saved successfully.';
                // Refresh entry status for display
                $entry['status'] = $status;
            }
        }
        $this->render('admin/verify_progress', [
            'entry'   => $entry,
            'errors'  => $errors,
            'success' => $success,
        ]);
    }
}