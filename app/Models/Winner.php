<?php
namespace App\Models;

use App\Core\Model;

/**
 * Winner model. Stores the final winners declared for a challenge.
 */
class Winner extends Model
{
    /**
     * Declare winners for a challenge. Inserts records for each winner.
     *
     * @param int   $challengeId
     * @param array $winners Array of arrays with keys: participant_id, rank_position, prize_amount
     * @param int   $declaredBy Admin ID
     * @return bool
     */
    public function declareWinners(int $challengeId, array $winners, int $declaredBy): bool
    {
        $this->db->beginTransaction();
        try {
            foreach ($winners as $winner) {
                $stmt = $this->db->prepare(
                    'INSERT INTO winners (challenge_id, participant_id, rank_position, prize_amount, declared_by, declared_at, created_at, updated_at)
                     VALUES (:challenge_id, :participant_id, :rank_position, :prize_amount, :declared_by, NOW(), NOW(), NOW())'
                );
                $stmt->execute([
                    'challenge_id'  => $challengeId,
                    'participant_id'=> $winner['participant_id'],
                    'rank_position' => $winner['rank_position'],
                    'prize_amount'  => $winner['prize_amount'],
                    'declared_by'   => $declaredBy,
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get winners for a challenge.
     *
     * @param int $challengeId
     * @return array
     */
    public function getByChallenge(int $challengeId): array
    {
        $stmt = $this->db->prepare(
            'SELECT w.*, p.full_name FROM winners w JOIN participants p ON w.participant_id = p.id WHERE w.challenge_id = :cid ORDER BY rank_position'
        );
        $stmt->execute(['cid' => $challengeId]);
        return $stmt->fetchAll();
    }
}