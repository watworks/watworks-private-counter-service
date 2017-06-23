<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$app = new \Silex\Application();

// TODO: define core services

// api routes
$app->get("/_health", function() {
    return new JsonResponse(['status' => 'ok']);
});

$app->get("/hello", function() {
    return new JsonResponse(['hello' => 'world']);
});

return $app;
