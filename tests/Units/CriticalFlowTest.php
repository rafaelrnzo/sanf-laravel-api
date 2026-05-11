<?php

namespace Tests\Units;

use TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Firebase\JWT\JWT;
use Dompdf\Dompdf;
use Kreait\Firebase\Factory as FirebaseFactory;
use Yajra\DataTables\DataTables;

class CriticalFlowTest extends TestCase
{
    public function testApplicationBoots()
    {
        $this->assertNotNull(app());
        $this->assertEquals('testing', env('APP_ENV'));
    }

    public function testAuthAndJwt()
    {
        // Auth container
        $this->assertNotNull(app('auth'));

        // JWT encode/decode
        $payload = [
            'sub' => 1,
            'name' => 'Unit Test',
            'iat' => time(),
        ];

        $secret = 'unit-test-secret';

        $token = JWT::encode($payload, $secret, 'HS256');

        $this->assertNotEmpty($token);

        $decoded = JWT::decode($token, $secret, ['HS256']);

        $this->assertEquals(1, $decoded->sub);
    }

    public function testRedisCacheQueue()
    {
        // Redis
        $redis = Redis::connection();
        $this->assertNotNull($redis);

        // Cache
        Cache::put('health-check', 'ok', 10);

        $this->assertEquals(
            'ok',
            Cache::get('health-check')
        );

        // Queue
        $queue = app('queue');

        $this->assertNotNull($queue);
    }

    public function testStorageAndPdf()
    {
        // Local storage
        $disk = Storage::disk('local');

        $disk->put('unit-test.txt', 'storage-ok');

        $this->assertEquals(
            'storage-ok',
            $disk->get('unit-test.txt')
        );

        // DomPDF
        $pdf = new Dompdf();

        $pdf->loadHtml('<h1>PDF OK</h1>');

        $pdf->render();

        $output = $pdf->output();

        $this->assertNotEmpty($output);
    }

    public function testFirebaseFactory()
    {
        $factory = new FirebaseFactory();

        $this->assertNotNull($factory);
    }

    public function testDatatables()
    {
        $datatables = app('datatables');

        $this->assertNotNull($datatables);

        $data = collect([
            [
                'id' => 1,
                'name' => 'Ikhsan'
            ]
        ]);

        $json = $datatables
            ->collection($data)
            ->toJson();

        $this->assertStringContainsString(
            'Ikhsan',
            $json->getContent()
        );
    }

    public function testHealthEndpoint()
    {
        $this->get('/');

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'success',
            'code',
            'message',
            'data'
        ]);
    }
}