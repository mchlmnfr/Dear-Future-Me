<?php
namespace App\Models;

use App\Core\Model;

/**
 * StepEntry model. Represents daily step count submissions by participants.
 * Each entry contains a screenshot (stored in Supabase Storage or local
 * filesystem) and both the extracted and user‑confirmed step counts.
 */
class StepEntry extends Model
{
    /**
     * Create a new step entry.
     *
     * @param int       $participantId
     * @param string    $entryDate       Date in YYYY‑MM‑DD format
     * @param string    $screenshotUrl
     * @param int|null  $extractedSteps
     * @param int|null  $confirmedSteps
     * @param string    $extractionStatus
     * @param string    $reviewStatus
     * @return int|null
     */
    public function create(
        int $participantId,
        string $entryDate,
        string $screenshotUrl,
        ?int $extractedSteps,
        ?int $confirmedSteps,
        string $extractionStatus = 'pending',
        string $reviewStatus = 'pending'
    ): ?int {
        $stmt = $this->db->prepare(
            'INSERT INTO step_entries (participant_id, entry_date, screenshot_url, extracted_step_count, confirmed_step_count, extraction_status, review_status, created_at, updated_at)
             VALUES (:participant_id, :entry_date, :screenshot_url, :extracted_step_count, :confirmed_step_count, :extraction_status, :review_status, NOW(), NOW())'
        );
        $result = $stmt->execute([
            'participant_id'       => $participantId,
            'entry_date'          => $entryDate,
            'screenshot_url'      => $screenshotUrl,
            'extracted_step_count'=> $extractedSteps,
            'confirmed_step_count'=> $confirmedSteps,
            'extraction_status'   => $extractionStatus,
            'review_status'       => $reviewStatus,
        ]);
        return $result ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Find a step entry by participant and date. Used to enforce one entry per day.
     *
     * @param int    $participantId
     * @param string $entryDate
     * @return array|null
     */
    public function findByParticipantAndDate(int $participantId, string $entryDate): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM step_entries WHERE participant_id = :pid AND entry_date = :date LIMIT 1');
        $stmt->execute([
            'pid'  => $participantId,
            'date' => $entryDate,
        ]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Retrieve all step entries for a participant. Optionally filter by month.
     *
     * @param int         $participantId
     * @param string|null $month         Format YYYY‑MM (optional)
     * @return array
     */
    public function getEntriesByParticipant(int $participantId, ?string $month = null): array
    {
        if ($month) {
            $stmt = $this->db->prepare(
                'SELECT * FROM step_entries WHERE participant_id = :pid AND to_char(entry_date,\'YYYY-MM\') = :month ORDER BY entry_date'
            );
            $stmt->execute([
                'pid'   => $participantId,
                'month' => $month,
            ]);
        } else {
            $stmt = $this->db->prepare('SELECT * FROM step_entries WHERE participant_id = :pid ORDER BY entry_date');
            $stmt->execute(['pid' => $participantId]);
        }
        return $stmt->fetchAll();
    }
}