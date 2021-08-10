<?php

namespace Tests\TestCases\Unit;

use NbsPhp\ApiWrapper\Api\Endpoint;
use NbsPhp\ApiWrapper\Api\Route;
use NbsPhp\ApiWrapper\ApiWrapper;
use PHPUnit\Framework\TestCase;

class ApiWrapperTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testLoad()
    {
        ApiWrapper::load(__DIR__ . '/../../Assets/routes/test.php');

        $route = Route::find('file.test');
        $this->assertInstanceOf(Endpoint::class, $route);
    }
}
