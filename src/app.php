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

// define app services & helpers
$app['redis'] = function ($app) {
    return new \Predis\Client($app['redis.url']);
};

$app['getKeyOr404'] = $app->protect(function ($key) use ($app) {
    $r = $app['redis'];
    if (!$r->exists($key)) {
        throw new HttpException(404, "not found");
    }
    return (double) $r->get($key);
});

$app['validateOr422'] = $app->protect(function ($num) {
    if (!is_numeric($num)) {
        throw new HttpException(422, "number is not a valid numeric format");
    }
    return (double) $num;
});

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

$app->get("/swagger.json", function () use ($app) {
    $str = file_get_contents(__DIR__.'/../docs/swagger.json');
    return new Response($str, 200, ['Content-Type' => 'application/json']);
});

$app->get("/counters/{name}", function ($name) use ($app) {
    $val = $app['getKeyOr404']($name);
    return $app->json(['value' => $val], 200);
});

$app->put("/counters/{name}", function ($name) use ($app) {
    $r = $app['redis'];
    $exists = $r->exists($name);
    $code = ($exists) ? 200 : 201;
    if (!$exists) {
        $r->set($name, (double) 0);
    }
    return $app->json(['value' => (double) 0], $code);
});

$app->delete("/counters/{name}", function ($name) use ($app) {
    $v = $app['getKeyOr404']($name);
    $app['redis']->del($name);
    return new Response(200);
});

$app->put("/counters/{name}/increment/{num}", function ($name, $num) use ($app) {
    $v = $app['getKeyOr404']($name);
    $num = $app['validateOr422']($num);
    $app['redis']->incrby($name, $num);
    return $app->json(['value' => $v + $num, 'prevValue' => $v], 200);
});

$app->put("/counters/{name}/decrement/{num}", function ($name, $num) use ($app) {
    $v = $app['getKeyOr404']($name);
    $num = $app['validateOr422']($num);
    $app['redis']->decrby($name, $num);
    return $app->json(['value' => $v - $num, 'prevValue' => $v], 200);
});

$app->put("/counters/{name}/{num}", function ($name, $num) use ($app) {
    $v = $app['getKeyOr404']($name);
    $num = $app['validateOr422']($num);
    $app['redis']->set($name, $num);
    return $app->json(['value' => $num, 'prevValue' => $v], 200);
});

// globally enable CORS
$app['cors-enabled']($app);

return $app;
