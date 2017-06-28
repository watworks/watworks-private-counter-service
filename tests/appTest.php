<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class Test extends TestCase
{
    private function loadApp()
    {
        return require __DIR__.'/../src/app.php';
    }
    
    private function callApi($method, $path, $headers = [], $body = null)
    {
        $app = $this->loadApp();
        $req = Request::create($path, $method, [], [], [], [], $body);
        foreach ($headers as $key => $val) {
            $req->headers->set($key, $val);
        }
        return $app->handle($req);
    }

    public function testLoadApp()
    {
        $app = $this->loadApp();
        $this->assertTrue($app instanceof \Silex\Application);
    }

    public function testRedisService()
    {
        $app = $this->loadApp();
        $r = $app['redis'];
        $r->set('foo', 'bar');
        $bar = $r->get('foo');
        $this->assertSame('bar', $bar);
    }

    public function testDocsRoute()
    {
        $res = $this->callApi("GET", "/swagger.json");
        $this->assertSame(200, $res->getStatusCode());
    }

    // TODO: actually test the counter routes
    public function testApiRoutes()
    {
        $this->markTestIncomplete();
    }
}
