<?php
namespace App\Services;

use App\Core\Model;
use App\Models\Participant;
use App\Models\Verification;

/**
 * LeaderboardService calculates scores and rankings for participants based on
 * verified progress entries and step submissions.
 */
class LeaderboardService extends Model
{
    /**
     * @var array
     */
    protected array $config;
    public function __construct(array $config)
    {
        $this->config = $config;
        parent::__construct($config);
    }
    /**
     * Generate the leaderboard as an array of participants with total score and
     * total steps. Sorted by score desc then steps desc.
     *
     * @return array
     */
    public function getLeaderboard(): array
    {
        $participantModel = new Participant($this->config);
        $verificationModel = new Verification($this->config);
        // Use ActivityEntry instead of StepEntry to sum steps across daily activity logs
        $activityModel = new \App\Models\ActivityEntry($this->config);

        // Fetch all participants
        $stmt = $this->db->query('SELECT id, full_name FROM participants WHERE status = \'active\'');
        $participants = $stmt->fetchAll();
        // Get score sums
        $scoreMap = $verificationModel->sumScoresByParticipant();
        // Get total calories burned per participant from activity_entries (steps removed)
        $stmtBurn = $this->db->query('SELECT participant_id, COALESCE(SUM(calories_burned),0) AS total_burned FROM activity_entries GROUP BY participant_id');
        $burnRows = $stmtBurn->fetchAll();
        $burnMap = [];
        foreach ($burnRows as $row) {
            $burnMap[(int)$row['participant_id']] = (float)$row['total_burned'];
        }
        // Build leaderboard array
        $leaderboard = [];
        foreach ($participants as $p) {
            $pid = (int)$p['id'];
            $leaderboard[] = [
                'participant_id' => $pid,
                'full_name'      => $p['full_name'],
                'total_score'    => $scoreMap[$pid] ?? 0,
                'total_burned'   => $burnMap[$pid] ?? 0,
            ];
        }
        // Sort by score desc, then steps desc
        usort($leaderboard, function ($a, $b) {
            // Sort primarily by total_score (descending), then by total_burned (descending)
            if ($a['total_score'] === $b['total_score']) {
                return $b['total_burned'] <=> $a['total_burned'];
            }
            return $b['total_score'] <=> $a['total_score'];
        });
        return $leaderboard;
    }
}