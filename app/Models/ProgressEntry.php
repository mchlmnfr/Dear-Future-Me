<?php
namespace App\Models;

use App\Core\Model;

/**
 * ProgressEntry model. Stores monthly progress uploads for participants. Each
 * entry may be verified by an admin and scored.
 */
class ProgressEntry extends Model
{
    /**
     * Create a new progress entry.
     *
     * @param int    $participantId
     * @param int    $monthNumber
     * @param string $note
     * @param string|null $photoUrl
     * @param string $status         (pending/verified/rejected)
     * @return int|null
     */
    public function create(int $participantId, int $monthNumber, string $note, ?string $photoUrl, string $status = 'pending'): ?int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO progress_entries (participant_id, month_number, progress_note, photo_url, status, created_at, updated_at)
             VALUES (:participant_id, :month_number, :progress_note, :photo_url, :status, NOW(), NOW())'
        );
        $result = $stmt->execute([
            'participant_id' => $participantId,
            'month_number'  => $monthNumber,
            'progress_note' => $note,
            'photo_url'     => $photoUrl,
            'status'        => $status,
        ]);
        return $result ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Find a progress entry by participant and month. Used to prevent duplicate
     * submissions for the same month.
     *
     * @param int $participantId
     * @param int $monthNumber
     * @return array|null
     */
    public function findByParticipantAndMonth(int $participantId, int $monthNumber): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM progress_entries WHERE participant_id = :pid AND month_number = :month LIMIT 1'
        );
        $stmt->execute([
            'pid'   => $participantId,
            'month' => $monthNumber,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get all progress entries for a participant.
     *
     * @param int $participantId
     * @return array
     */
    public function getByParticipant(int $participantId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM progress_entries WHERE participant_id = :pid ORDER BY month_number');
        $stmt->execute(['pid' => $participantId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all progress entries that are pending verification.
     *
     * @return array
     */
    public function getPendingVerification(): array
    {
        $stmt = $this->db->query('SELECT pe.*, p.full_name FROM progress_entries pe JOIN participants p ON pe.participant_id = p.id WHERE pe.status = \'pending\' ORDER BY pe.created_at');
        return $stmt->fetchAll();
    }

    /**
     * Update the status of a progress entry (verified/rejected) and set
     * updated_at.
     *
     * @param int    $entryId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $entryId, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE progress_entries SET status = :status, updated_at = NOW() WHERE id = :id');
        return $stmt->execute([
            'status' => $status,
            'id'     => $entryId,
        ]);
    }
}