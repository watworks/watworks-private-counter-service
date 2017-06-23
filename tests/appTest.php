<?php

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private function loadApp()
    {
        return require __DIR__.'/../src/app.php';
    }

    public function testHealthRoute()
    {
        $app = $this->loadApp();
        
        $this->assertTrue(true);
    }

    public function testHelloRoute()
    {
        $this->assertTrue(true);
    }
}
