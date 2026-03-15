<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Admin;
use App\Models\Participant;

/**
 * Handles administrator actions including login, dashboard display and
 * management of participants. Only authenticated admins can access most
 * methods. See requireAdmin() in the base controller for protection.
 */
class AdminController extends Controller
{
    /**
     * Display the admin login form or process login requests.
     */
    public function login(): void
    {
        // If already logged in as admin using unified session, redirect.
        if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
            $this->redirect('admin/dashboard');
            return;
        }
        // Render the unified login form. Actual login processing happens in AuthController::login().
        $this->render('auth/login');
    }

    /**
     * Display the admin dashboard. Shows basic statistics. Requires
     * authentication.
     */
    public function dashboard(): void
    {
        $this->requireAdmin();
        // Build admin dashboard statistics. Pull counts from the database
        // using the Database service so controllers remain thin and views
        // remain simple.
        $db = \App\Services\Database::getInstance($this->config)->getConnection();

        // Total active participants
        $totalParticipants = (int) $db->query("SELECT COUNT(*) FROM participants WHERE status = 'active'")
            ->fetchColumn();

        // Total goals submitted
        $totalGoals = (int) $db->query("SELECT COUNT(*) FROM goals")
            ->fetchColumn();

        // Pending progress entries awaiting verification
        $pendingVerifications = (int) $db->query("SELECT COUNT(*) FROM progress_entries WHERE status = 'pending'")
            ->fetchColumn();

        // Total activity entries (count daily logs)
        $totalActivityEntries = (int) $db->query("SELECT COUNT(*) FROM activity_entries")
            ->fetchColumn();

        // Participants per department (left join to include departments with
        // no participants)
        $stmtDept = $db->query(
            "SELECT d.id, d.department_name, COUNT(p.id) AS participant_count\n" .
            "FROM departments d\n" .
            "LEFT JOIN participants p ON p.department_id = d.id\n" .
            "GROUP BY d.id, d.department_name\n" .
            "ORDER BY d.department_name"
        );
        $departments = $stmtDept->fetchAll();

        // Recent progress entries (latest 5)
        $stmtRecent = $db->query(
            "SELECT p.full_name, pe.month_number, pe.status, pe.created_at\n" .
            "FROM progress_entries pe\n" .
            "JOIN participants p ON pe.participant_id = p.id\n" .
            "ORDER BY pe.created_at DESC\n" .
            "LIMIT 5"
        );
        $recentProgress = $stmtRecent->fetchAll();

        $stats = [
            'participants'         => $totalParticipants,
            'goals'                => $totalGoals,
            'pendingVerifications' => $pendingVerifications,
            'activityEntries'      => $totalActivityEntries,
        ];

        $this->render('admin/dashboard', [
            'adminName'      => $_SESSION['user_name'] ?? 'Admin',
            'stats'          => $stats,
            'departments'    => $departments,
            'recentProgress' => $recentProgress,
        ]);
    }

    /**
     * Display and process the participant creation form. Requires admin
     * authentication. Admin enters full name, department and employee ID.
     * A temporary password is set and the participant must change it on first
     * login.
     */
    public function createParticipant(): void
    {
        $this->requireAdmin();
        $participantModel = new Participant($this->config);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName     = trim($_POST['full_name'] ?? '');
            $departmentId = (int)($_POST['department_id'] ?? 0);
            $employeeId   = trim($_POST['employee_id'] ?? '');
            // Validation
            if ($fullName === '') {
                $errors[] = 'Full name is required.';
            }
            if ($employeeId === '') {
                $errors[] = 'Employee ID is required.';
            }
            if ($departmentId <= 0) {
                $errors[] = 'Please select a department.';
            }
            // Check duplicate employee ID
            if ($participantModel->findByEmployeeId($employeeId)) {
                $errors[] = 'Employee ID already exists.';
            }
            if (empty($errors)) {
                $tempPassword = 'HandF12345';
                $hash         = password_hash($tempPassword, PASSWORD_DEFAULT);
                $participantId = $participantModel->create($fullName, $departmentId, $employeeId, $hash, true);
                if ($participantId) {
                    $success = 'Participant created successfully. Temporary password is HandF12345.';
                    $this->render('admin/create_participant', [
                        'success' => $success,
                    ]);
                    return;
                } else {
                    $errors[] = 'Failed to create participant.';
                }
            }
        }
        // Departments list could be loaded from a model, but here we provide a static list.
        $departments = $this->getDepartments();
        $this->render('admin/create_participant', [
            'errors'      => $errors,
            'departments' => $departments,
        ]);
    }

    /**
     * Declare winners based on the leaderboard. Picks top 3 participants and
     * records them in the winners table. Only admins can access this.
     */
    public function declareWinners(): void
    {
        $this->requireAdmin();
        // Use LeaderboardService to get ranking
        $service = new \App\Services\LeaderboardService($this->config);
        $ranking = $service->getLeaderboard();
        // Pick top 3 participants
        $top = array_slice($ranking, 0, 3);
        $winnersData = [];
        $prize = 800; // Php 800 each
        $position = 1;
        foreach ($top as $row) {
            $winnersData[] = [
                'participant_id' => $row['participant_id'],
                'rank_position'  => $position,
                'prize_amount'   => $prize,
            ];
            $position++;
        }
        $winnerModel = new \App\Models\Winner($this->config);
        $success = $winnerModel->declareWinners(1, $winnersData, (int)$_SESSION['user_id']);
        if ($success) {
            // Redirect to winners page
            $this->redirect('winner/index');
        } else {
            // Display error message using a dedicated view
            $this->render('admin/declare_winners', [
                'error'   => 'Failed to declare winners.',
                'ranking' => $ranking,
            ]);
        }
    }

    /**
     * Logout the admin.
     */
    public function logout(): void
    {
        session_destroy();
        $this->redirect('auth/index');
    }

    /**
     * Example method to fetch departments. In a real application this would
     * query the departments table. Here we return a simple hard‑coded list.
     *
     * @return array<int,array<string,mixed>>
     */
    private function getDepartments(): array
    {
        return [
            ['id' => 1, 'name' => 'HR'],
            ['id' => 2, 'name' => 'Finance'],
            ['id' => 3, 'name' => 'IT'],
            ['id' => 4, 'name' => 'Marketing'],
        ];
    }
}