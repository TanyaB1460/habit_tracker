<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\HabitController;
use App\Controllers\HomeController;
use App\Controllers\StatsController;
use App\Core\Router;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$appDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

ini_set('display_errors', $appDebug ? '1' : '0');
error_reporting(E_ALL);

$logFile = dirname(__DIR__) . '/runtime/logs/app.log';

if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler($logFile, Level::Debug));

set_exception_handler(function (\Throwable $e) use ($logger, $appDebug) {
    $logger->error('Unhandled exception', [
        'exception' => $e,
    ]);

    http_response_code(500);

    if ($appDebug) {
        echo '<h1>Application error</h1>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        require dirname(__DIR__) . '/views/errors/500.php';
    }

    exit;
});

$router = new Router();

$router->register([
    HomeController::class,
    HabitController::class,
    StatsController::class,
]);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);