<?php

// Место для подключения общих middleware
use loandbeholdru\slimcontrol\middlewares\allowOrigin;
use Slim\App;
use loandbeholdru\slimcontrol\middlewares\mysql;


/** @var $app App */
$app->add(new allowOrigin('Cabinet'));


$app->addRoutingMiddleware();
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});