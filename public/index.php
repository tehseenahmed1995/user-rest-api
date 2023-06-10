<?php

use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\config\Db;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

require_once __DIR__ . '/../src/routes/api.php';

$app->run();