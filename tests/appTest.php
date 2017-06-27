<?php

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private function loadApp()
    {
        return require __DIR__.'/../src/app.php';
    }
    
    private function callApi($method, $path, $headers, $body)
    {
        $app = $this->loadApp();
        $req = new Request();
        return $app->run($req);
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
        $this->assertSame('foo', $bar);
    }

    public function testHealthRoute()
    {
        $this->markTestIncomplete();
    }

    public function testHelloRoute()
    {
        $this->markTestIncomplete();
    }

    public function testErrorRoute()
    {
        $this->markTestIncomplete();
    }
}
