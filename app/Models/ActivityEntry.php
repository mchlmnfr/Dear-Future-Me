<?php
namespace App\Models;

use App\Core\Model;

/**
 * ActivityEntry model. Stores daily activity logs such as steps, water intake,
 * calories burned during workouts and calories consumed from food. Used for
 * participant daily tracking beyond the main challenge.
 */
class ActivityEntry extends Model
{
    /**
     * Insert a new activity entry.
     *
     * When participants log their daily activity they can specify the type of activity
     * (e.g. running, biking, swimming), record the number of steps they took, the water
     * they drank and estimated calories burned during exercise.  They also log their
     * meals (breakfast, lunch, snack and dinner) instead of a single calories consumed
     * field.  All values are stored so that administrators can see a complete picture
     * of the participant’s habits and the dashboard can summarise totals.
     *
     * @param int    $participantId Participant ID
     * @param string $date           Date of the activity (Y-m-d)
     * @param string $activityType   Description of the activity type (running, biking, etc.)
     * @param float  $waterLiters    Liters of water consumed
     * @param float  $caloriesBurned Estimated calories burned during the activity
     * @param string $breakfast      Description of breakfast consumed
     * @param string $lunch          Description of lunch consumed
     * @param string $snack          Description of snack consumed
     * @param string $dinner         Description of dinner consumed
     * @return int|null The newly inserted record ID or null on failure
     */
    public function create(
        int $participantId,
        string $date,
        string $activityType,
        float $waterLiters,
        float $caloriesBurned,
        string $breakfast,
        string $lunch,
        string $snack,
        string $dinner
    ): ?int {
        $stmt = $this->db->prepare(
            'INSERT INTO activity_entries (
                participant_id,
                entry_date,
                activity_type,
                water_liters,
                calories_burned,
                breakfast,
                lunch,
                snack,
                dinner,
                created_at
            ) VALUES (
                :participant_id,
                :entry_date,
                :activity_type,
                :water_liters,
                :calories_burned,
                :breakfast,
                :lunch,
                :snack,
                :dinner,
                NOW()
            )'
        );
        $result = $stmt->execute([
            'participant_id'  => $participantId,
            'entry_date'      => $date,
            'activity_type'   => $activityType,
            'water_liters'    => $waterLiters,
            'calories_burned' => $caloriesBurned,
            'breakfast'       => $breakfast,
            'lunch'           => $lunch,
            'snack'           => $snack,
            'dinner'          => $dinner,
        ]);
        return $result ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Aggregate activity metrics for a participant.
     * Returns total steps, water and calories burned.  Meal entries are not
     * aggregated numerically because they are free‑text descriptions.  This
     * method is used by the dashboard to display summary metrics for each
     * participant.
     *
     * @param int $participantId
     * @return array [steps, water, burned]
     */
    public function sumMetrics(int $participantId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                COALESCE(SUM(water_liters), 0)  AS total_water,
                COALESCE(SUM(calories_burned), 0) AS total_burned
            FROM activity_entries
            WHERE participant_id = :pid'
        );
        $stmt->execute(['pid' => $participantId]);
        $row = $stmt->fetch();
        return [
            'water'  => (float)($row['total_water'] ?? 0),
            'burned' => (float)($row['total_burned'] ?? 0),
        ];
    }
}