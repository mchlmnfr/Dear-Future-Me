<?php
namespace App\Core;

use Exception;

/**
 * The central application class responsible for parsing incoming requests and
 * dispatching them to the appropriate controller and action. It also holds
 * configuration values that can be passed to other services.
 */
class App
{
    /**
     * @var array Configuration values loaded from config.php
     */
    private array $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Execute the application. Parse the URL, instantiate the controller and
     * call the requested method with parameters.
     */
    public function run(): void
    {
        $url = $this->parseUrl();

        // Determine controller name. Default is AuthController.
        $controllerName = 'Auth';
        if (!empty($url[0])) {
            $controllerName = ucfirst($url[0]);
            array_shift($url);
        }
        $controllerClass = 'App\\Controllers\\' . $controllerName . 'Controller';

        // Determine action name. Default is index.
        $action = 'index';
        if (!empty($url[0])) {
            $action = $url[0];
            array_shift($url);
        }

        // Parameters are any remaining segments in the URL.
        $params = $url ?? [];

        // Instantiate the controller. Throw an exception if it doesn't exist.
        if (!class_exists($controllerClass)) {
            http_response_code(404);
            echo "Controller {$controllerClass} not found.";
            return;
        }
        $controller = new $controllerClass($this->config);

        // Call the action if it exists on the controller. Otherwise show 404.
        if (!method_exists($controller, $action)) {
            http_response_code(404);
            echo "Action {$action} not found in controller {$controllerClass}.";
            return;
        }

        // Call the action with the parameters.
        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Parse the request URI into an array of path segments.
     *
     * @return array
     */
    private function parseUrl(): array
    {
        // Determine the request path without query string
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Determine the base path where index.php is running. This is
        // necessary when the application is installed in a subfolder such as
        // /bmip/public. We derive it from the script name (e.g. /bmip/public/index.php).
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = rtrim(str_replace('\\', '/', $scriptDir), '/');

        // Remove the base path from the request path
        if ($basePath && $basePath !== '/' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        // Trim leading and trailing slashes
        $path = trim($path, '/');

        return $path === '' ? [] : explode('/', $path);
    }
}