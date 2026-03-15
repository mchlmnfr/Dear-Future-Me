<?php
namespace App\Models;

use App\Core\Model;

/**
 * Verification model. Stores admin verification results for progress entries.
 */
class Verification extends Model
{
    /**
     * Create a verification record.
     *
     * @param int    $participantId
     * @param int    $progressEntryId
     * @param int    $verifiedBy  Admin ID
     * @param string $status      approved/rejected
     * @param int    $score       Points awarded
     * @param string|null $remarks
     * @return int|null
     */
    public function create(int $participantId, int $progressEntryId, int $verifiedBy, string $status, int $score, ?string $remarks): ?int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO verifications (participant_id, progress_entry_id, verified_by, verification_status, score, remarks, verified_at, created_at, updated_at)
             VALUES (:participant_id, :progress_entry_id, :verified_by, :verification_status, :score, :remarks, NOW(), NOW(), NOW())'
        );
        $result = $stmt->execute([
            'participant_id'     => $participantId,
            'progress_entry_id'  => $progressEntryId,
            'verified_by'        => $verifiedBy,
            'verification_status'=> $status,
            'score'              => $score,
            'remarks'            => $remarks,
        ]);
        return $result ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Sum scores per participant. Returns an associative array keyed by
     * participant_id.
     *
     * @return array<int,int>
     */
    public function sumScoresByParticipant(): array
    {
        $stmt = $this->db->query('SELECT participant_id, SUM(score) AS total_score FROM verifications WHERE verification_status = \'approved\' GROUP BY participant_id');
        $rows = $stmt->fetchAll();
        $scores = [];
        foreach ($rows as $row) {
            $scores[(int)$row['participant_id']] = (int)$row['total_score'];
        }
        return $scores;
    }
}