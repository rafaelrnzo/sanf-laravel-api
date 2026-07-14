<?php

namespace Tests\Units\StandbyFinancing;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery;
use Sanf\Api\Modules\StandbyFinancing\Controllers\SbfTransactionController;
use Sanf\Api\Modules\StandbyFinancing\Services\SbfSptGeneratorService;
use Sanf\Core\Modules\LlmOcr\Services\GeminiOcrService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SbfSptControllerTest extends \TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testSptPreviewReturnsInlinePdf(): void
    {
        $response = (new SbfTransactionController())->sptPreview(
            Request::create('/sbf/spt/preview', 'POST', $this->payload()),
            new SbfSptGeneratorService()
        );

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('inline', (string) $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $body = ob_get_clean();
        $this->assertStringStartsWith('%PDF-', $body);
    }

    public function testSptGenerateUploadsToMinioAndReturnsWebCompatibleReference(): void
    {
        Storage::fake('minio_post');

        $response = (new SbfTransactionController())->sptGenerate(
            Request::create('/sbf/spt/generate', 'POST', $this->payload()),
            new SbfSptGeneratorService()
        );

        $this->assertSame(200, $response->getStatusCode());

        $body = $response->getData(true);
        $data = $body['data'];

        $this->assertSame('success', $body['status']);
        $this->assertStringStartsWith('SBF-SPT-', $data['xid']);
        $this->assertStringStartsWith('uploads/sbf/spt/' . date('Y/m') . '/', $data['path']);
        $this->assertStringEndsWith('.pdf', $data['file_name']);
        $this->assertStringEndsWith($data['file_name'], $data['path']);
        $this->assertStringStartsWith('SPT__', $data['origin_name']);

        Storage::disk('minio_post')->assertExists($data['path']);
    }

    public function testSptGenerateRejectsDisbursementExceedingDocumentTotal(): void
    {
        Storage::fake('minio_post');

        $payload = $this->payload();
        $payload['total_disbursement'] = 999999999;

        $this->expectException(ValidationException::class);

        (new SbfTransactionController())->sptGenerate(
            Request::create('/sbf/spt/generate', 'POST', $payload),
            new SbfSptGeneratorService()
        );
    }

    public function testSptPreviewRejectsMissingSupplier(): void
    {
        $payload = $this->payload();
        unset($payload['supplier']);

        $this->expectException(ValidationException::class);

        (new SbfTransactionController())->sptPreview(
            Request::create('/sbf/spt/preview', 'POST', $payload),
            new SbfSptGeneratorService()
        );
    }

    public function testSptPreviewRejectsMissingLetterFields(): void
    {
        $payload = $this->payload();
        unset($payload['letter_number'], $payload['signer_name']);

        $this->expectException(ValidationException::class);

        (new SbfTransactionController())->sptPreview(
            Request::create('/sbf/spt/preview', 'POST', $payload),
            new SbfSptGeneratorService()
        );
    }

    public function testSptOcrScanUploadsAndReturnsExtractedFields(): void
    {
        Storage::fake('minio_post');

        $ocr = Mockery::mock(GeminiOcrService::class);
        $ocr->shouldReceive('extract')->once()->andReturn([
            'letter_number' => '001/SPT/VI/2026',
            'letter_date' => '2026-06-29',
        ]);

        $request = Request::create('/sbf/spt/ocr-scan', 'POST', ['cust_id' => 'CUST-1'], [], [
            'file' => UploadedFile::fake()->create('spt-signed.pdf', 120, 'application/pdf'),
        ]);

        $response = (new SbfTransactionController())->sptOcrScan($request, $ocr);

        $this->assertSame(200, $response->getStatusCode());

        $body = $response->getData(true);

        $this->assertSame('success', $body['status']);
        $this->assertTrue($body['ocr']['success']);
        $this->assertSame('001/SPT/VI/2026', $body['data']['letter_number']);
        $this->assertSame('2026-06-29', $body['data']['letter_date']);
        $this->assertStringStartsWith('SBF-SPT-', $body['document']['xid']);
        $this->assertStringStartsWith('uploads/sbf/spt/' . date('Y/m') . '/', $body['document']['path']);
        $this->assertSame('spt-signed.pdf', $body['document']['origin_name']);

        Storage::disk('minio_post')->assertExists($body['document']['path']);
    }

    public function testSptOcrScanFallsBackToInvoiceFieldNames(): void
    {
        Storage::fake('minio_post');

        $ocr = Mockery::mock(GeminiOcrService::class);
        $ocr->shouldReceive('extract')->once()->andReturn([
            'invoice_number' => 'SPT-2026-009',
            'invoice_date' => '2026-06-01',
        ]);

        $request = Request::create('/sbf/spt/ocr-scan', 'POST', ['cust_id' => 'CUST-1'], [], [
            'file' => UploadedFile::fake()->create('spt.pdf', 50, 'application/pdf'),
        ]);

        $body = (new SbfTransactionController())->sptOcrScan($request, $ocr)->getData(true);

        $this->assertSame('SPT-2026-009', $body['data']['letter_number']);
        $this->assertSame('2026-06-01', $body['data']['letter_date']);
    }

    public function testSptOcrScanStillReturnsDocumentWhenOcrFails(): void
    {
        Storage::fake('minio_post');

        $ocr = Mockery::mock(GeminiOcrService::class);
        $ocr->shouldReceive('extract')->once()->andThrow(new \RuntimeException('Gemini down'));

        $request = Request::create('/sbf/spt/ocr-scan', 'POST', ['cust_id' => 'CUST-1'], [], [
            'file' => UploadedFile::fake()->create('spt.pdf', 50, 'application/pdf'),
        ]);

        $response = (new SbfTransactionController())->sptOcrScan($request, $ocr);
        $body = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $body['status']);
        $this->assertFalse($body['ocr']['success']);
        $this->assertStringContainsString('Gemini down', $body['ocr']['message']);
        $this->assertSame('', $body['data']['letter_number']);
        Storage::disk('minio_post')->assertExists($body['document']['path']);
    }

    public function testSptOcrScanRejectsMissingFile(): void
    {
        Storage::fake('minio_post');

        $ocr = Mockery::mock(GeminiOcrService::class);
        $ocr->shouldNotReceive('extract');

        $request = Request::create('/sbf/spt/ocr-scan', 'POST', ['cust_id' => 'CUST-1']);

        $this->expectException(ValidationException::class);

        (new SbfTransactionController())->sptOcrScan($request, $ocr);
    }

    private function payload(): array
    {
        return [
            'cust_id' => 'CUST-1',
            'no_plafond' => 'PLF-SBF-0001',
            'letter_number' => '001/SPT/VI/2026',
            'letter_date' => '2026-06-29',
            'signer_name' => 'Budi Santoso',
            'signer_role' => 'Direktur Utama',
            'customer' => [
                'identity_name' => 'PT Maju Jaya Sentosa',
                'address' => 'Jl. Sudirman No. 1, Jakarta',
                'phone' => '0211234567',
                'email' => 'finance@majujaya.co.id',
            ],
            'bank_account' => [
                'bank_owner' => 'PT Maju Jaya Sentosa',
                'bank_provider' => 'Bank Central Asia',
                'bank_account_number' => '1234567890',
            ],
            'supplier' => [
                [
                    'total_amount' => 150000000,
                    'invoice_list' => [
                        ['nomor_invoice' => 'INV-001', 'tanggal_invoice' => '2026-06-01', 'amount' => 100000000],
                        ['nomor_invoice' => 'INV-002', 'tanggal_invoice' => '2026-06-10', 'amount' => 50000000],
                    ],
                ]
            ],
        ];
    }
}
