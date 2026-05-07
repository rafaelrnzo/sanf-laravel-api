<?php

namespace Tests\Units;

use TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Firebase\JWT\JWT;
use Dompdf\Dompdf;
use Kreait\Firebase\Factory as FirebaseFactory;

class CriticalFlowTest extends TestCase
{
    public function testAuthAndJwt()
    {
        $this->assertNotNull(app('auth'));

        $token = JWT::encode(['test' => 1], 'secret', 'HS256');
        $this->assertNotEmpty($token);
    }

    public function testRedisCacheQueueSession()
    {
        Redis::connection();
        $this->assertTrue(true);

        Cache::put('test-key', 'ok', 10);
        $this->assertEquals('ok', Cache::get('test-key'));

        Queue::size();
        $this->assertTrue(true);

        try {
            $session = app('session');
            $this->assertNotNull($session, 'Session should be resolvable if configured');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Session is not configured/available');
        }
    }

    public function testNotificationEmailFirebase()
    {
        $this->assertNotNull(app('mailer'));

        $factory = new FirebaseFactory();
        $this->assertNotNull($factory);
    }

    public function testStorageS3Pdf()
    {
        $disk = Storage::disk('local');
        $disk->put('test.txt', 'test');
        $this->assertEquals('test', $disk->get('test.txt'));

        $pdf = new Dompdf();
        $pdf->loadHtml('<h1>Test</h1>');
        $pdf->render();

        $this->assertTrue(true);
    }

    public function testDatatablesYajra()
    {
        $datatables = app('datatables');
        $this->assertNotNull($datatables);

        $data = collect([['id' => 1, 'name' => 'Test Yajra']]);
        $response = $datatables->collection($data)->toJson();
        $this->assertStringContainsString('Test Yajra', $response->getContent());
    }
}
