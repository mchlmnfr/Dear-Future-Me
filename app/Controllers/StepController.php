<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\StepEntry;

/**
 * Handles daily step challenge submissions. Participants upload a screenshot
 * of their step counter and optionally confirm the extracted step count.
 */
class StepController extends Controller
{
    /**
     * Display the step upload form and handle submissions. A participant
     * uploads a screenshot, the system attempts to extract the number of
     * steps and the participant confirms or edits the value before saving.
     */
    public function upload(): void
    {
        $this->requireParticipant();
        $participantId = (int)$_SESSION['user_id'];
        $stepModel     = new StepEntry($this->config);
        $message       = null;
        $error         = null;
        $extracted     = null;
        $confirmed     = null;
        $date          = date('Y-m-d');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Determine if this is the initial upload or the confirmation.
            if (isset($_POST['confirmed_steps'])) {
                // Final submission: save entry.
                $confirmed = (int)$_POST['confirmed_steps'];
                $filePath  = $_POST['screenshot_path'] ?? '';
                $extracted = $_POST['extracted_steps'] !== '' ? (int)$_POST['extracted_steps'] : null;
                $entryDate = $_POST['entry_date'] ?? $date;
                // Validate fields
                if (!$filePath || !file_exists($filePath)) {
                    $error = 'Screenshot file not found.';
                } elseif ($confirmed <= 0) {
                    $error = 'Confirmed steps must be a positive integer.';
                } else {
                    // Ensure there is no existing entry for this date.
                    if ($stepModel->findByParticipantAndDate($participantId, $entryDate)) {
                        $error = 'You have already submitted a step entry for ' . htmlspecialchars($entryDate) . '.';
                    } else {
                        // Save the entry.
                        $relPath = $filePath;
                        $entryId = $stepModel->create(
                            $participantId,
                            $entryDate,
                            $relPath,
                            $extracted,
                            $confirmed,
                            $extracted === null ? 'failed' : 'success',
                            'pending'
                        );
                        if ($entryId) {
                            $message = 'Step entry saved successfully!';
                        } else {
                            $error = 'Failed to save step entry.';
                        }
                    }
                }
            } elseif (!empty($_FILES['screenshot']['name'])) {
                // First step: handle file upload and attempt extraction.
                $uploadDir = ROOT . '/public/assets/uploads/steps/' . $participantId;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = date('Ymd_His') . '_' . basename($_FILES['screenshot']['name']);
                $targetPath = $uploadDir . '/' . $fileName;
                $tmpPath    = $_FILES['screenshot']['tmp_name'];
                $fileType   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                // Validate file type (allow jpg, jpeg, png)
                $allowedTypes = ['jpg', 'jpeg', 'png'];
                if (!in_array($fileType, $allowedTypes, true)) {
                    $error = 'Only JPG and PNG images are allowed.';
                } else {
                    if (move_uploaded_file($tmpPath, $targetPath)) {
                        // Attempt to extract steps using the detection service. This
                        // integrates with Google Vision if a key is set and
                        // falls back to filename extraction if disabled or failing.
                        $detector  = new \App\Services\StepDetectionService($this->config);
                        $extracted = $detector->extractSteps($targetPath);
                        // If detection service fails and no number is found, fallback to the naive method
                        if ($extracted === null) {
                            $extracted = $this->extractSteps($targetPath);
                        }
                        // Provide relative path for later saving
                        $relPath  = str_replace(ROOT . '/public', '', $targetPath);
                        // Show confirmation form
                        $this->render('participant/step_confirm', [
                            'screenshotPath' => $relPath,
                            'extracted'      => $extracted,
                            'entryDate'      => $date,
                        ]);
                        return;
                    } else {
                        $error = 'Failed to upload file.';
                    }
                }
            }
        }
        // Default: show upload form
        $this->render('participant/step_upload', [
            'message'   => $message,
            'error'     => $error,
        ]);
    }

    /**
     * Simple stub for extracting steps from an image. In a production system
     * you would integrate an OCR library (e.g. Tesseract) or a machine
     * learning model. This stub attempts to find the largest numeric string
     * in the filename (as a naive placeholder).
     *
     * @param string $filePath Absolute path to the image file
     * @return int|null
     */
    private function extractSteps(string $filePath): ?int
    {
        // As a basic approach, attempt to match digits in the file name.
        $fileName = basename($filePath);
        if (preg_match_all('/(\d{3,})/', $fileName, $matches)) {
            // Return the largest number found
            $numbers = array_map('intval', $matches[1]);
            return max($numbers);
        }
        // Fallback: return null to indicate extraction failed.
        return null;
    }
}