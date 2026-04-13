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

if ($_ENV['APP_DEBUG'] === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

$logFile = dirname(__DIR__) . '/runtime/logs/app.log';
if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler($logFile, Level::Debug));

set_exception_handler(function (\Throwable $e) use ($logger) {
    $logger->critical($e->getMessage(), [
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    require dirname(__DIR__) . '/views/errors/500.php';
    exit;
});

$router = new Router();

$router->add('GET',  '/',              HomeController::class,  'index');
$router->add('POST', '/toggle',        HomeController::class,  'toggle');

$router->add('GET',  '/habits',        HabitController::class, 'index');
$router->add('POST', '/habits/create', HabitController::class, 'create');
$router->add('GET',  '/habits/edit',   HabitController::class, 'edit');
$router->add('POST', '/habits/update', HabitController::class, 'update');
$router->add('POST', '/habits/delete', HabitController::class, 'delete');

$router->add('GET',  '/stats',         StatsController::class, 'index');

$router->dispatch();
