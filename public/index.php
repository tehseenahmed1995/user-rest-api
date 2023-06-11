<?php

use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\config\Db;

require_once __DIR__ . '/../vendor/autoload.php';

//temp fix to handle CORS on localhost fo react application
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:X-Request-With');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

require_once __DIR__ . '/../src/routes/api.php';

$app->run();