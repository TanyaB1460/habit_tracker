<?php

declare(strict_types=1);

use App\Controllers\HabitController;
use App\Controllers\HomeController;
use App\Controllers\StatsController;
use App\Core\Middleware\LoggerMiddleware;
use App\Core\Router;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

//Здесь начинается инициализация приложения: подключается автозагрузка классов.
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$appDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);

ini_set('display_errors', $appDebug ? '1' : '0');
error_reporting(E_ALL);

$logFile = dirname(__DIR__) . '/runtime/logs/app.log';

if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler($logFile, Level::Debug));

//Здесь frontend-контроллер загружает конфигурацию окружения
$psr17Factory = new Psr17Factory();
$requestCreator = new ServerRequestCreator(
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory
);

$request = $requestCreator->fromGlobals();

//Здесь из суперглобальных массивов формируется объект HTTP‑запроса по PSR‑7. Это и есть инициализация данных запроса.
$router = new Router();
$router->register([
    HomeController::class,
    HabitController::class,
    StatsController::class,
]);

$handler = new class ($router) implements RequestHandlerInterface {
    public function __construct(
        private Router $router
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->router->dispatch($request);
    }
};

try {
    if ($appDebug) {
        $loggerMiddleware = new LoggerMiddleware($logger);
        $response = $loggerMiddleware->process($request, $handler);
    } else {
        $response = $handler->handle($request);
    }
} catch (\Throwable $e) {
    $logger->error('Unhandled exception', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    if ($appDebug) {
        $content = '<!DOCTYPE html>'
            . '<html lang="ru"><head>'
            . '<meta charset="UTF-8"><title>Application error</title></head><body>';
        $content .= '<h1>Application error</h1>';
        $content .= '<p><strong>Message:</strong> '
            . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            . '</p>';
        $content .= '<p><strong>File:</strong> '
            . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8')
            . '</p>';
        $content .= '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
        $content .= '<pre>'
            . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8')
            . '</pre>';
        $content .= '</body></html>';

        $response = new \Nyholm\Psr7\Response(
            500,
            ['Content-Type' => 'text/html; charset=UTF-8'],
            $content
        );
    } else {
        $response = $router->renderView(
            dirname(__DIR__) . '/views/errors/500.php',
            500
        );
    }
}

http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header($name . ': ' . $value, false);
    }
}

echo (string) $response->getBody();
