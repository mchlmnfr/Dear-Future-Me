<?php
/**
 * Application front controller.
 *
 * All requests are routed through this script. It bootstraps the autoloader,
 * starts the session and instantiates the core application which will
 * dispatch the request to the appropriate controller and action.
 */

declare(strict_types=1);

// Ensure errors are displayed during development. You may disable this in
// production by setting display_errors=0 in php.ini.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Start the session early so that controllers can use $_SESSION. Vercel
// stateless execution means sessions should be stored in the database or
// another persistent store. For a simple demo we use PHP's default which
// stores session data in temporary files. In production you should replace
// this with a database‑backed session handler.
session_start();

// Define root constants for easy path resolution.
define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');

// Autoloader: automatically require classes from the App, Controllers,
// Models, Core and Services namespaces. This eliminates the need for
// manual require statements throughout the code base.
spl_autoload_register(function ($class) {
    // Convert namespace separators to directory separators.
    $file = ROOT . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load configuration. This returns an array of config values. Controllers
// access it via the Database service or directly when needed.
$config = require ROOT . '/config.php';

// Instantiate and run the application. All routing logic lives in
// \App\Core\App, which determines the controller and action based on
// the requested URI.
$app = new App\Core\App($config);
$app->run();
