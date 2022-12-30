<?php
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;



require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/settings.php';

/** @var $app \Slim\App */



/** @var $app App */
$app->group('/api', function (RouteCollectorProxy $group){
   $group->post('/resend', \App\other\smsReader::class);
});



try {
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
        throw new HttpNotFoundException($request);
    });
    $app->run();
}catch (HttpNotFoundException $e){
    include __DIR__ . '/tg.php';
}catch (Throwable $e){
    file_put_contents(ERRORLOG, $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo $e->getMessage();
}

