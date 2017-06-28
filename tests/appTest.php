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

    private function decodeJsonResponse(Response $res)
    {
        $data = $res->getContent();
        return json_decode($data, true);
    }

    private function clearStorage()
    {
        $app = $this->loadApp();
        $app['redis']->flushdb();
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
    public function testCreateGetAndDeleteCounter()
    {
        $this->clearStorage();

        // get a counter: shouldn't exist yet
        $res = $this->callApi("GET", "/counters/foo");
        $this->assertSame(404, $res->getStatusCode());

        // create one, second call should be a no-op
        $res = $this->callApi("PUT", "/counters/foo");
        $this->assertSame(201, $res->getStatusCode());
        $res = $this->callApi("PUT", "/counters/foo");
        $this->assertSame(200, $res->getStatusCode());

        // now retrieve it
        $res = $this->callApi("GET", "/counters/foo");
        $this->assertSame(200, $res->getStatusCode());
        
        // delete it
        $res = $this->callApi("DELETE", "/counters/foo");
        $this->assertSame(200, $res->getStatusCode());

        // get it again - shouldn't exist
        $res = $this->callApi("GET", "/counters/foo");
        $this->assertSame(404, $res->getStatusCode());
    }

    public function testModifyCounter()
    {
        $this->clearStorage();

        // create a counter - set it to 100
        $res = $this->callApi("PUT", "/counters/foo");
        $this->assertSame(201, $res->getStatusCode());
        $res = $this->callApi("PUT", "/counters/foo/100");
        $this->assertSame(200, $res->getStatusCode());
        $d = $this->decodeJsonResponse($res);
        $this->assertSame(0, $d['prevValue']);
        $this->assertSame(100, $d['value']);

        // increment it
        $this->callApi("PUT", "/counters/foo/increment/20");
        $d = $this->decodeJsonResponse($res);
        $this->assertSame(100, $d['prevValue']);
        $this->assertSame(120, $d['value']);

        // decrement(t)
        $this->callApi("PUT", "/counters/foo/decrement/50");
        $d = $this->decodeJsonResponse($res);
        $this->assertSame(120, $d['prevValue']);
        $this->assertSame(70, $d['value']);
    }
}
