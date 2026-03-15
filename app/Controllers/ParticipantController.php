<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Participant controller. Provides dashboard and links to various features
 * for participants.
 */
class ParticipantController extends Controller
{
    /**
     * Participant dashboard. Displays a welcome message and links to actions.
     */
    public function dashboard(): void
    {
        $this->requireParticipant();
        // Update theme based on query parameter if provided
        $this->handleTheme();

        $participantId = (int) ($_SESSION['user_id'] ?? 0);
        $participantName = $_SESSION['user_name'] ?? 'Participant';

        // Load the participant's goal. If no goal exists, redirect to goal submission page.
        $goalModel = new \App\Models\Goal($this->config);
        $participantGoal = $goalModel->findByParticipant($participantId);
        if (!$participantGoal) {
            // Force participants to set a goal before accessing dashboard
            header('Location: /goal/submit');
            exit;
        }

        // Fetch metrics for the participant dashboard
        $db = \App\Services\Database::getInstance($this->config)->getConnection();

        // Total approved verification score for this participant
        $stmtScore = $db->prepare(
            "SELECT COALESCE(SUM(score),0) FROM verifications\n" .
            "WHERE verification_status = 'approved' AND participant_id = :pid"
        );
        $stmtScore->execute(['pid' => $participantId]);
        $totalScore = (int) $stmtScore->fetchColumn();
        
        // Aggregate daily activity metrics (water and calories burned)
        $activityModel = new \App\Models\ActivityEntry($this->config);
        $metrics = $activityModel->sumMetrics($participantId);

        // Total monthly progress entries submitted
        $stmtProg = $db->prepare(
            "SELECT COUNT(*) FROM progress_entries WHERE participant_id = :pid"
        );
        $stmtProg->execute(['pid' => $participantId]);
        $progressCount = (int) $stmtProg->fetchColumn();

        // Count verified progress entries for completion percentage (out of 3 months)
        $stmtVerified = $db->prepare(
            "SELECT COUNT(*) FROM progress_entries WHERE participant_id = :pid AND status = 'verified'"
        );
        $stmtVerified->execute(['pid' => $participantId]);
        $verifiedCount = (int) $stmtVerified->fetchColumn();

        $completionPercent = min(100, ($verifiedCount / 3) * 100);

        // Determine participant rank and leaderboard using LeaderboardService
        $leaderboardService = new \App\Services\LeaderboardService($this->config);
        $leaderboard = $leaderboardService->getLeaderboard();
        $rank = null;
        foreach ($leaderboard as $index => $row) {
            if ((int)$row['participant_id'] === $participantId) {
                $rank = $index + 1;
                break;
            }
        }
        // Top 5 leaderboard entries for display
        $topLeaderboard = array_slice($leaderboard, 0, 5);

        // Count number of activity logs
        $stmtActCount = $db->prepare(
            "SELECT COUNT(*) FROM activity_entries WHERE participant_id = :pid"
        );
        $stmtActCount->execute(['pid' => $participantId]);
        $activityCount = (int)$stmtActCount->fetchColumn();

        $stats = [
            'score'             => $totalScore,
            'progress'          => $progressCount,
            'rank'              => $rank,
            'completionPercent' => $completionPercent,
            'verifiedCount'     => $verifiedCount,
            // aggregated daily metrics
            'metrics'           => $metrics,
            'activityCount'     => $activityCount,
        ];

        // Recent activity: last 5 daily entries.  We include the type of activity as well as
        // steps, water and calories burned.  Meal logs are not shown in this summary but
        // remain stored in the database.
        $stmtActivity = $db->prepare(
            "SELECT entry_date, activity_type, water_liters, calories_burned\n" .
            "FROM activity_entries\n" .
            "WHERE participant_id = :pid\n" .
            "ORDER BY entry_date DESC LIMIT 5"
        );
        $stmtActivity->execute(['pid' => $participantId]);
        $activities = $stmtActivity->fetchAll();

        // Fetch last weigh-in and entire weigh‑in history for charting
        $weighModel = new \App\Models\WeighIn($this->config);
        $lastWeighIn = $weighModel->getLast($participantId);
        $weightInfo  = null;
        if ($lastWeighIn) {
            $weightInfo = [
                'date'           => $lastWeighIn['weigh_date'],
                'weight'         => $lastWeighIn['weight'],
                'height'         => $lastWeighIn['height'],
                'bmi'            => $lastWeighIn['bmi'],
                'classification' => $lastWeighIn['classification'],
            ];
        }

        // Retrieve full weigh‑in history for this participant (sorted ascending by date)
        $stmtHistory = $db->prepare(
            "SELECT weigh_date, weight, bmi FROM weigh_ins WHERE participant_id = :pid ORDER BY weigh_date ASC"
        );
        $stmtHistory->execute(['pid' => $participantId]);
        $weighHistory = $stmtHistory->fetchAll();

        // Calculate next weigh-in date (7 days after last weigh-in)
        $nextWeighDate = null;
        if ($lastWeighIn && !empty($lastWeighIn['weigh_date'])) {
            $nextTime = strtotime($lastWeighIn['weigh_date']) + (7 * 86400);
            $nextWeighDate = date('Y-m-d', $nextTime);
        }

        // Challenge progress check countdown: set a fixed end date 3 months from start; here we assume a constant or config.
        $progressCheckDate = $this->config['challenge_end_date'] ?? date('Y-m-d', strtotime('+90 days', strtotime(date('Y-m-01'))));
        $now  = time();
        $end  = strtotime($progressCheckDate);
        $timeDiff = $end > $now ? $end - $now : 0;
        // Compute days, hours and minutes remaining until the progress check date
        $remainingDays    = floor($timeDiff / 86400);
        $remainingHours   = floor(($timeDiff % 86400) / 3600);
        $remainingMinutes = floor(($timeDiff % 3600) / 60);

        $this->render('participant/dashboard', [
            'participantName'   => $participantName,
            'stats'             => $stats,
            'activities'        => $activities,
            'goal'              => $participantGoal,
            'weightInfo'        => $weightInfo,
            'nextWeighDate'     => $nextWeighDate,
            'remainingDays'     => $remainingDays,
            'remainingHours'    => $remainingHours,
            'remainingMinutes'  => $remainingMinutes,
            'topLeaderboard'    => $topLeaderboard,
            'weightHistory'     => $weighHistory,
        ]);
    }

    /**
     * Display a personalized workout and diet plan based on the participant's BMI classification.
     * Participants must have submitted a goal before viewing the plan.
     */
    public function plan(): void
    {
        // Ensure the user is logged in as a participant
        $this->requireParticipant();
        // Update theme if the `theme` query parameter is present
        $this->handleTheme();

        $participantId = (int) ($_SESSION['user_id'] ?? 0);

        // Load the participant's goal; if none exists, redirect to goal submission
        $goalModel = new \App\Models\Goal($this->config);
        $goal      = $goalModel->findByParticipant($participantId);
        if (!$goal) {
            header('Location: /goal/submit');
            exit;
        }

        // Extract BMI classification from the baseline_value string.
        $baseline       = $goal['baseline_value'] ?? '';
        $classification = null;
        if ($baseline) {
            // The baseline string contains "BMI: X (Classification)" if provided by the form
            if (preg_match('/BMI:\\s*[^\\(]+\\(([^\\)]+)\\)/i', $baseline, $match)) {
                $classification = trim($match[1]);
            }
        }

        // Default to "Normal" if classification cannot be determined
        if (!$classification) {
            $classification = 'Normal';
        }

        // Build suggested workout and diet plans based on classification
        $suggestions = $this->buildPlanSuggestions($classification);

        // Render the plan page
        $this->render('participant/plan', [
            'classification' => $classification,
            'goal'           => $goal,
            'suggestions'    => $suggestions,
        ]);
    }

    /**
     * Build workout and diet suggestions based on BMI classification.
     *
     * @param string $classification One of Underweight, Normal, Overweight, Obese
     * @return array
     */
    private function buildPlanSuggestions(string $classification): array
    {
        // Define multiple workouts with difficulty levels (Easy, Intermediate, Hard) for each classification.
        // Each workout includes steps with minimal equipment and estimated calories burned.
        $workouts = [
            'Underweight' => [
                [
                    'name'        => 'Gentle Yoga Flow',
                    'duration'    => '15 min',
                    'calories'    => '50‑80 kcal',
                    'level'       => 'Easy',
                    'equipment'   => ['Mat'],
                    'description' => 'A light yoga session to improve flexibility and promote relaxation.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Seated breathing and gentle neck stretches for 2 minutes.'],
                        ['title' => 'Cat‑Cow', 'instruction' => 'Alternate between arching and rounding your back for 1 minute.'],
                        ['title' => 'Child’s Pose', 'instruction' => 'Hold for 1 minute, breathing deeply.'],
                        ['title' => 'Downward Dog', 'instruction' => 'Hold for 30 seconds, stretching calves and hamstrings.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Lie on your back and hug knees to chest for 1 minute.'],
                    ],
                ],
                [
                    'name'        => 'Bodyweight Strength Circuit',
                    'duration'    => '20 min',
                    'calories'    => '100‑150 kcal',
                    'level'       => 'Intermediate',
                    'equipment'   => ['Mat'],
                    'description' => 'A circuit to build muscle and healthy weight.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Jog in place or march for 2 minutes.'],
                        ['title' => 'Push‑ups (knees)', 'instruction' => 'Perform 10 knee push‑ups, focusing on form.'],
                        ['title' => 'Bodyweight Squats', 'instruction' => 'Do 15 squats, keeping chest up.'],
                        ['title' => 'Plank Hold', 'instruction' => 'Hold a plank on forearms for 20-30 seconds.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Stretch major muscle groups for 3 minutes.'],
                    ],
                ],
                [
                    'name'        => 'High‑Intensity Interval Blast',
                    'duration'    => '25 min',
                    'calories'    => '200‑300 kcal',
                    'level'       => 'Hard',
                    'equipment'   => ['Mat'],
                    'description' => 'A vigorous HIIT session to increase endurance and muscle tone.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Jumping jacks and dynamic stretches for 3 minutes.'],
                        ['title' => 'Mountain Climbers', 'instruction' => 'Perform for 45 seconds.'],
                        ['title' => 'Burpees', 'instruction' => 'Perform 12 burpees at a steady pace.'],
                        ['title' => 'Lunges', 'instruction' => 'Do 12 lunges per leg.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Stretch legs, arms and back for 4 minutes.'],
                    ],
                ],
            ],
            'Normal' => [
                [
                    'name'        => 'Moderate Cardio Mix',
                    'duration'    => '20 min',
                    'calories'    => '150‑200 kcal',
                    'level'       => 'Easy',
                    'equipment'   => ['None'],
                    'description' => 'A mix of light cardio exercises to maintain fitness.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'March in place and swing arms for 2 minutes.'],
                        ['title' => 'High Knees', 'instruction' => 'Run in place lifting knees high for 1 minute.'],
                        ['title' => 'Jumping Jacks', 'instruction' => 'Do 20 jumping jacks.'],
                        ['title' => 'Side Steps', 'instruction' => 'Step side to side with arm reaches for 1 minute.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Slow march and stretch for 3 minutes.'],
                    ],
                ],
                [
                    'name'        => 'Full‑Body HIIT',
                    'duration'    => '25 min',
                    'calories'    => '200‑300 kcal',
                    'level'       => 'Intermediate',
                    'equipment'   => ['Mat'],
                    'description' => 'A high‑intensity interval workout to maintain fitness and improve endurance.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Dynamic stretches for 3 minutes.'],
                        ['title' => 'Burpees', 'instruction' => 'Perform 12 burpees.'],
                        ['title' => 'Lunges', 'instruction' => 'Do 12 lunges per leg.'],
                        ['title' => 'Plank‑to‑Push‑up', 'instruction' => 'Alternate between plank and push‑up for 45 seconds.'],
                        ['title' => 'High Knees', 'instruction' => 'Run in place with high knees for 1 minute.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Stretch legs, arms and back for 3 minutes.'],
                    ],
                ],
                [
                    'name'        => 'Advanced Interval Training',
                    'duration'    => '30 min',
                    'calories'    => '300‑400 kcal',
                    'level'       => 'Hard',
                    'equipment'   => ['Mat'],
                    'description' => 'A challenging interval workout for those with a solid fitness base.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Jump rope or dynamic stretches for 4 minutes.'],
                        ['title' => 'Squat Jumps', 'instruction' => 'Perform 15 squat jumps.'],
                        ['title' => 'Push‑ups', 'instruction' => 'Do 20 push‑ups.'],
                        ['title' => 'Plank Jacks', 'instruction' => 'Perform 1 minute of plank jacks.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Stretch thoroughly for 5 minutes.'],
                    ],
                ],
            ],
            'Overweight' => [
                [
                    'name'        => 'Chair Cardio Routine',
                    'duration'    => '20 min',
                    'calories'    => '100‑150 kcal',
                    'level'       => 'Easy',
                    'equipment'   => ['Chair'],
                    'description' => 'A seated routine combining light cardio and strength.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Seated marching with arm swings for 2 minutes.'],
                        ['title' => 'Seated Punches', 'instruction' => 'Punch arms forward for 1 minute.'],
                        ['title' => 'Knee Lifts', 'instruction' => 'Lift knees alternately for 1 minute.'],
                        ['title' => 'Arm Circles', 'instruction' => 'Perform small circles with extended arms for 1 minute.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Deep breathing and stretches for 3 minutes.'],
                    ],
                ],
                [
                    'name'        => 'Low‑Impact Cardio & Strength',
                    'duration'    => '30 min',
                    'calories'    => '250‑350 kcal',
                    'level'       => 'Intermediate',
                    'equipment'   => ['Chair', 'Mat'],
                    'description' => 'A joint‑friendly routine to burn calories and build strength.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'March in place and shoulder rolls for 3 minutes.'],
                        ['title' => 'Chair Squats', 'instruction' => 'Sit down and stand up 15 times.'],
                        ['title' => 'Wall Push‑ups', 'instruction' => 'Do 10 wall push‑ups.'],
                        ['title' => 'Seated Knee Raises', 'instruction' => 'Lift one knee at a time for 1 minute.'],
                        ['title' => 'Standing Side Leg Lifts', 'instruction' => 'Do 12 side leg lifts per side using the chair for balance.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Stretch calves, hamstrings and arms for 3 minutes.'],
                    ],
                ],
                [
                    'name'        => 'Interval Walking & Strength',
                    'duration'    => '30 min',
                    'calories'    => '300‑400 kcal',
                    'level'       => 'Hard',
                    'equipment'   => ['Comfortable shoes'],
                    'description' => 'A brisk interval walking program combined with bodyweight strength.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Walk slowly for 5 minutes.'],
                        ['title' => 'Brisk Walk', 'instruction' => 'Walk at a brisk pace for 5 minutes.'],
                        ['title' => 'Bodyweight Squats', 'instruction' => 'Perform 15 squats.'],
                        ['title' => 'Repeat', 'instruction' => 'Repeat brisk walk and squats sequence 3 times.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Walk slowly for 5 minutes and stretch.'],
                    ],
                ],
            ],
            'Obese' => [
                [
                    'name'        => 'Seated Mobility Routine',
                    'duration'    => '15 min',
                    'calories'    => '50‑80 kcal',
                    'level'       => 'Easy',
                    'equipment'   => ['Chair'],
                    'description' => 'Gentle seated exercises to improve mobility and circulation.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Seated marching for 2 minutes.'],
                        ['title' => 'Arm Raises', 'instruction' => 'Raise arms overhead for 1 minute.'],
                        ['title' => 'Heel Lifts', 'instruction' => 'Lift heels off the floor alternately for 1 minute.'],
                        ['title' => 'Seated Torso Twists', 'instruction' => 'Twist torso gently left and right, 10 times each side.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Deep breathing and neck stretches for 2 minutes.'],
                    ],
                ],
                [
                    'name'        => 'Gentle Walking & Mobility',
                    'duration'    => '30‑45 min',
                    'calories'    => '150‑250 kcal',
                    'level'       => 'Intermediate',
                    'equipment'   => ['Comfortable shoes'],
                    'description' => 'A beginner‑friendly plan focusing on steady walking and mobility.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Walk slowly for 5 minutes.'],
                        ['title' => 'Brisk Walk', 'instruction' => 'Increase your pace and walk for 20-30 minutes.'],
                        ['title' => 'Seated Arm Circles', 'instruction' => 'Sit and perform arm circles for 1 minute.'],
                        ['title' => 'Seated Torso Twists', 'instruction' => 'Twist torso gently 10 times each side.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Walk slowly for 5 minutes and stretch legs and back.'],
                    ],
                ],
                [
                    'name'        => 'Extended Walking Intervals',
                    'duration'    => '45‑60 min',
                    'calories'    => '300‑450 kcal',
                    'level'       => 'Hard',
                    'equipment'   => ['Comfortable shoes'],
                    'description' => 'A longer walking session with intervals to improve stamina.',
                    'steps'       => [
                        ['title' => 'Warm‑up', 'instruction' => 'Walk slowly for 5 minutes.'],
                        ['title' => 'Interval Walk', 'instruction' => 'Alternate 5 minutes brisk walking with 2 minutes easy walking for 40 minutes.'],
                        ['title' => 'Cool‑down', 'instruction' => 'Walk slowly for 5 minutes and stretch.'],
                    ],
                ],
            ],
        ];

        // Define diet plans including fasting options for each classification.
        $dietPlans = [
            'Underweight' => [
                [
                    'name'        => 'Balanced Calorie Surplus',
                    'description' => 'Consume nutrient‑dense foods like nuts, avocados, lean proteins, whole grains and fruits. Aim for small, frequent meals throughout the day.',
                ],
                [
                    'name'        => 'Protein‑Rich Plan',
                    'description' => 'Include lean meats, dairy, legumes and healthy fats to support muscle growth and weight gain.',
                ],
                [
                    'name'        => 'Fasting Not Recommended',
                    'description' => 'Fasting plans are not advised for underweight individuals. Focus on consistent, balanced meals.',
                ],
            ],
            'Normal' => [
                [
                    'name'        => 'Maintenance Meal Plan',
                    'description' => 'Focus on balanced portions of lean proteins, whole grains, fruits and vegetables. Avoid excessive sugars and processed foods.',
                ],
                [
                    'name'        => 'Intermittent Fasting 14:10',
                    'description' => 'Fast for 14 hours overnight, with a 10‑hour eating window. Maintain balanced meals during eating periods.',
                ],
                [
                    'name'        => 'Plant‑Forward Diet',
                    'description' => 'Emphasize plant‑based foods while including lean meats and fish sparingly. Keeps calories moderate and nutrient dense.',
                ],
            ],
            'Overweight' => [
                [
                    'name'        => 'Calorie‑Controlled Diet',
                    'description' => 'Reduce portion sizes and choose high‑fiber foods like vegetables, fruits, beans and whole grains. Limit sugary drinks and fried foods.',
                ],
                [
                    'name'        => 'Low‑Carb Plan',
                    'description' => 'Reduce intake of refined carbohydrates and sugars; prioritize vegetables, proteins and healthy fats.',
                ],
                [
                    'name'        => 'Intermittent Fasting 16:8',
                    'description' => 'Fast for 16 hours with an 8‑hour eating window. Ensure meals are nutrient dense and portion controlled.',
                ],
            ],
            'Obese' => [
                [
                    'name'        => 'Low‑Impact Nutrition Plan',
                    'description' => 'Emphasize nutrient‑dense, low‑calorie foods such as leafy greens, lean proteins and legumes. Consider smaller, more frequent meals to stabilize blood sugar.',
                ],
                [
                    'name'        => 'Very‑Low‑Calorie Plan (Medical Supervision)',
                    'description' => 'Adhere to a strict low‑calorie diet under medical supervision. Focus on proteins, vegetables and essential nutrients.',
                ],
                [
                    'name'        => 'Intermittent Fasting 18:6',
                    'description' => 'Fast for 18 hours with a 6‑hour eating window. Include adequate hydration and nutrient dense meals within the window.',
                ],
            ],
        ];

        // Select workouts and diets for the classification. If classification unknown, default to Normal.
        $workoutSuggestions = $workouts[$classification] ?? $workouts['Normal'];
        $dietSuggestions    = $dietPlans[$classification] ?? $dietPlans['Normal'];

        return [
            'workouts' => $workoutSuggestions,
            'diets'    => $dietSuggestions,
        ];
    }
}