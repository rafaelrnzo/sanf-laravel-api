<?php

namespace Tests\Units\StandbyFinancing;

use Illuminate\Validation\ValidationException;
use Sanf\Api\Modules\StandbyFinancing\Services\SbfSptGeneratorService;

class SbfSptGeneratorServiceTest extends \TestCase
{
    public function testRenderProducesPdfBytes(): void
    {
        $pdf = (new SbfSptGeneratorService())->render($this->payload());

        $this->assertNotEmpty($pdf);
        $this->assertStringStartsWith('%PDF-', $pdf);
    }

    public function testFileNameIsDeterministicAndUrlSafe(): void
    {
        $service = new SbfSptGeneratorService();

        $name = $service->fileName($this->payload());

        $this->assertSame($name, $service->fileName($this->payload()));
        $this->assertStringStartsWith('SPT__', $name);
        $this->assertStringEndsWith('__2026-06-29__Generated.pdf', $name);
        $this->assertNotRegExp('/[+\/=]/', $name);
    }

    public function testPreparedContentFlattensInvoicesAndSumsTotals(): void
    {
        $content = $this->prepareContent($this->payload());

        $this->assertCount(2, $content['invoice_list']);
        $this->assertSame('INV-001', $content['invoice_list'][0]['nomor']);
        $this->assertSame(150000000, $content['total_po_amount']);
        $this->assertSame('id', $content['letter_date']->locale);
    }

    public function testDisbursementDefaultsToSupplierTotalAmountWhenOmitted(): void
    {
        $payload = $this->payload();
        unset($payload['total_disbursement']);
        $payload['supplier'][0]['total_amount'] = 120000000;

        $content = $this->prepareContent($payload);

        $this->assertSame(120000000, $content['total_disbursement']);
    }

    public function testExplicitDisbursementIsHonored(): void
    {
        $payload = $this->payload();
        $payload['total_disbursement'] = 90000000;

        $content = $this->prepareContent($payload);

        $this->assertSame(90000000, $content['total_disbursement']);
    }

    public function testRejectsDisbursementExceedingDocumentTotal(): void
    {
        $payload = $this->payload();
        $payload['total_disbursement'] = 999999999;

        $this->expectException(ValidationException::class);

        (new SbfSptGeneratorService())->render($payload);
    }

    public function testRejectsEmptyInvoiceList(): void
    {
        $payload = $this->payload();
        $payload['supplier'] = [['total_amount' => 0, 'invoice_list' => []]];

        $this->expectException(ValidationException::class);

        (new SbfSptGeneratorService())->render($payload);
    }

    private function prepareContent(array $payload): array
    {
        $method = new \ReflectionMethod(SbfSptGeneratorService::class, 'prepareTemplateContent');
        $method->setAccessible(true);

        return $method->invoke(new SbfSptGeneratorService(), $payload);
    }

    private function payload(array $override = []): array
    {
        return array_replace([
            'cust_id' => 'CUST-1',
            'no_plafond' => 'PLF-SBF-0001',
            'letter_number' => '001/SPT/VI/2026',
            'letter_date' => '2026-06-29',
            'signer_name' => 'Budi Santoso',
            'signer_role' => 'Direktur Utama',
            'total_disbursement' => 150000000,
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
            'supplier' => [[
                'total_amount' => 150000000,
                'invoice_list' => [
                    ['nomor_invoice' => 'INV-001', 'tanggal_invoice' => '2026-06-01', 'amount' => 100000000],
                    ['nomor_invoice' => 'INV-002', 'tanggal_invoice' => '2026-06-10', 'amount' => 50000000],
                ],
            ]],
        ], $override);
    }
}
