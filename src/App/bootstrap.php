<?php

declare(strict_types=1);

require __DIR__ . "/../../vendor/autoload.php";

use Framework\App;
use App\Config\Paths;
use Dotenv\Dotenv;

set_exception_handler(["Framework\\ErrorHandler", "handleException"]);
set_error_handler(["Framework\\ErrorHandler", "handleError"]);
register_shutdown_function(function () {
    $error = error_get_last();

    if ($error !== null && $error['type'] === E_ERROR) {
        ErrorHandler::handleException(new \Error($error['message'], $error['type']));
    }
});

use Framework\ErrorHandler;

use function App\Config\{registerRoutes, registerMiddleware};

header("Content-type: application/json; charset=UTF-8");


$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

$app = new App(Paths::SOURCE . "App/container-definitions.php");

registerRoutes($app);
registerMiddleware($app);

return $app;