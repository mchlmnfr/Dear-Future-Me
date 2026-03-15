<?php
namespace App\Models;

use App\Core\Model;

/**
 * WeighIn model.
 *
 * Stores participant weekly weigh-ins. Each record contains the participant id,
 * the date of the weigh-in, measured weight (kg), height (ft or m converted
 * into meters), calculated BMI and classification. Use this to track weight
 * changes over the course of the challenge.
 */
class WeighIn extends Model
{
    /**
     * Insert a new weigh-in record.
     *
     * @param int    $participantId
     * @param string $date Date of weigh-in (Y-m-d)
     * @param float  $weight Weight in kilograms
     * @param float  $height Height in feet (will be converted to meters)
     * @param float  $bmi Calculated BMI value
     * @param string $classification BMI classification label
     * @return int|null New record ID or null on failure
     */
    public function create(
        int $participantId,
        string $date,
        float $weight,
        float $height,
        float $bmi,
        string $classification
    ): ?int {
        $stmt = $this->db->prepare(
            'INSERT INTO weigh_ins (
                participant_id,
                weigh_date,
                weight,
                height,
                bmi,
                classification,
                created_at
            ) VALUES (
                :participant_id,
                :weigh_date,
                :weight,
                :height,
                :bmi,
                :classification,
                NOW()
            )'
        );
        $result = $stmt->execute([
            'participant_id' => $participantId,
            'weigh_date'    => $date,
            'weight'        => $weight,
            'height'        => $height,
            'bmi'           => $bmi,
            'classification'=> $classification,
        ]);
        return $result ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Get the most recent weigh-in for a participant.
     *
     * @param int $participantId
     * @return array|null
     */
    public function getLast(int $participantId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM weigh_ins WHERE participant_id = :pid ORDER BY weigh_date DESC LIMIT 1'
        );
        $stmt->execute(['pid' => $participantId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}