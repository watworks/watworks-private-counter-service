<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

// some checks, for testing
if (!getenv("FOO")) {
    throw new \RuntimeException("FOO env var required");
}

// setup app and error handling
$app = new Application();
$app->error(function(\Exception $e, $code) {
    echo sprintf("App error: %s", $e->getMessage());
});

// TODO: define core services

// index route to have *something* to look at
$app->get("/", function() {
    $str = <<<EOT
    <html>
        <body>
            <h1>Hi.</h1>
            <p>Test the example routes below.<p>
            <ul>
                <li><a href="/_health">/_health</a></li>
                <li><a href="/hello">/hello</a></li>
                <li><a href="/error">/error</a></li>
            </ul>
        <body>
    <html>
EOT;
    return new Response($str);
});

// api routes
$app->get("/_health", function() {
    return new JsonResponse(['status' => 'ok']);
});

$app->get("/hello", function() {
    return new JsonResponse(['hello' => getenv("FOO")]);
});

$app->get("/error", function () {
    throw new \RuntimeException("testing an error");
});

return $app;
