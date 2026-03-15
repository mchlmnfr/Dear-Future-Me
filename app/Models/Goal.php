<?php
namespace App\Models;

use App\Core\Model;

/**
 * Goal model. Represents a participant's wellness goal submission.
 */
class Goal extends Model
{
    /**
     * Create a goal for a participant. Each participant can only have one goal.
     *
     * @param int    $participantId
     * @param string $goalType
     * @param string $baselineValue
     * @param string $targetValue
     * @param string $details
     * @return int|null
     */
    public function create(
        int $participantId,
        string $goalType,
        string $baselineValue,
        string $targetValue,
        string $details,
        string $verificationType
    ): ?int {
        $stmt = $this->db->prepare(
            'INSERT INTO goals (
                participant_id,
                goal_type,
                verification_type,
                baseline_value,
                target_value,
                goal_details,
                submission_status,
                submitted_at,
                created_at,
                updated_at
            ) VALUES (
                :participant_id,
                :goal_type,
                :verification_type,
                :baseline_value,
                :target_value,
                :goal_details,
                :submission_status,
                NOW(),
                NOW(),
                NOW()
            )'
        );
        $result = $stmt->execute([
            'participant_id'     => $participantId,
            'goal_type'          => $goalType,
            'verification_type'  => $verificationType,
            'baseline_value'     => $baselineValue,
            'target_value'       => $targetValue,
            'goal_details'       => $details,
            'submission_status'  => 'submitted',
        ]);
        return $result ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Retrieve a goal by participant ID. Returns null if none exists.
     *
     * @param int $participantId
     * @return array|null
     */
    public function findByParticipant(int $participantId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM goals WHERE participant_id = :pid LIMIT 1');
        $stmt->execute(['pid' => $participantId]);
        $goal = $stmt->fetch();
        return $goal ?: null;
    }
}