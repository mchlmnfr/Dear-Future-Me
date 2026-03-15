<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Participant;
use App\Models\Admin;

/**
 * Handles authentication for both participants and administrators. Provides
 * login forms and processes, and forces participants to change the
 * default temporary password on first login.
 */
class AuthController extends Controller
{
    /**
     * Display the login form. This is a unified login for both admins and
     * participants. If already authenticated, redirect based on role.
     */
    public function index(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $role = $_SESSION['role'] ?? '';
            if ($role === 'admin') {
                $this->redirect('admin/dashboard');
                return;
            }
            if ($role === 'participant') {
                $this->redirect('participant/dashboard');
                return;
            }
        }
        $this->render('auth/login');
    }

    /**
     * Process login for both admins and participants. Determines user role
     * based on where the credentials match.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginId = trim($_POST['login_id'] ?? '');
            $password = $_POST['password'] ?? '';
            $adminModel = new Admin($this->config);
            $participantModel = new Participant($this->config);
            // Attempt admin authentication first
            $admin = $adminModel->authenticate($loginId, $password);
            if ($admin) {
                $_SESSION['user_id']   = $admin['id'];
                $_SESSION['user_name'] = $admin['full_name'] ?? $admin['email'];
                $_SESSION['role']      = 'admin';
                $this->redirect('admin/dashboard');
                return;
            }
            // If no admin found, attempt participant authentication
            $participant = $participantModel->authenticate($loginId, $password);
            if ($participant) {
                $_SESSION['user_id']    = $participant['id'];
                $_SESSION['user_name']  = $participant['full_name'];
                $_SESSION['role']       = 'participant';
                $_SESSION['department_id'] = $participant['department_id'];
                // Force password change if needed
                if ((int)$participant['must_change_password'] === 1) {
                    $this->redirect('auth/changePassword');
                    return;
                }
                $this->redirect('participant/dashboard');
                return;
            }
            $error = 'Invalid credentials. Please try again.';
            $this->render('auth/login', ['error' => $error]);
        } else {
            $this->render('auth/login');
        }
    }

    /**
     * Display and process the password change form for participants who
     * logged in using the temporary password. After a successful password
     * change, redirect to the dashboard.
     */
    public function changePassword(): void
    {
        // Ensure a participant is logged in. Use unified session keys.
        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'participant') {
            $this->redirect('auth/index');
            return;
        }
        $participantModel = new Participant($this->config);
        $participantId = (int)$_SESSION['user_id'];
        // Handle form submission.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword     = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            // Validate current password and new password rules.
            $errors = [];
            $participantData = $participantModel->findById($participantId);
            if (!$participantData || !password_verify($currentPassword, $participantData['password_hash'])) {
                $errors[] = 'Current password is incorrect.';
            }
            if (strlen($newPassword) < 8) {
                $errors[] = 'New password must be at least 8 characters long.';
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New password and confirm password do not match.';
            }
            if (empty($errors)) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $participantModel->updatePassword($participantId, $hash, false);
                // Clear flag in session and redirect to dashboard.
                $this->redirect('participant/dashboard');
                return;
            }
            // Render form with errors.
            $this->render('participant/change_password', [
                'errors' => $errors,
            ]);
            return;
        }
        // Show form.
        $this->render('participant/change_password');
    }

    /**
     * Logout the current user (participant or admin) and destroy the session.
     */
    public function logout(): void
    {
        session_destroy();
        $this->redirect('auth/index');
    }
}