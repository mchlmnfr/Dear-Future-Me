<?php
namespace App\Core;

/**
 * Base controller class. All application controllers should extend this
 * class. It provides helper methods for rendering views and performing
 * redirects, and exposes the configuration array loaded on bootstrap.
 */
abstract class Controller
{
    /**
     * @var array Application configuration
     */
    protected array $config;

    /**
     * Constructor. Stores the configuration for use in subclasses.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Inspect the query string for a `theme` parameter and store it in the
     * session. This allows pages to toggle between dark and light modes
     * persistently per user. Valid values are 'dark' and 'light'.
     *
     * Controllers should call this at the beginning of actions that render
     * pages so the view can read the chosen theme from the session.
     */
    protected function handleTheme(): void
    {
        // Only update the theme if explicitly provided in the query string
        if (isset($_GET['theme'])) {
            $value = strtolower($_GET['theme']);
            // Accept only 'dark' or 'light' values
            if ($value === 'dark' || $value === 'light') {
                $_SESSION['theme'] = $value;
            }
        }
    }

    /**
     * Render a view. The view files live under app/Views. You can render
     * nested views by specifying a path like 'participant/dashboard'.
     *
     * @param string $view Relative path to the view file (without .php)
     * @param array  $data Data made available to the view via variables
     */
    protected function render(string $view, array $data = []): void
    {
        // Extract variables to local scope so they can be accessed directly in the view.
        extract($data);

        $viewFile = ROOT . '/app/Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View {$view} not found.";
        }
    }

    /**
     * Perform an HTTP redirect to another path. If a base URL is configured
     * (for example when deploying on Vercel), it will be prepended to the
     * destination. Redirects are immediate and stop script execution.
     *
     * @param string $path
     */
    protected function redirect(string $path): void
    {
        $base = $this->config['base_url'] ?? '';
        header('Location: ' . rtrim($base, '/') . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Require a participant to be logged in. If the participant is not
     * authenticated they will be redirected to the login page. Use this in
     * participant controllers to protect routes.
     */
    protected function requireParticipant(): void
    {
        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'participant') {
            $this->redirect('auth/index');
        }
    }

    /**
     * Require an administrator to be logged in. If the admin is not
     * authenticated they will be redirected to the admin login page. Use this
     * in admin controllers to protect routes.
     */
    protected function requireAdmin(): void
    {
        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            $this->redirect('auth/index');
        }
    }
}