<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

// setup app and error handling
$app = new Application();
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider(), ["cors.allowOrigin" => "*"]);
$app->error(function (\Exception $e, $code) {
    echo sprintf("App error: %s", $e->getMessage());
});


// set config from environment
$app['redis.url'] = getenv('REDIS_URL');

// define app services

$app['redis'] = function ($app) {
    return new \Predis\Client($app['redis.url']);
};

// index route to have *something* to look at
// TODO: make this route only conditionally available if this is for local development
$app->get("/", function () use ($app) {
    return $app->redirect("http://localhost:8003/");
});

// api routes
$app->get("/_health", function () use ($app) {
    $r = $app['redis'];

    return new JsonResponse(['status' => 'ok']);
});

$app->get("/_error", function () use ($app) {
    throw new \RuntimeException("testing an error");
});

$app->get("/swagger.json", function() use ($app) {
    $str = file_get_contents(__DIR__.'/../docs/swagger.json');
    return new Response($str, 200, ['Content-Type' => 'application/json']);
});

$notImplemented = function() {
    throw new HttpException(501, "not implemented");
};

$app->get("/counters/{name}", $notImplemented);
$app->put("/counters/{name}", $notImplemented);
$app->delete("/counters/{name}", $notImplemented);
$app->put("/counters/{name}/increment/{num}", $notImplemented);
$app->put("/counters/{name}/decrement/{num}", $notImplemented);
$app->put("/counters/{name}/{num}", $notImplemented);

// globally enable CORS
$app['cors-enabled']($app);

return $app;
